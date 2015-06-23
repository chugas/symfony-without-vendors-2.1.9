<?php

namespace Application\Success\CoreBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use FOS\CommentBundle\Events;
use FOS\CommentBundle\Event\CommentEvent;
use Application\Success\CoreBundle\Event\TimelineEvent;

/**
 * @author Gaston Caldeiro <chugas488@gmail.com>
 */
class CommentListener implements EventSubscriberInterface {

  protected $newsRepository;

  public function __construct($newsRepository) {
    $this->newsRepository = $newsRepository;
  }

  public function onCommentCreate(CommentEvent $event) {
    $comment = $event->getComment();
    $thread = $comment->getThread();
    $id = $thread->getId();
    $data = explode('_', $id);
    $news_id = $data[1];

    $news = $this->newsRepository->find($news_id);

    //$newEvent = new TimelineEvent($comment->getAuthor(), 'comment_news', array('complement' => $comment));
    $newEvent = new TimelineEvent($comment->getAuthor(), 'comment_news', array('complement' => $comment, 'indirectComplement' => $news, 'userComplement' => $comment->getAuthor()));
    $event->getDispatcher()->dispatch(TimelineEvent::TIMELINE_POST_PERSIST, $newEvent);
  }

  public static function getSubscribedEvents() {
    return array(
        Events::COMMENT_POST_PERSIST => 'onCommentCreate'
    );
  }

}
