<?php

namespace Application\Success\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request as Request;
use Symfony\Component\HttpFoundation\Response as Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class OAuthController extends Controller {

  private function setAccessToken($service, $accessToken) {
    $session = $this->container->get('session');
    // save in session
    $session->set('_kbs_oauth.connect_confirmation.' . $service, $accessToken);
  }

  private function getAccessToken($service) {
    $session = $this->container->get('session');
    return $session->get('_kbs_oauth.connect_confirmation.' . $service, null);
  }

  private function getResourceOwnerByName($name) {
    if ($name == 'yahoo') {
      return $this->container->get('kbs_oauth.resource_owners.yahoo');
    }
    if ($name == 'google') {
      return $this->container->get('kbs_oauth.resource_owners.google');
    }
    
    $ownerMap = $this->container->get('hwi_oauth.resource_ownermap.' . $this->container->getParameter('hwi_oauth.firewall_name'));
    $resourceOwner = $ownerMap->getResourceOwnerByName($name);

    return $resourceOwner;
  }

  public function renderLoginUrlAction(Request $request, $service) {
    $router = $this->container->get('router');
    $callBackUrl = $router->generate('oauth_callback_handler', array('service' => $service), true);
    $yahooLoginUrl = $this->container->get('hwi_oauth.security.oauth_utils')->getAuthorizationUrl($request, $service, $callBackUrl);

    return new Response($yahooLoginUrl);
  }

  public function callbackHandlerAction(Request $request, $service) {
    $router = $this->container->get('router');
    // Get the data from the resource owner
    $resourceOwner = $this->getResourceOwnerByName($service);
    if (empty($resourceOwner)) {
      throw new NotFoundHttpException();
    }

    if ($resourceOwner->handles($request)) {
      $accessToken = $resourceOwner->getAccessToken(
              $request, $router->generate('oauth_callback_handler', array('service' => $service), true)
      );

      // save in session
      $this->setAccessToken($service, $accessToken);
    }

    return $this->render('UserBundle:Invitation:loading.html.twig', array());
  }

  public function getYahooContactsAction(Request $request) {
    $accessToken = $this->getAccessToken('facebook');
    var_dump($accessToken);
    die();
    if (empty($accessToken)) {
      return new JsonResponse(array(
                  'status' => false,
                  'message' => ''
              ));
    }

    $resourceOwner = $this->getResourceOwnerByName('yahoo');
    $contacts = $resourceOwner->getContacts($accessToken);
    print_r($contacts);
    die;
  }
  
  public function getGoogleContactsAction(Request $request){
    $resourceOwner = $this->getResourceOwnerByName('google');
    $service = 'google';
    if ($resourceOwner->handles($request)) {
      $accessToken = $resourceOwner->getAccessToken(
              $request, $this->container->get('router')->generate('google_contacts', array(), true)
      );

      // save in session
      $this->setAccessToken($service, $accessToken);
    }

    if (empty($accessToken)) {
      return new JsonResponse(array(
                  'status' => false,
                  'message' => ''
              ));
    }

    
    $contacts = $resourceOwner->getContacts($accessToken);
    print_r($contacts);
    die;
  }

}