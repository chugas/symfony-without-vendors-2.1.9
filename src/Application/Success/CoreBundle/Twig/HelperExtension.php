<?php

namespace Application\Success\CoreBundle\Twig;

class HelperExtension extends \Twig_Extension {

  private $registration;
  private $templating;

  public function __construct($templating, $registration) {
    $this->templating = $templating;
    $this->registration = $registration;
  }
  
  public function getName() {
    return 'success.helper';
  }

  public function getFunctions() {
    return array(
        'register_box'  => new \Twig_Function_Method($this, 'register_box', array('is_safe' => array('html')))
    );
  }

  public function register_box() {
    return $this->templating->render('WebBundle:Frontend/Usuario:register_form.html.twig', array('form' => $this->registration->createView()));
  }
}
