<?php
declare(strict_types=1);

namespace AppBundle\Endpoint;


class StatsEndpoint extends AbstractSecuredApiEndpoint implements GetMethodInterface {
  /**
   * Validate incoming JSON against scheme
   *
   * @return void
   */
  function validate() {
    // Nothing to validate in this endpoint
  }

  /**
   * Handle request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   * @throws \Exception
   */
  function handle() {
    // TODO: Implement handle() method.
  }

}