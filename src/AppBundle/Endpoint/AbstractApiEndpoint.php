<?php
declare(strict_types=1);

namespace AppBundle\Endpoint;


use AppBundle\Entity\Owner;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

abstract class AbstractApiEndpoint extends AbstractEndpoint {
  /**
   * @var LoggerInterface
   */
  private $logger;

  /**
   * @var RegistryInterface
   */
  private $doctrine;

  /**
   * AbstractApiEndpoint constructor.
   *
   * @param LoggerInterface $logger
   * @param RegistryInterface $doctrine
   */
  public function __construct(LoggerInterface $logger, RegistryInterface $doctrine) {
    $this->logger = $logger;
    $this->doctrine = $doctrine;
  }

  /**
   * @return \AppBundle\Endpoint\AbstractEndpoint
   */
  public function beforeProcess() {
    /** @var \Doctrine\ORM\EntityManager $em */
    $em = $this->doctrine->getManager();
    $em->getConnection()
      ->beginTransaction();

    return $this;
  }

  /**
   * @return \AppBundle\Endpoint\AbstractEndpoint
   * @throws \Doctrine\DBAL\ConnectionException
   */
  public function afterSuccessfulProcess() {
    /** @var \Doctrine\ORM\EntityManager $em */
    $em = $this->doctrine->getManager();
    $em->getConnection()
      ->commit();

    return $this;
  }

  /**
   * @return \AppBundle\Endpoint\AbstractEndpoint|void
   * @throws \Doctrine\DBAL\ConnectionException
   */
  function afterFailedProcess() {
    /** @var \Doctrine\ORM\EntityManager $em */
    $em = $this->doctrine->getManager();
    $em->getConnection()
      ->rollBack();
  }


  /**
   * @return RegistryInterface
   */
  public function getDoctrine(): RegistryInterface {
    return $this->doctrine;
  }

  /**
   * @return LoggerInterface
   */
  public function getLogger(): LoggerInterface {
    return $this->logger;
  }

}