<?php
/*
namespace Application\Success\UserBundle\Listener;

use Application\Success\PortalBundle\Manager\MailerManager;
use Success\RelationBundle\Event\RelationEvent;
use Success\RelationBundle\Events;

class RelationListener {

  private $mailer;
  private $translator;
  private $templating;
  
  public function __construct(MailerManager $mailer, $templating, $translator) {
    $this->mailer = $mailer;
    $this->templating = $templating;
    $this->translator = $translator;
  }

  public function onRelationPersist(RelationEvent $event) {    
    $relation = $event->getRelation();
    // Enviamos mails unicamente para seguidores entre usuarios
    if($relation->getName() == 'follow'){
      $user_from = $relation->getEntity1();
      $user_to = $relation->getEntity2();
      
      // Enviamos email
      $subject = $this->translator->trans('user.relation.mail.subject');
      $body = $this->templating->render('UsuarioBundle:Relations:mail.html.twig', array(
          'user' => $user_from,
          'user_to' => $user_to
      ));
      $this->mailer->send($subject, $body, $user_from);
    }
  }

  public static function getSubscribedEvents() {
    return array(
        Events::RELATION_PRE_PERSIST => 'onRelationPersist'
    );
  }  

}*/
