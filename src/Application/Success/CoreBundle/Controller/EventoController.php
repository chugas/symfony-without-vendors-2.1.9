<?php

namespace Application\Success\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EventoController extends Controller {
  
  public function indexAction() {
    $request = $this->getRequest();
    $page = $request->get('page', 1);
    $this->get('success.repository.evento')->setMaxPerPage(5);
    $eventos = $this->get('success.repository.evento')->getList($page);

    if ($this->getRequest()->isXmlHttpRequest()) {

      $json = json_encode(array(
          'response' => $this->renderView('WebBundle:Frontend/Evento/response:list.html.twig', array('eventos' => $eventos)), 
          'haveToPaginate' => (count($eventos) == $this->get('success.repository.evento')->getMaxPerPage()),
          'href' => ( $this->get('router')->generate('web_eventos', array('page' => $page + 1)) )
      ));

      $response = new Response($json, 200, array('Content-Type' => 'application/json'));
      
      $response->setPrivate();
      
      return $response;

    } else {
      $response = $this->render('WebBundle:Frontend/Evento:index.html.twig', array(
          'eventos' => $eventos,
          'max_per_page' => $this->get('success.repository.evento')->getMaxPerPage()
      ));
      $response->setSharedMaxAge(300);
      
      return $response;
    }
  }
  
  public function rightAction() {
    $response = $this->render('WebBundle:Frontend/Evento/private:right.html.twig');

    $response->setPrivate();

    return $response;
  }

  public function rightShowAction($id) {
    $evento = $this->getFiesta($id);
    
    $response = $this->render('WebBundle:Frontend/Evento/private:right_show.html.twig', array('evento' => $evento));

    $response->setPrivate();

    return $response;
  }

  public function createAction() {
    $request = $this->getRequest();

    $type = $this->get('success.form.type.evento');
    $productoratype = $this->get('success.form.type.productora');
    
    $form = $this->getForm($type);
    $formProductora = $this->getForm($productoratype);

    if ($request->isMethod('POST')) {

      // CHEQUEO
      //$data = $request->request->get($type->getName());
      $resource = $this->get('success.repository.evento')->createNew();
      $form->setData($resource);

      if ($form->bind($request)->isValid()) {
        $user = $this->getUser();
        $lineup = $request->get('lineup');
        $resource->setLineup($lineup);
        $resource->setUser($user);
        
        $this->create($resource);
        $this->setFlash('success', 'create');

        return $this->redirect($this->generateUrl('web_eventos'));
      } else {
          var_dump($form->getErrorsAsString());die();
      }

    } else {
      
      $resource = $this->get('success.repository.evento')->createNew();
      $form->setData($resource);
      
    }

    return $this->render('WebBundle:Frontend/Evento:create.html.twig', array(
                'form'    => $form->createView(),
                'evento' => $resource,
                'formProductora' => $formProductora->createView()
            ));

  }

  public function showAction($id) {
    $fiesta = $this->getFiesta($id);

    $respuesta = $this->render('WebBundle:Frontend/Evento:show.html.twig', array(
                'evento' => $fiesta
            ));
    $respuesta->setSharedMaxAge(300);
    return $respuesta;
  }
  
  /**
   * Display the form for editing or update the resource.
   */
  public function updateAction($id) {
    $this->check($id);
    
    $request = $this->getRequest();

    $resource = $this->get('success.repository.event')->find($id);

    $type = $this->get('success.form.type.event');
    
    $form = $this->getForm($type, $resource);

    if ($request->isMethod('POST')) {
      if($form->bind($request)->isValid()){
        $this->update($resource);

        $this->setFlash('success', 'create');

        return $this->redirect($this->generateUrl('company_update', array('id' => $resource->getId())));
      }
    }

    return $this->render('WebBundle:Frontend/Evento:update.html.twig', array(
                'form'    => $form->createView(),
                'company' => $resource,
                'browser' => $this->get('portal.browser.detect')
            ));
  }  
  
  /**
   * Chequea Permisos. En caso de que el usuario no tenga permisos para administrar la fiesta
   * de id $id se lanza una excepcion
   * 
   * @param type $id
   * @return boolean
   * @throws AccessDeniedHttpException
   */
  public function check($id){
    if(!$this->getUser()->hasFiesta($id)){
      throw new AccessDeniedHttpException("Acceso Denegado");
    }
    return true;
  }

  /**
   * Delete resource.
   */
  public function deleteAction() {
    $resource = $this->findOr404();

    $this->delete($resource);
    $this->setFlash('success', 'delete');

    return $this->redirectToIndex($resource);
  }

  public function create($resource) {
    //$this->dispatchEvent('success.product.pre_create', $resource);
    $this->persistAndFlush($resource);
    //$this->dispatchEvent('success.product.post_create', $resource);
  }

  public function update($resource) {
    //$this->dispatchEvent('success.product.pre_update', $resource);
    $this->persistAndFlush($resource);
    //$this->dispatchEvent('success.product.post_update', $resource);
  }

  public function delete($resource) {
    //$this->dispatchEvent('pre_delete', $resource);
    $this->removeAndFlush($resource);
    //$this->dispatchEvent('post_delete', $resource);
  }

  public function persistAndFlush($resource) {
    $manager = $this->getDoctrine()->getManager();

    $manager->persist($resource);
    $manager->flush();
  }

  public function removeAndFlush($resource) {
    $manager = $this->getDoctrine()->getManager();

    $manager->remove($resource);
    $manager->flush();
  }

  /**
   * Informs listeners that event data was used
   *
   * @param string       $name
   * @param Event|object $eventOrResource
   */
  public function dispatchEvent($name, $eventOrResource) {
    if (!$eventOrResource instanceof Event) {
      $eventOrResource = new GenericEvent($eventOrResource);
    }

    $this->get('event_dispatcher')->dispatch($name, $eventOrResource);
  }

  public function getManager() {
    return $this->getDoctrine()->getManager();
  }

  protected function setFlash($type, $event) {
    return $this
            ->get('session')
            ->getFlashBag()
            ->add($type, $this->generateFlashMessage($event))
    ;
  }

  protected function generateFlashMessage($event) {
    $message = 'success.resource.' . $event;
    
    return $this->get('translator')->trans($message, array('%resource%' => 'flashes'), 'flashes');    
  }
  
  public function getForm($type, $resource = null)
  {
      return $this->createForm($type, $resource);
  }
  
  public function getFiesta($id){
    $fiesta = $this->get('success.repository.evento')->find($id);
      
    if (!$fiesta) {
      throw new NotFoundHttpException("Fiesta '" . $id . "' no encontrada");
    }
    
    return $fiesta;
  }
}
