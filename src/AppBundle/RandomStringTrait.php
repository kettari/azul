<?php
declare(strict_types=1);

namespace AppBundle;


trait RandomStringTrait
{
  /**
   * Generates random string.
   *
   * @param int $length
   * @param string $characters
   * @return string
   */
  protected function generateRandomString($length = 3, $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
  {
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
  }
}