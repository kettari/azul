<?php
declare(strict_types=1);

namespace AppBundle\Endpoint;

use AppBundle\Entity\LinkRequest;
use AppBundle\NowDateHelper;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ShortcutResolveEndpoint extends AbstractApiEndpoint implements GetMethodInterface {
  /**
   * Validate request
   *
   * @return AbstractEndpoint
   */
  function validate() {
    return $this;
  }

  /**
   * Handle request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   * @throws \Doctrine\ORM\OptimisticLockException
   */
  function handle() {
    /** @var \AppBundle\Entity\Link $link */
    if (is_null($link = $this->getDoctrine()
      ->getRepository('AppBundle:Link')
      ->findOneByShortcut($this->getRequest()
        ->get('shortcut')))) {
      throw new NotFoundHttpException('Shortcut is not found');
    }

    // Check if URL expired
    if (!is_null($link->getDateExpires()) &&
      ($link->getDateExpires() < NowDateHelper::getNow())) {
      $this->getLogger()
        ->info('Shortcut "{shortcut}" -> "{url}" expired on "{date_expires}" for client {client_ip}',
          [
            'shortcut'     => $this->getRequest()
              ->get('shortcut'),
            'url'          => $link->getUrl(),
            'date_expires' => $link->getDateExpires()
              ->format('Y-m-d\TH:i:s\Z'),
            'client_ip'    => $this->getRequest()
              ->getClientIp(),
          ]);

      throw new GoneHttpException('Shortcut has expired');
    }

    // Redirect
    $this->getLogger()
      ->info('Redirecting "{shortcut}" -> "{url}" for client {client_ip}', [
        'shortcut'  => $this->getRequest()
          ->get('shortcut'),
        'url'       => $link->getUrl(),
        'client_ip' => $this->getRequest()
          ->getClientIp(),
      ]);
    // Update hits and last access date
    $link->setHits($link->getHits() + 1)
      ->setDateLastAccessed(NowDateHelper::getNow());
    /** @var \Doctrine\ORM\EntityManager $em */
    $em = $this->getDoctrine()
      ->getManager();
    // Create request record
    $linkRequest = new LinkRequest();
    $linkRequest->setLink($link)
      ->setIp($this->getRequest()
        ->getClientIp());
    $em->persist($linkRequest);
    $em->flush();

    return new RedirectResponse($link->getUrl());
  }

}