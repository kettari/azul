<?php

namespace AppBundle\Controller;

use AppBundle\Redirect\Shortcut;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
  /**
   * @Route("/{shortcut}", name="/{shortcut}",
   * requirements={"shortcut":
   *   "[0-9a-zA-Z\-\_]{3,100}"})
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param string $shortcut
   * @return \Symfony\Component\HttpFoundation\Response
   * @throws \Exception
   */
  public function shortcutAction(Request $request, $shortcut)
  {
    $l = $this->container->get('logger');

    try {

      $shortcut_handler = new Shortcut($this->container);
      if ($shortcut_handler->resolve($shortcut, $url, $reason)) {

        // Redirect
        $l->info(
          'Redirecting "{shortcut}" -> "{url}"',
          [
            'shortcut'  => $shortcut,
            'url'       => $url,
            'client_ip' => $request->getClientIp(),
          ]
        );

        return new RedirectResponse($url);

      } else {

        // Give the user good answer why not redirected
        switch ($reason) {
          case Shortcut::REASON_SHORTCUT_NOT_FOUND:
            return $this->render(
              'default/error_404.html.twig',
              [],
              new Response('Shortcut is not found', 404)
            );
            break;
          case Shortcut::REASON_SHORTCUT_EXPIRED:
            return $this->render('default/error_expired.html.twig');
            break;
        }

      }

    } catch (Exception $e) {
      if ('dev' == $this->getParameter("kernel.environment")) {
        throw $e;
      } else {
        $l->critical('Exception: '.$e->getMessage());

        return new Response('Internal Server Error :(', 500);
      }
    }

    // Should not get here
    return new Response('=)');
  }

  /**
   * @Route("/{shortcut}/", name="/{shortcut}/ (alias)",
   * requirements={"shortcut":
   *   "[0-9a-zA-Z]{3,100}"})
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param string $shortcut
   * @return \Symfony\Component\HttpFoundation\Response
   * @throws \Exception
   */
  public function shortcutAliasAction(Request $request, $shortcut) {
    return $this->shortcutAction($request, $shortcut);
  }
}
