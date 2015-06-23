<?php

namespace Application\Success\CoreBundle\Entity;

use Spy\TimelineBundle\Entity\Timeline as BaseTimeline;

class Timeline extends BaseTimeline {

  protected $id;
  protected $action;
  protected $subject;

}
