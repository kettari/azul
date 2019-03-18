<?php
declare(strict_types=1);

namespace AppBundle\Entity;

use AppBundle\NowDateHelper;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;


/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LinkRequestRepository")
 * @ORM\Table(name="link_request",indexes={@Index(name="creation_idx",columns={"date_created"})})
 */
class LinkRequest {
  /**
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * ~~OWNING SIDE~~
   *
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Link", inversedBy="linkRequests")
   */
  private $link;

  /**
   * @var \DateTime
   * @ORM\Column(type="datetime")
   */
  private $dateCreated;

  /**
   * @var string
   * @ORM\Column(type="string",length=45)
   */
  private $ip;

  /**
   * Link constructor.
   */
  public function __construct() {
    $this->dateCreated = NowDateHelper::getNow();
  }

  /**
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * @return \DateTime
   */
  public function getDateCreated(): \DateTime {
    return $this->dateCreated;
  }

  /**
   * @param \DateTime $dateCreated
   * @return LinkRequest
   */
  public function setDateCreated(\DateTime $dateCreated): LinkRequest {
    $this->dateCreated = $dateCreated;

    return $this;
  }

  /**
   * @return \AppBundle\Entity\Link
   */
  public function getLink() {
    return $this->link;
  }

  /**
   * @param Link $link
   * @return LinkRequest
   */
  public function setLink(Link $link) {
    $this->link = $link;

    return $this;
  }

  /**
   * @param string $ip
   * @return LinkRequest
   */
  public function setIp(string $ip): LinkRequest {
    $this->ip = $ip;

    return $this;
  }

  /**
   * @return string
   */
  public function getIp(): string {
    return $this->ip;
  }
}