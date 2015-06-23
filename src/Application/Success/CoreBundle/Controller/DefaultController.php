<?php

namespace Application\Success\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller {

  public function indexAction() {
    $eventos = $this->get('success.repository.evento')->findProximas(10);
    //$user = $this->getUser();
    //\Doctrine\Common\Util\Debug::dump($user);die('aCA');

    if(!$eventos){
      $eventos = $this->get('success.repository.evento')->findPasadas(4);
    }
    //var_dump($eventos); 
    //die();
    $response = $this->render('WebBundle:Frontend/Default:index.html.twig', array('eventos' => $eventos));

    //$response->setPrivate();
    $response->setSharedMaxAge(300);

    return $response;
  }

  public function pageAction($page) {
    $respuesta = $this->render('WebBundle:Frontend/Default:' . $page . '.html.twig');

    $respuesta->setSharedMaxAge(3600 * 24);

    return $respuesta;
  }

  public function headerAction() {
    $response = $this->render('WebBundle:Frontend/Layout:header.html.twig');

    return $response;
  }

  public function contactoAction() {   
    $form = $this->createForm($this->get('success.contact.type'));
    $response = $this->render('WebBundle:Frontend/Contacto:index.html.twig', array('form' => $form->createView()));
    $response->setPrivate();
    return $response;
  }

  public function submitAction() {
    $form = $this->createForm($this->get('success.contact.type'));
    $request = $this->getRequest();
    $form->bind($request);

    if ($form->isValid()) {
      $params = $form->getData();

      // Let say the user it's ok
      $message = $this->get('translator')->trans('contact.submit.success', array(), 'flashes');
      $this->get('session')->getFlashBag()->add('success', $message);
      
      // Enviar mail
      $contact = array('name' => $params['name'], 'email' => $params['email'], 'subject' => $params['subject'], 'body' => $params['body']);
      $body = $this->renderView('WebBundle:Frontend/Contacto/Mails:mail.html.twig', array('contact' => $contact));
      $this->get('success.manager.mailer')->send('Contacto | Justrave', $body, 'Justrave');

      // Redirect somewhere
      if ($request->isXmlHttpRequest()) {
        $return = json_encode(array("responseCode" => 200, "response" => 'OK'));
        return new Response($return, 200, array('Content-Type' => 'application/json'));
      } else {
        return $this->redirect($this->generateUrl('core_contacto'));
      }
    }

    // Errors ? Re-render the form
    if ($request->isXmlHttpRequest()) {
      $return = json_encode(array("responseCode" => 400, "response" => 'ERROR', 'form' => $form->createView()));
      return new Response($return, 200, array('Content-Type' => 'application/json'));
    } else {
      // Let say the user there's a problem
      $message = $this->get('translator')->trans('contact.submit.failure', array(), 'flashes');
      $this->get('session')->getFlashBag()->add('error', $message);

      $response = $this->render('WebBundle:Frontend/Contacto:index.html.twig', array('form' => $form->createView()));
      $response->setPrivate();
      
      return $response;
    }
  }

}
