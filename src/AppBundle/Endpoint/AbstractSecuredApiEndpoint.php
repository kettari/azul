<?php
declare(strict_types=1);

namespace AppBundle\Endpoint;


use AppBundle\Entity\Owner;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

abstract class AbstractSecuredApiEndpoint extends AbstractApiEndpoint implements SecuredInterface {

  /**
   * @var \AppBundle\Entity\Owner
   */
  private $owner;

  /**
   * Authenticates request
   *
   * @throws \Doctrine\ORM\NonUniqueResultException
   */
  public function authenticate() {
    if ($apiKey = $this->getRequest()
      ->get('apiKey')) {
      /** @var \Doctrine\ORM\EntityManager $em */
      $em = $this->getDoctrine()
        ->getManager();
      if (is_null($this->owner = $em->getRepository('AppBundle:Owner')
        ->findOneByApiKey($apiKey))) {
        throw new AccessDeniedHttpException('API key provided but it is wrong');
      }
    } else {
      throw new UnauthorizedHttpException('', 'API key not provided');
    }
  }

  /**
   * @return \AppBundle\Entity\Owner
   */
  public function getOwner(): Owner {
    return $this->owner;
  }
}