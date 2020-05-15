<?php

namespace App\Models;

use App\Utils\Dump;

abstract class BasicEnum implements \JsonSerializable
{
  /**
   * @var string
   */
  protected $value;

  /**
   * @param mixed $value
   */
  public function __construct($value)
  {
    $this->setValue($value);
  }

  /**
   * @param mixed $value
   */
  public function setValue($value)
  {
    if (!static::isValid($value)) {
      throw new \Error(sprintf("Invalid enumeration \"%s\" for Enum %s", $value, get_class($this)));
    }
    $this->value = $value;
  }

  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }

  /**
   * Check if the set value on this enum is a valid value for the enum
   * @return boolean
   */
  public static function isValid($value)
  {
    return in_array($value, static::validValues());
  }

  /**
   * Get the valid values for this enum
   * Defaults to constants you define in your subclass
   * override to provide custom functionality
   * @return array
   */
  public static function validValues()
  {
    $r = new \ReflectionClass(get_called_class());
    return array_values($r->getConstants());
  }

  /**
   * @see JsonSerialize
   */
  public function jsonSerialize()
  {
    return $this->getValue();
  }

  /**
   * @return string
   */
  public function __toString()
  {
    return (string) $this->getValue();
  }

  public function keyString($key)
  {
    $declaredElems = $this->getConstList();
    if (array_key_exists($key, $declaredElems)) {
      return strtolower($key);
    }

    throw new \Error("key not a value in enum YesNo");
  }
}
