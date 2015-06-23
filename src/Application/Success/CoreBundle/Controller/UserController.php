<?php

namespace Application\Success\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends Controller {

  /*public function newsfeedSearchAction() {
    $qb = $this->get('spy_timeline.query_builder');
    $page = $this->getRequest()->get('page', 1);
    $qb->setPage($page);
    $qb->setMaxPerPage(6);
    if($this->getRequest()->get('verb', '') != '') {
      $vars = $this->getRequest()->get('verb', '');
      $verbs = explode(',', $vars);
      $criterias = $qb->field('verb')->in($verbs);
      $qb->setCriterias($criterias);
    }
    
    $options = array(
      'filter'          => true,
      'paginate'        => false
    );
    $timeline = $qb->execute($options);

    if ($this->getRequest()->isXmlHttpRequest()) {

      $json = json_encode(array(
          'response' => $this->renderView('WebBundle:Frontend/Timeline/response:list.html.twig', array('timeline' => $timeline)), 
          'haveToPaginate' => (count($timeline) == 6),
          'href' => ( $this->get('router')->generate('core_newsfeed_search', array('page' => $page + 1, 'verb' => $this->getRequest()->get('verb', ''))) )
      ));

      $response = new Response($json, 200, array('Content-Type' => 'application/json'));

      return $response;

    } else {    
    
      $response = $this->render('WebBundle:Frontend/Timeline:search.html.twig', array(
          'timeline' => $timeline
      ));

      return $response;
    }
  }*/

  public function newsfeedAction() {
    $response = $this->render('WebBundle:Frontend/Timeline:timeline.html.twig', array(
        'timeline' => array()
    ));
    //$response->setSharedMaxAge(300);

    return $response;
    /*
    $qb = $this->get('spy_timeline.query_builder');
    $page = $this->getRequest()->get('page', 1);
    $qb->setPage($page);
    $qb->setMaxPerPage(6);
    $options = array(
      'filter'          => true,
      'paginate'        => false
    );
    $timeline = $qb->execute($options);

    if ($this->getRequest()->isXmlHttpRequest()) {

      $json = json_encode(array(
          'response' => $this->renderView('WebBundle:Frontend/Timeline/response:list.html.twig', array('timeline' => $timeline)), 
          'haveToPaginate' => (count($timeline) == 6),
          'href' => ( $this->get('router')->generate('core_newsfeed', array('page' => $page + 1) ) )
      ));

      $response = new Response($json, 200, array('Content-Type' => 'application/json'));

      return $response;

    } else {    
    
      $response = $this->render('WebBundle:Frontend/Timeline:timeline.html.twig', array(
          'timeline' => $timeline
      ));
      $response->setSharedMaxAge(300);

      return $response;
    }*/

  }
  
  public function indexAction() { 
    // Obtenemos el usuario
    $user = $this->getUser();
    //Obtenemos numero de pagina
    $page = $this->getRequest()->get('page', 1);
    
    if(!is_null($this->getRequest()->get('o', null))){
      $this->get('success.repository.user')->setDefaultFilters($this->getRequest()->get('o'), 'ASC');
    }
    
    // Obtengo las novedades
    $this->get('success.repository.user')->setMaxPerPage(32);
    $users = $this->get('success.repository.user')->getList($page, (is_null($user) ? null : $user->getId()));

    if ($this->getRequest()->isXmlHttpRequest()) {

      $json = json_encode(array(
          'response' => $this->renderView('WebBundle:Frontend/Usuario/response:list.html.twig', array('users' => $users)), 
          'haveToPaginate' => (count($users) == 32),
          'href' => ( $this->get('router')->generate('web_usuarios', array('page' => $page + 1)) )
      ));

      $response = new Response($json, 200, array('Content-Type' => 'application/json'));
      
      return $response;

    } else {
      $response = $this->render('WebBundle:Frontend/Usuario:index.html.twig', array(
          'users' => $users,
          'max_per_page' => $this->get('success.repository.user')->getMaxPerPage()
      ));
      //$response->setSharedMaxAge(300);
      
      return $response;
    }
  }
  
  public function relationAction($user) {
    return $this->render('WebBundle:Frontend/Usuario:_esi_buttons.html.twig', array('user' => $user));
  }  

  /*public function searchAction() {
    // Inyectamos el Location
    //$this->get('success.repository.user')->setLocation($this->get('success.core.location'));    
    // Creamos la QueryBuilder
    $qb =  $this->get('success.repository.user')->getQueryBuilder( (!is_null($this->getUser()) ? $this->getUser()->getId() : null) );
    // Instanciamos el manejador
    $service = $this->get('success.user.filter');
    // Cantidad de items por pagina
    $service->setMaxPerPage(24);
    // Inyectamos el Request
    $service->setRequest($this->get('request'));
    // Seteamos QueryBuilder
    $service->setQueryBuilder($qb);
    // Seteamos el orden
    $service->setOrderBy('lastLogin', 'DESC');
    // Obtenemos el paginador
    $pager = $service->getPager();

    if ($this->getRequest()->isXmlHttpRequest()) {

      $response = json_encode(array(
          'response' => $this->renderView('WebBundle:Frontend/Usuario/response:list.html.twig', array('users' => $service->getResults())), 
          'haveToPaginate' => $pager->hasNextPage(),
          'href' => $this->get('router')->generate('core_users_search', array(
                    'filter' => array_merge(
                      $service->getFilterParameters(), 
                      array('_page' => ($pager->hasNextPage() ? $pager->getNextPage() : 1))
                    )))
      ));

      return new Response($response, 200, array('Content-Type' => 'application/json'));      
      
    } else {

      $respuesta = $this->render('WebBundle:Frontend/Usuario:search.html.twig', array(
          'users'     => $service->getResults(), 
          'manager'   => $service
      ));

      return $respuesta;

    }
  }*/
  
  public function showAction($username) {
    $user = $this->get('fos_user.user_manager')->findUserByUsername($username);
    if(!$user)
      throw new NotFoundHttpException('El usuario "' . $username . '" no se ha encontrado.');
    
    $response = $this->render('WebBundle:Frontend/Usuario:show.html.twig', array(
        'user' => $user
    ));
    
    $response->setSharedMaxAge(600);
    
    return $response;
  }

  /*public function galleryAction($galleryId) {
    $gallery = $this->container->get('success.repository.gallery')->getGallery($galleryId);
    
    $images = $this->container->get('success.repository.galleryHasMedia')->getListByGallery($galleryId);
    
    $response = $this->render('WebBundle:Frontend/Usuario:gallery.html.twig', array('gallery' => $gallery, 'images' => $images));

    $response->setSharedMaxAge(600);

    return $response;
  }

  public function relationStatusAction($user) {
    $response = $this->render('WebBundle:Frontend/Usuario:_esi_buttons_status.html.twig', array('user' => $user));

    $response->setPrivate();

    return $response;    
  }

  private function addRelationStatus($users) {
    // Si no hay usuario logueado retorno
    if(is_null($this->getUser())) return $users;
    
    $ids = array();
    foreach($users as $user) {
      $ids[] = (int)$user['id'];
    }
    // Obtenemos los status
    $status_ids = $this->normalize($this->container->get('success.relation.manager')->getStatusOf($this->getUser()->getId(), $ids));

    foreach($users as &$user) {
      $user['status'] = null;
      if(in_array((int)$user['id'], $status_ids)){        
        $user['status'] = 'follow';
      }
    }

    return $users;
  }
  
  private function normalize($relations) {
    $result = array();
    foreach($relations as $relation) {
      $result[] = (int)$relation['user2_id'];
    }
    return $result;
  }*/
}
