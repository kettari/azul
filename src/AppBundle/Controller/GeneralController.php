<?php
declare(strict_types=1);

namespace AppBundle\Controller;


use AppBundle\Endpoint\AbstractEndpoint;
use AppBundle\Endpoint\GetMethodInterface;
use AppBundle\Endpoint\PostMethodInterface;
use AppBundle\Endpoint\SecuredInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

class GeneralController extends Controller {

  /**
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param \AppBundle\Endpoint\AbstractEndpoint $endpoint
   * @return \Symfony\Component\HttpFoundation\Response
   * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
   * @throws \Exception
   */
  protected function executeEndpoint(Request $request, AbstractEndpoint $endpoint) {
    // Check HTTP method
    $methodAllowed = FALSE;
    if ('GET' == $request->getMethod() &&
      $endpoint instanceof GetMethodInterface) {
      $methodAllowed = TRUE;
    }
    if ('POST' == $request->getMethod() &&
      $endpoint instanceof PostMethodInterface) {
      $methodAllowed = TRUE;
    }
    if (!$methodAllowed) {
      throw new MethodNotAllowedException(['POST'],
        sprintf('This API method doesn\'t support %s HTTP method',
          $request->getMethod()));
    }

    // Inject dependencies
    $endpoint->setRequest($request);

    // Check security
    if ($endpoint instanceof SecuredInterface) {
      $endpoint->authenticate();
    }

    // Validate input data and handle request
    try {
      $result = $endpoint->beforeProcess()
        ->validate()
        ->handle();
      $endpoint->afterSuccessfulProcess();
    } catch (\Exception $e) {
      $endpoint->afterFailedProcess();

      throw $e;
    }

    return $result;
  }

}