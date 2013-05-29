<?php

namespace Application\Success\UsuarioBundle\Listener;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Listener del evento SecurityInteractive que se utiliza para redireccionar
 * al usuario recién logueado a la portada de su ciudad
 */
class LoginListener
{
    private $contexto, $router = null;

    public function __construct(SecurityContext $context, Router $router)
    {
        $this->contexto = $context;
        $this->router   = $router;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        //$token = $event->getAuthenticationToken();
        //$token->getUser();
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
      /* if ($this->contexto->isGranted('ROLE_TIENDA')) {
           $portada = $this->router->generate('extranet_portada');
       } else {
           $portada = $this->router->generate('portada', array(
               'ciudad' => $this->ciudad
           ));
       }

       $event->setResponse(new RedirectResponse($portada));
       $event->stopPropagation();*/
    }
}
