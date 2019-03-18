<?php
declare(strict_types=1);

namespace AppBundle;


use AppBundle\Entity\Link;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

class UrlShortener {
  use RandomStringTrait;

  const MAX_TRIES_COUNT = 50;

  /**
   * @var LoggerInterface
   */
  private $logger;

  /**
   * @var RegistryInterface
   */
  private $doctrine;

  /**
   * UrlShortener constructor.
   *
   * @param \Psr\Log\LoggerInterface $logger
   * @param \Symfony\Bridge\Doctrine\RegistryInterface $doctrine
   */
  public function __construct(LoggerInterface $logger, RegistryInterface $doctrine) {
    $this->logger = $logger;
    $this->doctrine = $doctrine;
  }

  /**
   * Creates alias for the url.
   *
   * @param string $url
   * @param null|\DateTime $expires
   * @return \AppBundle\Entity\Link
   * @throws \Exception
   */
  public function shorten($url, $expires = NULL) {
    $link = new Link();
    $link->setUrl($url)
      ->setShortcut($this->generateUniqueShortcut())
      ->setDateExpires($expires);
    $this->doctrine->getManager()
      ->persist($link);
    $this->doctrine->getManager()
      ->flush();

    return $link;
  }

  /**
   * @throws \Exception
   * @return string
   */
  private function generateUniqueShortcut() {
    $tries = 0;
    do {
      $shortcut = $this->generateRandomString();
      if ($tries++ > self::MAX_TRIES_COUNT) {
        throw new \Exception(sprintf('Max tries count exceeded when generating unique shortcut'));
      }
    } while (!is_null($this->doctrine->getRepository('AppBundle:Link')
      ->findOneByShortcut($shortcut)));

    return $shortcut;
  }
}