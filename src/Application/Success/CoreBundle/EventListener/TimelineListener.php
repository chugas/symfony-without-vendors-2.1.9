<?php

namespace Application\Success\CoreBundle\EventListener;

use Application\Success\CoreBundle\Event\TimelineEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Gaston Caldeiro <chugas488@gmail.com>
 */
class TimelineListener implements EventSubscriberInterface {

  protected $actionManager;

  public function __construct($actionManager) {
    $this->actionManager = $actionManager;
  }

  public function onTimelinePersist(TimelineEvent $event) {
    $subject = $this->actionManager->findOrCreateComponent($event->getSubject());
    $complements = $event->getComplements();
    $verb = $event->getVerb();

    $action = $this->actionManager->create($subject, $verb, $complements);
    $this->actionManager->updateAction($action);        
  }

  public static function getSubscribedEvents() {
    return array(
        TimelineEvent::TIMELINE_POST_PERSIST => 'onTimelinePersist'
    );
  }

}
