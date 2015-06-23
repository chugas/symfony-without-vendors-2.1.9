<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Success\UserBundle\Form\Handler;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use FOS\UserBundle\Form\Handler\RegistrationFormHandler as BaseHandler;
use Success\InviteBundle\Event\RefereableEvent;
use Success\InviteBundle\Events;

class RegistrationFormHandler extends BaseHandler {

  protected $eventDispatcher;
  /*protected $subscriberManager;
  protected $mandantName = 'default';*/
    
  //public function __construct(FormInterface $form, Request $request, UserManagerInterface $userManager, MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator, $newsletterManager) {
  public function __construct(FormInterface $form, Request $request, UserManagerInterface $userManager, MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator, $eventDispatcher) {
    //$this->subscriberManager = $newsletterManager;
    $this->eventDispatcher = $eventDispatcher;

    parent::__construct($form, $request, $userManager, $mailer, $tokenGenerator);
  }
  
  protected function onSuccess(UserInterface $user, $confirmation) {
    parent::onSuccess($user, $confirmation);

    //$this->subscriberManager->subscribe($user);
  }

  public function process($confirmation = false) {
    $user = $this->createUser();
    $this->form->setData($user);

    if ('POST' === $this->request->getMethod()) {
      $this->form->bind($this->request);

      $this->userManager->generateUsername($user);

      if ($this->form->isValid()) {
        $this->onSuccess($user, $confirmation);

        // Enviar senial de registro
        $this->eventDispatcher->dispatch(Events::REFEREABLE_POST_PERSIST, new RefereableEvent($user));
        
        return true;
      }
    }

    return false;
  }

}
