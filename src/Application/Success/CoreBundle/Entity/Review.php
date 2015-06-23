<?php

namespace Application\Success\CoreBundle\Entity;

class Review {

  /**
   * Product id.
   *
   * @var mixed
   */
  protected $id;

  /**
   * Product name.
   *
   * @var string
   */
  protected $title;

  /**
   * Permalink for the event.
   * Used in url to access it.
   *
   * @var string
   */
  protected $slug;

  /**
   * Product description.
   *
   * @var string
   */
  protected $description;

  /**
   * Deletion time.
   *
   * @var \DateTime
   */
  protected $deletedAt;
  protected $user;

  public function __construct() {
    $this->user = null;
  }

  public function getId() {
    return $this->id;
  }

  public function __toString() {
    return (string) $this->getTitle();
  }

  public function getTitle() {
    return $this->title;
  }

  public function setTitle($name) {
    $this->title = $name;

    return $this;
  }

  public function getSlug() {
    return $this->slug;
  }

  public function setSlug($slug) {
    $this->slug = $slug;

    return $this;
  }

  public function getDescription() {
    return $this->description;
  }

  public function setDescription($description) {
    $this->description = $description;

    return $this;
  }

  public function isDeleted() {
    return null !== $this->deletedAt && new \DateTime() >= $this->deletedAt;
  }

  public function getDeletedAt() {
    return $this->deletedAt;
  }

  public function setDeletedAt(\DateTime $deletedAt) {
    $this->deletedAt = $deletedAt;

    return $this;
  }

  public function getUser() {
    return $this->user;
  }

  public function setUser(\Symfony\Component\Security\Core\User\UserInterface $user) {
    $this->user = $user;

    return $this;
  }

}

?>
