<?php

namespace Application\Success\CoreBundle\Manager;

/**
 *
 * @author Gaston Caldeiro <chugas488@gmail.com>
 * @version 1.0
 */
class MailerManager {
  
  protected $from;
  
  protected $to;
  
  protected $mailer;

  public function __construct($from, $to, $mailer) {
    $this->from = $from;
    $this->to = $to;
    $this->mailer = $mailer;
  }
  
  public function send($subject, $body, $from, $to = NULL) {
    $swift_message = \Swift_Message::newInstance()
        ->setSubject($subject)
        ->setFrom($this->from, $from)
        ->setContentType('text/html')
        ->setBody($body)
    ;

    $message_to = (is_null($to) ? $this->to : $to);
    
    $swift_message->setTo($message_to);
    return $this->mailer->send($swift_message);
  }
}

?>
