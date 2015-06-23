<?php

namespace Application\Success\UserBundle\Entity;

use Success\RelationBundle\Entity\Relation as BaseRelation;

class RelationUser extends BaseRelation {

  protected $id;

  public function getId() {
    return $this->id;
  }
  
}