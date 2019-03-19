<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends Controller {

  const ERROR_METHOD_NOT_ALLOWED = 405;

  /**
   * @Route("/api/v1/shorten", name="/shorten")
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @return \Symfony\Component\HttpFoundation\Response
   * @throws \Exception
   */
  public function shortenAction(Request $request) {
    $logger = $this->container->get('logger');
    $doctrine = $this->container->get('doctrine');

    try {

      // Check HTTP method
      if ('POST' != $request->getMethod()) {
        return $this->apiError(Response::HTTP_METHOD_NOT_ALLOWED,
          self::ERROR_METHOD_NOT_ALLOWED,
          sprintf('This API method doesn\'t support %s HTTP method',
            $request->getMethod()));
      }

      /** @var \AppBundle\Entity\Link $link */
      $link = $doctrine->getRepository('AppBundle:Link')
        ->findOneByShortcut($shortcut);
      if (is_null($link)) {
        $logger->info('Not found "{shortcut}" for client {client_ip}', [
          'shortcut'  => $shortcut,
          'client_ip' => $request->getClientIp(),
        ]);

        return $this->render('default/error_404.html.twig', [],
          new Response('Shortcut is not found', 404));
      }


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
