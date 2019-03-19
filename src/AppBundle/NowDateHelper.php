<?php
declare(strict_types=1);

namespace AppBundle;


class NowDateHelper
{
  /** @var \DateTimeZone */
  static private $utc = null;

  /**
   * @return \DateTimeZone
   */
  public static function getUtc()
  {
    return self::$utc ? self::$utc : self::$utc = new \DateTimeZone('UTC');
  }

  /**
   * @return \DateTime
   */
  public static function getNow() {
    return new \DateTime('now', self::getUtc());
  }
}