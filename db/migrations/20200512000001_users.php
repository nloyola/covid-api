<?php

use Phinx\Migration\AbstractMigration;

class Users extends AbstractMigration {

  public function change() {
    $this->table('users')
      ->addColumn('version',  'biginteger', [ 'default' => 0 ])
      ->addColumn('slug',     'string', [ 'limit' => 100 ])
      ->addColumn('name',     'string', [ 'null' => true ])
      ->addColumn('email',    'string')
      ->addColumn('password', 'string')
      ->addTimestamps()
      ->create();

    // add default user so that admin can login
    $this->table('users')->insert([
      [
        'version'  => 0,
        'slug'     => 'administrator',
        'name'     => 'Administrator',
        'email'    => 'biobank2@gmail.com',
        'password' => '$2y$10$0iXhXUb1.ph3WkVzVmj6HuKK2PzpRIWKWyhY5eUIS/hX0H7kV3PAG'
      ],
      [
        'version'  => 0,
        'slug'     => 'nelson-loyola',
        'name'     => 'Nelson Loyola',
        'email'    => 'nloyola@gmail.com',
        'password' => '$2y$10$0iXhXUb1.ph3WkVzVmj6HuKK2PzpRIWKWyhY5eUIS/hX0H7kV3PAG'
      ],
    ])->save();

    $this->table('password_resets')
      ->addColumn('email',      'string')
      ->addColumn('selector',   'string', [ 'limit' => 100 ])
      ->addColumn('token',      'text', [ 'null' => false ])
      ->addColumn('reset_time', 'datetime', [ 'null' => true])
      ->create();

  }
}
