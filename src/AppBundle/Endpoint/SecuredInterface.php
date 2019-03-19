<?php
declare(strict_types=1);

namespace AppBundle\Endpoint;


interface SecuredInterface {

  /**
   * Authenticates request
   *
   * @return void
   */
  public function authenticate();

}