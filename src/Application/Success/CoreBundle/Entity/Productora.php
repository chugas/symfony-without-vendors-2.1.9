<?php

namespace Application\Success\CoreBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

class Productora {

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
  protected $name;

  /**
   * Permalink for the object productora.
   * Used in url to access it.
   *
   * @var string
   */
  protected $slug;

  /**
   * Deletion time.
   *
   * @var \DateTime
   */
  protected $deletedAt;

  public function __construct() {
  }

  /**
   * {@inheritdoc}
   */
  public function getId() {
    return $this->id;
  }

  public function __toString() {
    return (string) $this->getName();
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->name;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->name = $name;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getSlug() {
    return $this->slug;
  }

  /**
   * {@inheritdoc}
   */
  public function setSlug($slug) {
    $this->slug = $slug;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isDeleted() {
    return null !== $this->deletedAt && new \DateTime() >= $this->deletedAt;
  }

  /**
   * {@inheritdoc}
   */
  public function getDeletedAt() {
    return $this->deletedAt;
  }

  /**
   * {@inheritdoc}
   */
  public function setDeletedAt(\DateTime $deletedAt) {
    $this->deletedAt = $deletedAt;

    return $this;
  }

  public static function loadValidatorMetadata(\Symfony\Component\Validator\Mapping\ClassMetadata $metadata) {
    $metadata->addConstraint(new UniqueEntity(array(
                'fields' => 'name',
                'message' => 'Ya existe una productora con este nombre.',
            )));
  }

}
