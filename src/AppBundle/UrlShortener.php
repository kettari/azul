<?php
declare(strict_types=1);

namespace AppBundle;


use AppBundle\Entity\Link;
use AppBundle\Entity\Owner;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

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
   * @param Owner $owner
   * @param null|\DateTime $expires
   * @param null|string $shortcut
   * @return \AppBundle\Entity\Link
   * @throws \Exception
   */
  public function shorten($url, $owner, $expires = NULL, $shortcut = NULL) {
    if (!is_null($shortcut) &&
      !is_null($this->doctrine->getRepository('AppBundle:Link')
        ->findOneByShortcut($shortcut))) {
      throw new ConflictHttpException(sprintf('Shortcut "%s" already used',
        $shortcut));
    }

    $link = new Link();
    $link->setUrl($url)
      ->setOwner($owner)
      ->setShortcut(is_null($shortcut) ? $this->generateUniqueShortcut() : $shortcut)
      ->setDateExpires($expires)
      ->setType(is_null($expires) ? 'permanent' : 'temporary');
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
    $length = 3;
    do {
      $shortcut = $this->generateRandomString($length);
      if ($tries++ > self::MAX_TRIES_COUNT) {
        throw new \Exception(sprintf('Max tries count exceeded when generating unique shortcut, length = %d',
          $length));
      }
      $length++;
    } while (!is_null($this->doctrine->getRepository('AppBundle:Link')
      ->findOneByShortcut($shortcut)));

    return $shortcut;
  }
}