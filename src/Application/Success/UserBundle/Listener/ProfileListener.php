<?php

namespace Application\Success\UserBundle\Listener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class ProfileListener {

  private $contexto, $router = null;
  private $complete = false;
  private $em;

  public function __construct(SecurityContext $context, Router $router, $em) {
    $this->contexto = $context;
    $this->router = $router;
    $this->em = $em;
  }

  public function onSecurityInteractiveLogin(InteractiveLoginEvent $event) {
    $token = $event->getAuthenticationToken();
    
    if ($token->getUser()->haveToComplete()) {
      $this->complete = true;
    }
  }

  public function onKernelResponse(FilterResponseEvent $event) {
    /*if($event->getRequest()->attributes->get('_route') != 'sonata_user_admin_security_check' && $this->complete){
      $portada = $this->router->generate('fos_user_profile_show');
      $event->setResponse(new RedirectResponse($portada));
      $event->stopPropagation();      
    }*/
  }

}
