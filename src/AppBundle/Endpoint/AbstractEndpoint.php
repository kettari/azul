<?php
declare(strict_types=1);

namespace AppBundle\Endpoint;


use Symfony\Component\HttpFoundation\Request;

abstract class AbstractEndpoint {

  /**
   * @var Request $request
   */
  private $request;

  /**
   * @return AbstractEndpoint
   */
  abstract function beforeProcess();

  /**
   * Validate request
   *
   * @return AbstractEndpoint
   */
  abstract function validate();

  /**
   * Handle request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  abstract function handle();

  /**
   * @return AbstractEndpoint
   */
  abstract function afterSuccessfulProcess();

  /**
   * @return AbstractEndpoint
   */
  abstract function afterFailedProcess();

  /**
   * @return Request
   */
  public function getRequest(): Request {
    return $this->request;
  }

  /**
   * @param Request $request
   * @return AbstractEndpoint
   */
  public function setRequest(Request $request) {
    $this->request = $request;

    return $this;
  }

}