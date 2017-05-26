<?php
/**
 * Created by PhpStorm.
 * User: ant
 * Date: 26.05.2017
 * Time: 20:43
 */

namespace AppBundle\Redirect;


use Symfony\Component\DependencyInjection\ContainerInterface;

class Shortcut
{
  const REASON_OK = 0;
  const REASON_SHORTCUT_NOT_FOUND = 1;
  const REASON_SHORTCUT_EXPIRED = 2;

  /**
   * @var ContainerInterface
   */
  private $container;

  /**
   * Shortcut constructor.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   */
  public function __construct(ContainerInterface $container)
  {
    $this->container = $container;
  }

  /**
   * Resolves shortcut to the URL.
   *
   * @param string $shortcut Shortcut to resolve
   * @param string $url URL that corresponds to the shortcut
   * @param string $reason Extended result of operation: OK, not found, expired
   * @return boolean True if shortcut successfully resolved, false if something
   *   went wrong. See $reason for details of failure.
   */
  public function resolve($shortcut, &$url, &$reason)
  {
    $reason = self::REASON_SHORTCUT_NOT_FOUND;
    $url = '';
    $result = false;

    // Find the shortcut
    $d = $this->container->get('doctrine');
    /** @var \AppBundle\Entity\Link $link */
    if ($link = $d->getRepository('AppBundle:Link')
      ->findOneBy(['shortcut' => $shortcut])
    ) {

      // Check the link is not expired
      $now = new \DateTime('now', new \DateTimeZone('UTC'));
      if (('temporary' == $link->getType()) && ($link->getExpires() < $now)) {

        // Set link expired
        if ('expired' != $link->getStatus()) {
          $link->setStatus('expired');
          $link->setUpdated($now);
        }
        $reason = self::REASON_SHORTCUT_EXPIRED;

      } else {
        // Link is valid, update hits counter
        $link->setHits($link->getHits() + 1);
        $url = $link->getUrl();
        $result = true;
        $reason = self::REASON_OK;
      }

    }
    // Save changes
    $d->getManager()
      ->flush();

    return $result;
  }
}