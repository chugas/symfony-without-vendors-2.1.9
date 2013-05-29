<?php

namespace Application\Success\UsuarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Entity\Group as BaseGroup;

/**
 * @ORM\Entity(repositoryClass="Application\Success\UsuarioBundle\Entity\Repository\GroupRepository")
 * @ORM\Table(name="success_group")
 */
class Group extends BaseGroup {

  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * Get id
   *
   * @return integer $id
   */
  public function getId() {
    return $this->id;
  }
  
  public function __toString() {
    return $this->getName();
  }

}