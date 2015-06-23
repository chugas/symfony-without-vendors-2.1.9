<?php

namespace Application\Success\UserBundle\Form\Handler;

use FOS\UserBundle\Form\Handler\ProfileFormHandler as BaseHandler;

class ProfileFormHandler extends BaseHandler {

  public function setForm($form){
    $this->form = $form;
  }
  
}
