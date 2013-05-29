<?php

namespace Application\Success\UsuarioBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\SecurityController as BaseController;

class SecurityController extends BaseController {

  /**
   * Muestra la caja de login que se incluye en el lateral de la mayoría de páginas del sitio web.
   * Esta caja se transforma en información y enlaces cuando el usuario se loguea en la aplicación.
   * La respuesta se marca como privada para que no se añada a la cache pública. El trozo de plantilla
   * que llama a esta función se sirve a través de ESI
   *
   */
  public function cajaLoginAction() {
    //$usuario = $this->get('security.context')->getToken()->getUser();
    /* $peticion = $this->getRequest();
      $sesion = $peticion->getSession();

      $error = $peticion->attributes->get(
      SecurityContext::AUTHENTICATION_ERROR, $sesion->get(SecurityContext::AUTHENTICATION_ERROR)
      ); */
    
    $respuesta = $this->container->get('templating')->renderResponse('UsuarioBundle:Security:cajaLogin.html.' . $this->container->getParameter('fos_user.template.engine'));
    $respuesta->setMaxAge(30);
    $respuesta->setPrivate();
    
    return $respuesta;
  }

}
