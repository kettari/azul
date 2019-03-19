<?php
declare(strict_types=1);

namespace AppBundle\Endpoint;


use AppBundle\NowDateHelper;
use Symfony\Component\HttpFoundation\JsonResponse;

class StatsEndpoint extends AbstractSecuredApiEndpoint implements GetMethodInterface {
  /**
   * Validate incoming JSON against scheme
   *
   * @return \AppBundle\Endpoint\StatsEndpoint
   */
  function validate() {
    return $this;
  }

  /**
   * Handle request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   * @throws \Exception
   */
  function handle() {
    $since24hours = NowDateHelper::getNow();
    $since24hours->sub(new \DateInterval('PT24H'));
    $since90days = NowDateHelper::getNow();
    $since90days->sub(new \DateInterval('P90D'));
    /** @var \Doctrine\ORM\EntityManager $em */
    $em = $this->getDoctrine()
      ->getManager();
    // Requests
    $requests24hours = $em->getRepository('AppBundle:LinkRequest')
      ->findRequestsSince($since24hours);
    $requests90days = $em->getRepository('AppBundle:LinkRequest')
      ->findRequestsSince($since90days);
    // Links
    $links24hours = $em->getRepository('AppBundle:Link')
      ->findLinksCreatedSince($since24hours);
    $links90days = $em->getRepository('AppBundle:Link')
      ->findLinksCreatedSince($since90days);

    $result = [
      'serverDate'  => NowDateHelper::getNow()
        ->format('Y-m-d\TH:i:sP'),
      'last24hours' => [
        'shortcutsCreated'       => count($links24hours),
        'shortcutsRequestsTotal' => count($requests24hours),
      ],
      'last90days'  => [
        'shortcutsCreated'       => count($links90days),
        'shortcutsRequestsTotal' => count($requests90days),
      ],
    ];

    return new JsonResponse($result);
  }

}