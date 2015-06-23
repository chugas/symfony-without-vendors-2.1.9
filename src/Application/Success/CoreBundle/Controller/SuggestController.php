<?php

namespace Application\Success\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class SuggestController extends Controller {

  const DEFAULT_AVATAR = '/assets/frontend/images/assets/portada.jpg';

  protected $type;

  public function suggestAction() {
    $result = $this->suggestRequest('user');
    $response = new JsonResponse($result, 200, array());
    $response->setSharedMaxAge(600);

    return $response;
  }

  private function suggestRequest($type) {
    $request = $this->get('request');
    $q = $request->get('term');

    switch ($type) {
      case 'user':
        return $this->get('success.repository.user')->suggest($q);
        break;
      default: return array();
        break;
    }
  }

}
