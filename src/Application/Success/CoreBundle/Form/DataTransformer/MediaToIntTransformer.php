<?php

namespace Application\Success\CoreBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;

class MediaToIntTransformer extends EntityToIntTransformer {

  /**
   * @param ObjectManager $om
   */
  public function __construct($om) {
    parent::__construct($om);
    $this->setEntityClass("Application\Success\MediaBundle\Entity\Media");
    $this->setEntityRepository("SuccessMediaBundle:Media");
    $this->setEntityType("Media");
  }

}

