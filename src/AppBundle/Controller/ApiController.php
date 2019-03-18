<?php

namespace AppBundle\Controller;

use AppBundle\NowDateHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends Controller {

  /**
   * @Route("/api/v1/shorten", name="/shorten")
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param string $shortcut
   * @return \Symfony\Component\HttpFoundation\Response
   * @throws \Exception
   */
  public function shortenAction(Request $request, $shortcut) {
    $logger = $this->container->get('logger');
    $doctrine = $this->container->get('doctrine');

    try {

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
}
