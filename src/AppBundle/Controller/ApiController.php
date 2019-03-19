<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

class ApiController extends GeneralController {
  /**
   * @Route("/{shortcut}/", name="/{shortcut}/ (alias)",
   * requirements={"shortcut":
   *   "[0-9a-zA-Z\-\_]{0,100}"})
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @return \Symfony\Component\HttpFoundation\Response
   * @throws \Exception
   */
  public function shortcutResolveAliasAction(Request $request) {
    return $this->shortcutResolveAction($request);
  }

  /**
   * @Route("/{shortcut}", name="/{shortcut}",
   * requirements={"shortcut":
   *   "[0-9a-zA-Z\-\_]{0,100}"})
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @return \Symfony\Component\HttpFoundation\Response
   * @throws \Exception
   */
  public function shortcutResolveAction(Request $request) {
    $logger = $this->container->get('logger');
    try {
      return $this->executeEndpoint($request,
        $this->container->get('shortcut_resolve_endpoint'));
    } catch (NotFoundHttpException $e) {
      return $this->render('default/error_404.html.twig', [],
        new Response($e->getMessage(), 404));
    } catch (GoneHttpException $e) {
      return $this->render('default/error_410.html.twig', [],
        new Response($e->getMessage(), 410));
    } catch (\Exception $e) {
      if ('dev' == $this->getParameter("kernel.environment")) {
        throw $e;
      } else {
        $logger->critical('Exception: '.$e->getMessage());

        return new Response('Internal Server Error :(', 500);
      }
    }
  }

  /**
   * @Route("/api/v1/shortcut", name="/shortcut")
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @return \Symfony\Component\HttpFoundation\Response
   * @throws \Exception
   */
  public function shortcutCreateAction(Request $request) {
    $logger = $this->container->get('logger');
    try {
      return $this->executeEndpoint($request,
        $this->container->get('shortcut_create_endpoint'));
    } catch (\InvalidArgumentException $e) {
      return $this->apiError(Response::HTTP_BAD_REQUEST, $e->getMessage());
    } catch (MethodNotAllowedException $e) {
      return $this->apiError(Response::HTTP_METHOD_NOT_ALLOWED,
        $e->getMessage());
    } catch (BadRequestHttpException $e) {
      return $this->apiError(Response::HTTP_BAD_REQUEST, $e->getMessage());
    } catch (ConflictHttpException $e) {
      return $this->apiError(Response::HTTP_CONFLICT, $e->getMessage());
    } catch (UnauthorizedHttpException $e) {
      return $this->apiError(Response::HTTP_UNAUTHORIZED, $e->getMessage());
    } catch (AccessDeniedHttpException $e) {
      return $this->apiError(Response::HTTP_FORBIDDEN, $e->getMessage());
    } catch (\Exception $e) {
      if ('dev' == $this->getParameter("kernel.environment")) {
        throw $e;
      } else {
        $logger->critical('Exception: '.$e->getMessage());

        return $this->apiError(Response::HTTP_INTERNAL_SERVER_ERROR,
          'Internal Server Error :(');
      }
    }
  }

  /**
   * @param int $errorCode
   * @param string $errorDescription
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  protected function apiError($errorCode, $errorDescription = '') {
    return new JsonResponse([
      'errorCode'        => $errorCode,
      'errorDescription' => $errorDescription,
    ], $errorCode);
  }

  /**
   * @Route("/api/v1/stats", name="/stats")
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @return \Symfony\Component\HttpFoundation\Response
   * @throws \Exception
   */
  public function statsAction(Request $request) {
    $logger = $this->container->get('logger');
    try {
      return $this->executeEndpoint($request,
        $this->container->get('stats_endpoint'));
    } catch (\InvalidArgumentException $e) {
      return $this->apiError(Response::HTTP_BAD_REQUEST, $e->getMessage());
    } catch (MethodNotAllowedException $e) {
      return $this->apiError(Response::HTTP_METHOD_NOT_ALLOWED,
        $e->getMessage());
    } catch (BadRequestHttpException $e) {
      return $this->apiError(Response::HTTP_BAD_REQUEST, $e->getMessage());
    } catch (ConflictHttpException $e) {
      return $this->apiError(Response::HTTP_CONFLICT, $e->getMessage());
    } catch (UnauthorizedHttpException $e) {
      return $this->apiError(Response::HTTP_UNAUTHORIZED, $e->getMessage());
    } catch (AccessDeniedHttpException $e) {
      return $this->apiError(Response::HTTP_FORBIDDEN, $e->getMessage());
    } catch (\Exception $e) {
      if ('dev' == $this->getParameter("kernel.environment")) {
        throw $e;
      } else {
        $logger->critical('Exception: '.$e->getMessage());

        return new Response('Internal Server Error :(', 500);
      }
    }
  }
}
