<?php

namespace App\Services;

use App\Auth\JwtAuth;
use App\Utils\LoggerFuncs;
use App\Dto\UserDto;
use App\Factories\LoggerFactory;
use App\Models\PasswordReset;
use App\Models\User;
use App\Validation\ValidationError;
use App\Validation\ValidationResult;
use App\Validation\Validator;
use Illuminate\Support\Arr;
use Respect\Validation\Validator as v;

class UserService
{

  use LoggerFuncs;
  use HasUpdateValidator;

  public function __construct(
    LoggerFactory $loggerFactory,
    Validator $validator,
    JwtAuth $jwtAuth,
    MailService $mailService
  ) {
    $this->logger = $loggerFactory->createInstance('UserService');
    $this->validator = $validator;
    $this->jwtAuth = $jwtAuth;
    $this->mailService = $mailService;

    $this->propertyValidators = [
      'version'        => v::number()->intVal()->min(0),
      'slug'           => v::noWhitespace()->notEmpty()->slug()->userSlugExists(),
      'name'           => v::notEmpty(),
      'email'          => v::noWhitespace()->notEmpty()->email(),
      'emailAvailable' => v::noWhitespace()->notEmpty()->email()->emailAvailable(),
      'password'       => v::notEmpty()->length(8, null),
      'property'       => v::notEmpty()->in(['name', 'email', 'password' ]),
    ];

    $this->addValidationRules = [
      'name'  => $this->propertyValidators['name'],
      'email' => $this->propertyValidators['emailAvailable'],
      'password' => $this->propertyValidators['password']
    ];

    $this->loginValidationRules = [
      'email'    => $this->propertyValidators['email'],
      'password' => $this->propertyValidators['password']
    ];

    $this->updatePropertyValidators = [
      'slug'            => $this->propertyValidators['slug'],
      'expectedVersion' => $this->propertyValidators['version'],
      'property'        => $this->propertyValidators['property'],
    ];

    $this->updatePropertyValidator = [
      'property' => $this->propertyValidators['property'],
    ];
  }

  /**
   * @return App\Validation\Validation;
   */
  public function user($slug)
  {
    $user = User::where('slug', $slug)->first();
    if (!isset($user)) {
      return new ValidationError('user type with slug not found: ${slug}');
    }
    return new ValidationResult(UserDto::create($user));
  }

  /**
   * @return App\Validation\Validation;
   */
  public function userById($id)
  {
    $user = User::find($id);
    if (!isset($user)) {
      return new ValidationError('user type with id not found: ${id}');
    }
    return new ValidationResult(UserDto::create($user, false));
  }

  /**
   * @return App\Validation\Validation;
   */
  public function users(array $options, int $paginatorPerPage)
  {
    return $this->usersRaw($options, $paginatorPerPage, function ($user) {
      return UserDto::create($user);
    });
  }

  /**
   * @return App\Validation\Validation;
   */
  public function addUser(array $values)
  {
    $validation = $this->validator->validate($values, $this->addValidationRules);
    if ($validation->failed()) {
      return $validation;
    }

    $user = new User([
      'version'  => 0,
      'slug'     => User::makeSlugFromName($values['name']),
      'name'     => $values['name'],
      'email'    => $values['email'],
      'password' => password_hash($values['password'], PASSWORD_DEFAULT)
    ]);
    $user->save();
    return new ValidationResult(new UserDto($user));
  }

  /**
   * @return App\Validation\Validation;
   */
  public function userLogin(array $values)
  {
    $validation = $this->validator->validate($values, $this->loginValidationRules);
    if ($validation->failed()) {
      return new ValidationError($validation->getErrors());
    }

    ['email' => $email, 'password' => $password] = $values;
    if (!$this->attempt($email, $password)) {
      return new ValidationError('unauthorized');
    }

    $user = User::where('email', $email)->first();
    if (!isset($user)) {
      return new ValidationError("user with email not found: ${email}");
    }

    $token = $this->jwtAuth->createJwt($user->id, $user->name);
    $lifetime = $this->jwtAuth->getLifetime();

    return new ValidationResult([
      'access_token' => $token,
      'token_type' => 'Bearer',
      'expires_in' => $lifetime
    ]);
  }

  public function userLogout()
  {
    $this->auth->removeToken();
  }

  /**
   * @return App\Validation\Validation;
   */
  public function forgotPassword(string $email,
                                 string $operatingSystem,
                                 string $browserName,
                                 string $actionUrlBase)
  {
    $validation = $this->validator->validate([ 'email' => $email ],
                                             [ 'email' => $this->propertyValidators['email'] ]);
    if ($validation->failed()) {
      return new ValidationError($validation->getErrors());
    }

    $user = User::where('email', $email)->first();
    if (!isset($user)) {
      return new ValidationError("user with email not found: ${email}");
    }

    PasswordReset::where('email', $email)->delete();
    $token = random_bytes(32);
    $tokenHash = password_hash($token, PASSWORD_DEFAULT);

    $pwdReset = new PasswordReset([
      'email'      => $email,
      'selector'   => bin2hex(random_bytes(8)),
      'token'      => $tokenHash,
      'reset_time' => new \DateTime()
    ]);
    $pwdReset->save();

    $actionUrl = $actionUrlBase . "?selector={$pwdReset->selector}&validator=" . bin2hex($token);
    $this->sendPasswordResetEmail($email, $user->name, $operatingSystem, $browserName, $actionUrl);
    return new ValidationResult(UserDto::create($user));
  }

