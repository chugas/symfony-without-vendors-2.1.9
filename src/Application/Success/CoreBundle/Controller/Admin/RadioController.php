<?php

namespace Application\Success\CoreBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RadioController extends Controller {

  public function indexAction() {
    $storage = $this->get('success.admin.radio');
    
    if($this->getRequest()->isMethod('POST')){
      $blocks = $this->getRequest()->get('config');
      foreach($blocks as $context => $block){
        foreach($block as $param => $value){
          $storage->set($context, $param, $value);
        }
      }
    }
    
    $admin_pool = $this->get('sonata.admin.pool');

    return $this->render('WebBundle:Admin/Radio:index.html.twig', array(
        'blocks' => $storage->getAll(),
        'admin_pool' => $admin_pool,
        'name' => 'Radio'
    ));
  }

}
