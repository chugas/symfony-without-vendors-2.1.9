<?php

namespace Application\Success\CoreBundle\Timeline;

use Spy\Timeline\Spread\SpreadInterface;
use Spy\Timeline\Model\ActionInterface;
use Spy\Timeline\Spread\Entry\EntryCollection;
use Spy\Timeline\Spread\Entry\EntryUnaware;
//use Spy\Timeline\Spread\Entry\Entry;
use Application\Success\CoreBundle\Event\TimelineEvent;

/**
 * Spread
 *
 * @uses SpreadInterface
 * @author Gaston Caldeiro <chugas488@gmail.com>
 */
class Spread implements SpreadInterface {

  /**
   * {@inheritdoc}
   */
  public function supports(ActionInterface $action) {
    return in_array($action->getVerb(), TimelineEvent::getSupportedVerbs());
  }

  /**
   * {@inheritdoc}
   */
  public function process(ActionInterface $action, EntryCollection $coll) {
    $coll->add(new EntryUnaware('Application\Success\PortalBundle\Entity\Dummy', 1));
  }

}
