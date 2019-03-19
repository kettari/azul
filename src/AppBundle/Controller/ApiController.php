<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

class ApiController extends GeneralController {
  /**
   * @Route("/api/v1/shorten", name="/shorten")
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @return \Symfony\Component\HttpFoundation\Response
   * @throws \Exception
   */
  public function shortenAction(Request $request) {
    $logger = $this->container->get('logger');
    try {
      return $this->executeEndpoint($request,
        $this->container->get('shortener_endpoint'));
    } catch (\InvalidArgumentException $e) {
      return $this->apiError(Response::HTTP_BAD_REQUEST,
        Response::HTTP_BAD_REQUEST, $e->getMessage());
    } catch (MethodNotAllowedException $e) {
      return $this->apiError(Response::HTTP_METHOD_NOT_ALLOWED,
        Response::HTTP_METHOD_NOT_ALLOWED, $e->getMessage());
    } catch (BadRequestHttpException $e) {
      return $this->apiError(Response::HTTP_BAD_REQUEST,
        Response::HTTP_BAD_REQUEST, $e->getMessage());
    } catch (ConflictHttpException $e) {
      return $this->apiError(Response::HTTP_CONFLICT, Response::HTTP_CONFLICT,
        $e->getMessage());
    } catch (\Exception $e) {
      if ('dev' == $this->getParameter("kernel.environment")) {
        throw $e;
      } else {
        $logger->critical('Exception: '.$e->getMessage());

        return $this->apiError(Response::HTTP_INTERNAL_SERVER_ERROR,
          Response::HTTP_INTERNAL_SERVER_ERROR, 'Internal Server Error :(');
      }
    }
  }

  /**
   * @param int $httpCode
   * @param int $errorCode
   * @param string $errorDescription
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  protected function apiError($httpCode, $errorCode, $errorDescription = '') {
    return new JsonResponse([
      'errorCode'        => $errorCode,
      'errorDescription' => $errorDescription,
    ], $httpCode);
  }
}
