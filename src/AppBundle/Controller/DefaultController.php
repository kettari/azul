<?php

namespace AppBundle\Controller;

use AppBundle\NowDateHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller {
  /**
   * @Route("/{shortcut}/", name="/{shortcut}/ (alias)",
   * requirements={"shortcut":
   *   "[0-9a-zA-Z]{0,100}"})
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param string $shortcut
   * @return \Symfony\Component\HttpFoundation\Response
   * @throws \Exception
   */
  public function shortcutAliasAction(Request $request, $shortcut) {
    return $this->shortcutAction($request, $shortcut);
  }

  /**
   * @Route("/{shortcut}", name="/{shortcut}",
   * requirements={"shortcut":
   *   "[0-9a-zA-Z\-\_]{0,100}"})
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param string $shortcut
   * @return \Symfony\Component\HttpFoundation\Response
   * @throws \Exception
   */
  public function shortcutAction(Request $request, $shortcut) {
    $logger = $this->container->get('logger');
    $doctrine = $this->container->get('doctrine');

    try {

      /** @var \AppBundle\Entity\Link $link */
      $link = $doctrine->getRepository('AppBundle:Link')
        ->findOneByShortcut($shortcut);
      if (is_null($link)) {
        $logger->info('Not found shortcut "{shortcut}" for the client {client_ip}', [
          'shortcut'  => $shortcut,
          'client_ip' => $request->getClientIp(),
        ]);

        return $this->render('default/error_404.html.twig', [],
          new Response('Shortcut is not found', 404));
      }

      // Check if URL expired
      if (!is_null($link->getDateExpires()) &&
        ($link->getDateExpires() < NowDateHelper::getNow())) {
        $logger->info('Expired "{shortcut}" -> "{url}" for client {client_ip}',
          [
            'shortcut'  => $shortcut,
            'url'       => $link->getUrl(),
            'client_ip' => $request->getClientIp(),
          ]);

        return $this->render('default/error_410.html.twig', [],
          new Response('Shortcut is not found', 410));
      }

      // Redirect
      $logger->info('Redirecting "{shortcut}" -> "{url}" for client {client_ip}',
        [
          'shortcut'  => $shortcut,
          'url'       => $link->getUrl(),
          'client_ip' => $request->getClientIp(),
        ]);

      // Update hits and last access date
      $link->setHits($link->getHits() + 1)
        ->setDateLastAccessed(NowDateHelper::getNow());
      $doctrine->getManager()
        ->flush();

      return new RedirectResponse($link->getUrl());

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