  /**
   * @return App\Validation\Validation;
   */
  public function resetPassword(array $values) {
    $validationRules = [
      'selector' => v::notEmpty()->xdigit(),
      'validator' => v::notEmpty()->xdigit(),
      'password' => $this->propertyValidators['password']
    ];
    $validation = $this->validator->validate($values, $validationRules);
    if ($validation->failed()) {
      return $validation;
    }

    $time24hoursAgo = (new \DateTime())->modify('-24 hours');
    $passReset = PasswordReset::where('selector', $values['selector'])
      ->where('reset_time', '>=', $time24hoursAgo)
      ->first();
    if (!isset($passReset)) {
      return new ValidationError('password reset time exceeded');
    }

    $tokenBin = hex2bin($values['validator']);
    $tokenCheck = password_verify($tokenBin, $passReset->token);

    if ($tokenCheck !== true) {
      return new ValidationError('password reset validation failed');
    }

    $user = User::where('email', $passReset->email)->first();

    if (!isset($passReset)) {
      return new ValidationError('user for password reset not found');
    }

    $user->password = password_hash($values['password'], PASSWORD_DEFAULT);
    $user->save();
    $passReset->delete();
    $token = $this->auth->createToken($user);
    return new ValidationResult(['token' => $token, 'user' => UserDto::create($user)]);
  }

  /**
   * @return App\Validation\Validation;
   */
  public function updateUser(array $values)
  {
    $validation = $this->validateUpdatePropertyValues($values);
    if ($validation->failed()) {
      return $validation;
    }

    if ($values['property'] === 'password') {
      $values['currentPassword'] = $values['value']['currentPassword'];
      $values['newPassword'] = $values['value']['newPassword'];
      unset($values['value']);

      $validators = array_merge(
        $this->updatePropertyValidators,
        [
          'currentPassword' => $this->propertyValidators['password'],
          'newPassword' => $this->propertyValidators['password']
        ]
      );

    } else {
    // now validate the rest of the values in $values
      $validators = array_merge(
        $this->updatePropertyValidators,
        ['value' => $this->propertyValidators[$values['property']]]
      );
    }

    $validation = $this->validator->validate($values, $validators);
    if ($validation->failed()) {
      return $validation;
    }

    ['slug' => $slug, 'expectedVersion' => $expectedVersion, 'property' => $property, 'value' => $value] = $values;

    $user = User::where('slug', $slug)->first();
    if ($user === null) {
      throw new \Error('user is null');
    }

    if ($user->version != $expectedVersion) {
      return new ValidationError(
        ['expectedVersion' => [
          "user version mismatch: expected {$expectedVersion}, actual {$user->version}"
        ]]
      );
    }

    switch ($property) {
      case 'name':
        $user->slug = User::makeSlugFromName($value);
        $user->name = $value;
        break;

      case 'email':
        $user->email = $value;
        break;

      case 'password':
        if (!$this->auth->attempt($user->email, $values['currentPassword'])) {
          return new ValidationError('invalid password');
        }
        $user->password = password_hash($values['newPassword'], PASSWORD_DEFAULT);
        break;

      default:
        throw new \Error("update for property is invalid: ${property}");
    }

    $user->version = $user->version + 1;
    $user->save();
    return new ValidationResult(new UserDto($user));
  }

  private function usersRaw(array $options, int $paginatorPerPage, callable $dtoFunc)
  {
    $users = User::query();

    $name = $options['name'] ?? null;
    if ($name) {
      $users = $users->where('name', $name);
    }

    $email = $options['email'] ?? null;
    if ($email) {
      $users = $users->where('email', $email);
    }

    $users = $users->paginate($paginatorPerPage)->appends($options);
    return new ValidationResult(new PaginatedResult(
      $users->getCollection()->map($dtoFunc)->toArray(),
      Arr::except($users->toArray(), ['data'])
    ));
  }

  private function sendPasswordResetEmail(
    string $email,
    string $name,
    string $operatingSystem,
    string $browserName,
    string $actionUrl
  ) {
    if ($this->mode === 'testing') {
      return;
    }

    $recipients = [];
    if ($this->mode === 'prod') {
      $recipients = [
        $email,
        'biobank2@gmail.com',
        'nloyola@gmail.com'
      ];
    } else {
      $recipients = ['nloyola@gmail.com'];
    }


    $body = <<<PASSWORD_RESET_EMAIL_BODY_END
<h3>Hi {$name},</h3>

<p>
  You recently requested to reset your password at <b>COVID-19 Surveillance Collaboration</b> web site.
  Use the button below to reset it. This password reset is only valid for the
  next 24 hours.
</p>

<table cellpadding="0" cellspacing="0">
  <tr>
    <td align="center" style="border-radius: 2px;" bgcolor="#22BC66">
      <a href="{$actionUrl}" class="button button-green"
         target="_blank"
         style="padding: 8px 12px; border: 1px solid #22BC66;border-radius: 2px;font-family: Helvetica, Arial, sans-serif;font-size: 14px; color: #ffffff;text-decoration: none;font-weight:bold;display: inline-block;">
        Reset your password
      </a>
    </td>
  </tr>
</table>

<p>
  For security, this request was received from a {$operatingSystem} device using {$browserName}.
  If you did not request a password reset, please ignore this email or contact support if you have questions.
</p>

<p>
  Thanks<br>
  The COVID-19 Surveillance Collaboration
</p>

<p>
  If you’re having trouble with the button above, copy and paste the URL below into your web browser.
</p>

<p>{$actionUrl}</p>
PASSWORD_RESET_EMAIL_BODY_END;

    $v = $this->mailService->sendEmail(
      'biobank2@gmail.com',
      $recipients,
      "COVID-19 Surveillance Collaboration: Password Reset",
      $body
    );
    if ($v->failed()) {
      $this->logger->info('mailer: ' . print_r($v->errors(0), true));
    }
  }

  private function attempt($email, $password): bool {
    $user = User::where('email', $email)->first();
    if (!$user) {
      return false;
    }

    return password_verify($password, $user->password);
  }
}
