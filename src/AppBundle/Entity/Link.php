<?php
declare(strict_types=1);

namespace AppBundle\Entity;

use AppBundle\NowDateHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;


/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LinkRepository")
 * @ORM\Table(name="link",indexes={@Index(name="creation_idx",columns={"date_created"})})
 */
class Link {
  /**
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @var string
   * @ORM\Column(type="string",length=4096)
   */
  private $url = '';

  /**
   * @var string
   * @ORM\Column(type="string",length=20)
   */
  private $type = '';

  /**
   * @var string
   * @ORM\Column(type="string",length=100,unique=true,options={"collation":"utf8_bin"})
   */
  private $shortcut = '';

  /**
   * @var \DateTime
   * @ORM\Column(type="datetime")
   */
  private $dateCreated;

  /**
   * @var \DateTime
   * @ORM\Column(type="datetime",nullable=true)
   */
  private $dateLastAccessed;

  /**
   * @var \DateTime
   * @ORM\Column(type="datetime",nullable=true)
   */
  private $dateExpires;

  /**
   * @var int
   * @ORM\Column(type="integer")
   */
  private $hits = 0;

  /**
   * @var bool
   * @ORM\Column(type="boolean")
   */
  private $deleted = FALSE;

  /**
   * ~~INVERSE SIDE~~
   *
   * @ORM\OneToMany(targetEntity="AppBundle\Entity\LinkRequest", mappedBy="link")
   */
  private $linkRequests;

  /**
   * ~~OWNING SIDE~~
   *
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Owner", inversedBy="links")
   * @ORM\JoinColumn(nullable=false)
   */
  private $owner;

  /**
   * Link constructor.
   */
  public function __construct() {
    $this->dateCreated = NowDateHelper::getNow();
    $this->linkRequests = new ArrayCollection();
  }

  /**
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * @return string
   */
  public function getUrl(): string {
    return $this->url;
  }

  /**
   * @param string $url
   * @return Link
   */
  public function setUrl(string $url): Link {
    $this->url = $url;

    return $this;
  }

  /**
   * @return string
   */
  public function getType(): string {
    return $this->type;
  }

  /**
   * @param string $type
   * @return Link
   */
  public function setType(string $type): Link {
    $this->type = $type;

    return $this;
  }

  /**
   * @return string
   */
  public function getShortcut(): string {
    return $this->shortcut;
  }

  /**
   * @param string $shortcut
   * @return Link
   */
  public function setShortcut(string $shortcut): Link {
    $this->shortcut = $shortcut;

    return $this;
  }

  /**
   * @return \DateTime
   */
  public function getDateCreated(): \DateTime {
    return $this->dateCreated;
  }

  /**
   * @param \DateTime $dateCreated
   * @return Link
   */
  public function setDateCreated(\DateTime $dateCreated): Link {
    $this->dateCreated = $dateCreated;

    return $this;
  }

  /**
   * @return \DateTime
   */
  public function getDateLastAccessed() {
    return $this->dateLastAccessed;
  }

  /**
   * @param \DateTime $dateLastAccessed
   * @return Link
   */
  public function setDateLastAccessed($dateLastAccessed): Link {
    $this->dateLastAccessed = $dateLastAccessed;

    return $this;
  }

  /**
   * @return \DateTime
   */
  public function getDateExpires() {
    return $this->dateExpires;
  }

  /**
   * @param \DateTime|null $dateExpires
   * @return Link
   */
  public function setDateExpires($dateExpires = NULL): Link {
    $this->dateExpires = $dateExpires;

    return $this;
  }

  /**
   * @return int
   */
  public function getHits(): int {
    return $this->hits;
  }

  /**
   * @param int $hits
   * @return Link
   */
  public function setHits(int $hits): Link {
    $this->hits = $hits;

    return $this;
  }

  /**
   * @return bool
   */
  public function isDeleted(): bool {
    return $this->deleted;
  }

  /**
   * @param bool $deleted
   * @return Link
   */
  public function setDeleted(bool $deleted): Link {
    $this->deleted = $deleted;

    return $this;
  }

  /**
   * Add link request
   *
   * @param \AppBundle\Entity\LinkRequest $linkRequest
   * @return Link
   */
  public function addLinkRequest(LinkRequest $linkRequest) {
    $this->linkRequests[] = $linkRequest;

    return $this;
  }

  /**
   * Remove link request
   *
   * @param \AppBundle\Entity\LinkRequest $linkRequest
   */
  public function removeLinkRequest(LinkRequest $linkRequest) {
    $this->linkRequests->removeElement($linkRequest);
  }

  /**
   * Get link requests
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getLinkRequests() {
    return $this->linkRequests;
  }

  /**
   * @return \AppBundle\Entity\Owner
   */
  public function getOwner(): Owner {
    return $this->owner;
  }

  /**
   * @param \AppBundle\Entity\Owner $owner
   * @return \AppBundle\Entity\Link
   */
  public function setOwner(Owner $owner) {
    $this->owner = $owner;

    return $this;
  }
}