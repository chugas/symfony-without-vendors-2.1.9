<?php

namespace Application\Success\UsuarioBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\RegistrationController as BaseController;

class RegistrationController extends BaseController {

  public function registerAction() {
    $form = $this->container->get('fos_user.registration.form');
    $formHandler = $this->container->get('fos_user.registration.form.handler');
    $confirmationEnabled = $this->container->getParameter('fos_user.registration.confirmation.enabled');

    $process = $formHandler->process($confirmationEnabled);
    if ($process) {
      $user = $form->getData();
      
      /*****************ENVIAR MAIL DE GRACIAS POR SU REGISTRO******************/
      /*$mail = \Swift_Message::newInstance()
      ->setSubject('Mon sujet')
      ->setFrom($this->container->getParameter('vendor_email'))
      ->setTo($recipient)
      ->setBody($this->renderView('DjoUserBundle:Registration:emailThankYou.html.twig',array(),'text/html'));
      $this->get('mailer')->send($mail);*/
      /*************************************************************************/
      
      $authUser = false;
      if ($confirmationEnabled) {
        $this->container->get('session')->set('fos_user_send_confirmation_email/email', $user->getEmail());
        $route = 'fos_user_registration_check_email';
      } else {
        $authUser = true;
        $route = 'fos_user_registration_confirmed';
      }

      $this->setFlash('fos_user_success', 'registration.flash.user_created');
      $url = $this->container->get('router')->generate($route);
      $response = new RedirectResponse($url);

      if ($authUser) {
        $this->authenticateUser($user, $response);
      }

      return $response;
    }

    return $this->container->get('templating')->renderResponse('FOSUserBundle:Registration:register.html.' . $this->getEngine(), array(
                'form' => $form->createView(),
            ));
  }
  
  public function cajaRegistroAction() {
    // Obtenemos el formulario de registro de el usuario, configurado mediante fos_user
    $form = $this->container->get('fos_user.registration.form');

    return $this->container->get('templating')->renderResponse('UsuarioBundle:Registration:cajaRegistro.html.twig', array(
                'formulario' => $form->createView()
            ));
  }
  
  /**
   * Da de baja al usuario actualmente conectado borrando su información
   * de la base de datos
   */
  public function bajaAction() {
    /*$usuario = $this->get('security.context')->getToken()->getUser();

    if (null == $usuario ||
            !$this->get('security.context')->isGranted('ROLE_USUARIO')) {
      $this->get('session')->setFlash('info', 'Para darte de baja primero tienes que conectarte.'
      );

      return $this->redirect($this->generateUrl('usuario_login'));
    }

    $this->get('request')->getSession()->invalidate();
    $this->get('security.context')->setToken(null);

    $em = $this->getDoctrine()->getManager();
    $em->remove($usuario);
    $em->flush();

    return $this->redirect($this->generateUrl('portada'));*/
  }

}
