<?php

namespace Application\Success\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

//use Symfony\Component\HttpFoundation\Response;

class ReviewController extends Controller {

  public function indexAction() {
    $request = $this->getRequest();
    $page = $request->get('page', 1);
    $this->get('success.repository.reviews')->setMaxPerPage(10);
    $reviews = $this->get('success.repository.reviews')->getList($page);

    if ($this->getRequest()->isXmlHttpRequest()) {

      $json = json_encode(array(
          'response' => $this->renderView('WebBundle:Frontend/Review/response:list.html.twig', array('reviews' => $reviews)), 
          'haveToPaginate' => (count($reviews) == $this->get('success.repository.reviews')->getMaxPerPage()),
          'href' => ( $this->get('router')->generate('web_reviews', array('page' => $page + 1)) )
      ));

      $response = new Response($json, 200, array('Content-Type' => 'application/json'));
      
      return $response;

    } else {
      $response = $this->render('WebBundle:Frontend/Review:index.html.twig', array(
          'reviews' => $reviews,
          'max_per_page' => $this->get('success.repository.reviews')->getMaxPerPage()
      ));
      //$response->setSharedMaxAge(300);
      
      return $response;
    }
  }

  public function createAction() {
    $request = $this->getRequest();

    $type = $this->get('success.form.type.review');
    $resource = $this->get('success.repository.reviews')->createNew();

    $form = $this->getForm($type, $resource);

    if ($request->isMethod('POST')) {

      if ($form->bind($request)->isValid()) {
        $user = $this->getUser();
        $resource->setUser($user);

        $this->create($resource);
        $this->setFlash('success', 'create');

        return $this->redirect($this->generateUrl('web_reviews'));
      }
    }

    return $this->render('WebBundle:Frontend/Review:create.html.twig', array(
                'form' => $form->createView(),
                'review' => $resource
            ));
  }

  public function updateAction($id) {
    $review = $this->getReview($id);

    $this->check($review);

    $request = $this->getRequest();

    $type = $this->get('success.form.type.review');

    $form = $this->getForm($type, $review);

    if ($request->isMethod('POST')) {
      if ($form->bind($request)->isValid()) {

        $this->update($review);

        $this->setFlash('success', 'create');

        return $this->redirect($this->generateUrl('company_update', array('id' => $resource->getId())));
      }
    }

    return $this->render('WebBundle:Frontend/Evento:update.html.twig', array(
                'form' => $form->createView(),
                'company' => $resource,
                'browser' => $this->get('portal.browser.detect')
            ));
  }

  public function showAction($id) {
    $review = $this->getReview($id);

    $respuesta = $this->render('WebBundle:Frontend/Review:show.html.twig', array(
        'review' => $review
    ));

    //$respuesta->setSharedMaxAge(300);
    return $respuesta;
  }
  
  public function rightAction() {
    $response = $this->render('WebBundle:Frontend/Review/private:right.html.twig');

    $response->setPrivate();

    return $response;
  }  

  public function getReview($id) {
    $review = $this->get('success.repository.reviews')->find($id);

    if (!$review) {
      throw new NotFoundHttpException("Historia '" . $id . "' no encontrada");
    }

    return $review;
  }

  public function check($review) {
    if ($this->getUser()->getId() != $review->getUser()->getId()) {
      throw new AccessDeniedHttpException("Acceso Denegado");
    }
    return true;
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

  public function getForm($type, $resource = null) {
    return $this->createForm($type, $resource);
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
}
