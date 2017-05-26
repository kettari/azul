<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;


/**
 * @ORM\Entity
 * @ORM\Table(name="link",indexes={@Index(name="shortcut_idx",columns={"shortcut"})},
 *   options={"collate":"utf8mb4_general_ci", "charset":"utf8mb4"})
 */
class Link
{
  /**
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @ORM\Column(type="string",length=100,unique=true)
   */
  private $shortcut;

  /**
   * @ORM\Column(type="string")
   */
  private $url;

  /**
   * @ORM\Column(type="string",length=50)
   */
  private $status;

  /**
   * @ORM\Column(type="string",length=50)
   */
  private $type;

  /**
   * @ORM\Column(type="datetime",nullable=true)
   */
  private $expires;

  /**
   * @ORM\Column(type="datetime")
   */
  private $created;

  /**
   * @ORM\Column(type="datetime")
   */
  private $updated;

  /**
   * @ORM\Column(type="integer")
   */
  private $hits = 0;

  /**
   * Get id
   *
   * @return integer
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * Get shortcut
   *
   * @return string
   */
  public function getShortcut()
  {
    return $this->shortcut;
  }

  /**
   * Set shortcut
   *
   * @param string $shortcut
   *
   * @return Link
   */
  public function setShortcut($shortcut)
  {
    $this->shortcut = $shortcut;

    return $this;
  }

  /**
   * Get url
   *
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }

  /**
   * Set url
   *
   * @param string $url
   *
   * @return Link
   */
  public function setUrl($url)
  {
    $this->url = $url;

    return $this;
  }

  /**
   * Get status
   *
   * @return string
   */
  public function getStatus()
  {
    return $this->status;
  }

  /**
   * Set status
   *
   * @param string $status
   *
   * @return Link
   */
  public function setStatus($status)
  {
    $this->status = $status;

    return $this;
  }

  /**
   * Get type
   *
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }

  /**
   * Set type
   *
   * @param string $type
   *
   * @return Link
   */
  public function setType($type)
  {
    $this->type = $type;

    return $this;
  }

  /**
   * Get expires
   *
   * @return \DateTime
   */
  public function getExpires()
  {
    return $this->expires;
  }

  /**
   * Set expires
   *
   * @param \DateTime $expires
   *
   * @return Link
   */
  public function setExpires($expires)
  {
    $this->expires = $expires;

    return $this;
  }

  /**
   * Get created
   *
   * @return \DateTime
   */
  public function getCreated()
  {
    return $this->created;
  }

  /**
   * Set created
   *
   * @param \DateTime $created
   *
   * @return Link
   */
  public function setCreated($created)
  {
    $this->created = $created;

    return $this;
  }

  /**
   * Get updated
   *
   * @return \DateTime
   */
  public function getUpdated()
  {
    return $this->updated;
  }

  /**
   * Set updated
   *
   * @param \DateTime $updated
   *
   * @return Link
   */
  public function setUpdated($updated)
  {
    $this->updated = $updated;

    return $this;
  }

  /**
   * Get hits
   *
   * @return integer
   */
  public function getHits()
  {
    return $this->hits;
  }

  /**
   * Set hits
   *
   * @param integer $hits
   *
   * @return Link
   */
  public function setHits($hits)
  {
    $this->hits = $hits;

    return $this;
  }
}
