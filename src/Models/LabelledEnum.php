<?php

namespace App\Models;

abstract class LabelledEnum extends BasicEnum
{
  const Null = [ 'id' => null, 'label' => '' ];

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
    if ($value == '') {
      $value = null;
    }

    if (!self::isValid($value)) {
      throw new \Error(sprintf("Invalid enumeration \"%s\" for Enum %s", $value, get_class($this)));
    }

    if (is_array($value)) {
      $this->value = $value;
    } else {
      if ($value === null) {
        $this->value = self::Null;
      } else {
        foreach (self::validValues() as $el) {
          if ($el['id'] == $value) {
            $this->value = $el;
            return;
          }
        }
        throw new \Error(sprintf("Invalid enumeration \"%s\" for Enum %s", $value, get_class($this)));
      }
    }
  }

  /**
   * @return string
   */
  public function getLabel() {
    return $this->value['label'];
  }

  /**
   * @return string
   */
  public function __toString()
  {
    return $this->getLabel();
  }

  /**
   * Check if the set value on this enum is a valid value for the enum
   * @return boolean
   */
  public static function isValid($value)
  {
    if (is_array($value)) {
      if (in_array($value, static::validValues())) {
        return false;
      }
    }

    foreach (self::validValues() as $el) {
      if ($el['id'] == $value) {
        return true;
      }
    }
    return false;
  }

  /**
   * Get the legal values for this enum (excludes the "null" value).
   * @return array
   */
  public static function legalValues()
  {
    $result = [];
    foreach (parent::validValues() as $const) {
      if ($const['label'] !== '') {
        $result[] = $const;
      }
    }
    return $result;
  }

}
