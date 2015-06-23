<?php

namespace Application\Success\CoreBundle\Entity;

use Spy\TimelineBundle\Entity\Action as BaseAction;

class Action extends BaseAction {

  protected $id;
  protected $actionComponents;
  protected $timelines;

}
