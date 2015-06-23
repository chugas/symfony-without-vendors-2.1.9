<?php

namespace Application\Success\UserBundle\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Controller\ProfileController as BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends BaseController {

  public function listMediasAction() {
    $user = $this->getUser();
    if (!is_object($user) || !$user instanceof UserInterface) {
      throw new AccessDeniedException('This user does not have access to this section.');
    }
    $max_per_page = 5;
    $page = $this->container->get('request')->get('page', 1);
    $media_provider = $this->container->get('request')->get('provider', 'youtubes');
    if($media_provider == 'youtubes') {
      $collection = $user->getYoutubes()->slice(($page-1)*$max_per_page, $max_per_page);
      $count = $user->getYoutubes()->count();
    } else if($media_provider == 'songs') {
      $collection = $user->getSongs()->slice(($page-1)*$max_per_page, $max_per_page);
      $count = $user->getSongs()->count();      
    } else {
      return new Response(json_encode(array('nok')), 200, array('Content-Type' => 'application/json'));
    }
    $json = json_encode(array(
      'response' => $this->container->get('templating')->render('SuccessUserBundle:Profile/response:' . $media_provider . '.html.twig', array('collection' => $collection)), 
      'haveToPaginate' => ($count > $page*$max_per_page),
      'href' => ( $this->container->get('router')->generate('web_usuarios_medias', array('page' => $page + 1, 'provider' => $media_provider)) )
    ));

    return new Response($json, 200, array('Content-Type' => 'application/json'));
  }

  
  /*public function createGalleryAction() {
    $name = $this->container->get('request')->get('gallery_name', 'Default');
    
    $gallery = $this->container->get('sonata.media.manager.gallery')->create();
    $user = $this->getUser();

    if(!is_null($user)) {
      $gallery->setName($name);
      $gallery->setContext('users');
      $gallery->setEnabled(true);
      $gallery->setDefaultFormat('');
      $gallery->setUser($user);
      $this->container->get('sonata.media.manager.gallery')->save($gallery, true);
      
      $template = $this->container->get('templating')->render('SuccessUserBundle:Profile/media:_template_album.html.twig', array('gallery' => $gallery));
      $result = array('response' => 'ok', 'template' => $template);
      
      return new Response(json_encode($result), 200, array('Content-Type' => 'application/json'));
    }

    return new Response(json_encode(array('response' => 'nok')), 200, array('Content-Type' => 'application/json'));
  }
  
  public function renameGalleryAction($galleryId) {
    $name = $this->container->get('request')->get('name');
    
    $gallery = $this->container->get('sonata.media.manager.gallery')->find($galleryId);
    $user = $this->getUser();

    if(!is_null($user) && $user->getId() == $gallery->getUser()->getId()) {
      // Si es una galeria de el
      $gallery->setName($name);
      $this->container->get('sonata.media.manager.gallery')->save($gallery, true);
      return new Response(json_encode(array('ok')), 200, array('Content-Type' => 'application/json'));      
    }
    return new Response(json_encode(array('nok')), 200, array('Content-Type' => 'application/json'));
  }  

  public function galleryAction($gallery_id) {
    // Obtenemos la galeria de id $gallery_id
    $gallery = $this->container->get('success.repository.gallery')->getGallery($gallery_id);
    
    $images = $this->container->get('success.repository.galleryHasMedia')->getListByGallery($gallery_id);

    $response = $this->container->get('templating')->renderResponse('SuccessUserBundle:Profile:gallery.html.twig', array('gallery' => $gallery, 'images' => $images));

    $response->setPrivate();

    return $response;
  }

  public function avatarGalleryAction($galleryId) {
    // Obtenemos la galeria de id $gallery_id
    $gallery = $this->container->get('success.repository.gallery')->getGallery($galleryId);
    
    $response = $this->container->get('templating')->renderResponse('SuccessUserBundle:Profile/media:_template_album_li.html.twig', array('gallery' => $gallery));

    $response->setPrivate();

    return $response;
  }*/

  public function getUser() {
    if (null === $token = $this->container->get('security.context')->getToken()) {
      return null;
    }

    if (!is_object($user = $token->getUser())) {
      return null;
    }

    return $user;
  }

}
