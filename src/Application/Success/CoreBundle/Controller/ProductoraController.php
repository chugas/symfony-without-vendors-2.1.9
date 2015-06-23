<?php

namespace Application\Success\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ProductoraController extends Controller {
  
  public function createAction() {
    $request = $this->getRequest();

    $resource = $this->get('success.repository.productora')->createNew();
    
    $type = $this->get('success.form.type.productora');
    
    $form = $this->getForm($type, $resource);
      
    if ($request->isMethod('POST')) {
      if ($form->bind($request)->isValid()) {
        $this->create($resource);
        $data = json_encode(array('id' => $resource->getId(), 'text' => $resource->getName()));
        return new Response($data, 200, array('Content-Type' => 'application/json'));
      } else {
        return new Response(json_encode(array('errors' => $form->getErrorsAsString())), 500, array('Content-Type' => 'application/json'));          
      }
    } else {
      return $this->redirect('@homepage');
    }
  }

  public function showAction($id) {
    $fiesta = $this->getFiesta($id);
    
    $respuesta = $this->get('templating')->renderResponse('WebBundle:Frontend/Evento:show.html.twig', array(
                'fiesta' => $fiesta
    ));
    //$respuesta->setSharedMaxAge(60 * 60);
    return $respuesta;
  }
  
  /**
   * #########################################
   * ###TODO##################################
   * #########################################
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
  
  /**
   * Get a form instance for given resource.
   * If no custom form is specified in route defaults as "_form",
   * then a default name is generated using the template "%bundlePrefix%_%resourceName%".
   *
   * @param null|string $resource
   *
   * @return FormInterface
   */
  public function getForm($type, $resource = null)
  {
      return $this->createForm($type, $resource);
  }
  
  public function getUser(){
    return $this->get('security.context')->getToken()->getUser();
  }
  
  public function getFiesta($id){
    $fiesta = $this->get('success.repository.event')->find($id);
      
    if (!$fiesta) {
      throw new NotFoundHttpException("Fiesta '" . $id . "' no encontrada");
    }
    
    return $fiesta;
  }
}
