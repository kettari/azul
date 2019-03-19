<?php
declare(strict_types=1);

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;


/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OwnerRepository")
 * @ORM\Table(name="owner",indexes={@Index(name="api_keys_idx",columns={"api_key"}),
 *   @Index(name="valid_idx",columns={"valid_until"})})
 */
class Owner {
  /**
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @var string
   * @ORM\Column(type="string",length=50,unique=true)
   */
  private $apiKey = '';

  /**
   * @var \DateTime
   * @ORM\Column(type="datetime",nullable=false)
   */
  private $validUntil;

  /**
   * ~~INVERSE SIDE~~
   *
   * @ORM\OneToMany(targetEntity="AppBundle\Entity\Link", mappedBy="owner")
   */
  private $links;

  /**
   * Owner constructor.
   */
  public function __construct() {
    $this->links = new ArrayCollection();
  }

  /**
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Add link
   *
   * @param \AppBundle\Entity\Link $link
   * @return \AppBundle\Entity\Owner
   */
  public function addLink(Link $link) {
    $this->links[] = $link;

    return $this;
  }

  /**
   * Remove link
   *
   * @param \AppBundle\Entity\Link $link
   */
  public function removeLink(Link $link) {
    $this->links->removeElement($link);
  }

  /**
   * Get links
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getLinks() {
    return $this->links;
  }

  /**
   * @return string
   */
  public function getApiKey(): string {
    return $this->apiKey;
  }

  /**
   * @param string $apiKey
   * @return \AppBundle\Entity\Owner
   */
  public function setApiKey(string $apiKey): Owner {
    $this->apiKey = $apiKey;

    return $this;
  }

  /**
   * @return \DateTime
   */
  public function getValidUntil() {
    return $this->validUntil;
  }

  /**
   * @param \DateTime $validUntil
   * @return \AppBundle\Entity\Owner
   */
  public function setValidUntil(\DateTime $validUntil): Owner {
    $this->validUntil = $validUntil;

    return $this;
  }

}