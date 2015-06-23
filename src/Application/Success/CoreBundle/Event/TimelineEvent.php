<?php

namespace Application\Success\CoreBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class TimelineEvent extends Event {
    
  const TIMELINE_POST_PERSIST = 'success.timeline.post_persist';
  
  private static $verbs = array(
    'create_company'          => 'create_company',
    'create_company_product'  => 'create_company_product',
    'create_user_product'     => 'create_user_product',
    'create_topic'            => 'create_topic',
    'create_topic_comment'    => 'create_topic_comment',
    'comment_news'            => 'comment_news',
  );

  private $verb;
  private $subject;
  private $complements;

  public function __construct($subject, $verb, $complements) {
    $this->subject = $subject;
    $this->verb = $verb;
    $this->complements = $complements;
  }

  public function getSubject() {
    return $this->subject;
  }
  
  public function getVerb(){
    return $this->verb;
  }
  
  public function getComplements(){
    return $this->complements;
  }
  
  public static function getSupportedVerbs(){
    return self::$verbs;
  }

}
