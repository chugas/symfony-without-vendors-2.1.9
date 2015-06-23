<?php

namespace Application\Success\CoreBundle\Twig;

use Application\Success\CoreBundle\Storage\YamlStorage;

class RadioExtension extends \Twig_Extension {

  private $storage;
  
  public function __construct($path) {
    $this->storage = new YamlStorage($path);
  }

  public function getName() {
    return 'core_radio';
  }
  
  public function getFunctions() {
    return array(
      'radio_url'         => new \Twig_Function_Method($this, 'getUrl'),        
      'radio_description' => new \Twig_Function_Method($this, 'getDescription'),
      'promo_key'         => new \Twig_Function_Method($this, 'getPromoKey'),        
      'promo_description' => new \Twig_Function_Method($this, 'getPromoDescription')        
    );
  }
  
  public function getUrl(){
    return $this->storage->get('radio', 'url');
  }
  
  public function getDescription(){   
    return $this->storage->get('radio', 'description');
  }
  
  public function getPromoKey(){
    return $this->storage->get('promo', 'key');
  }
  
  public function getPromoDescription(){   
    return $this->storage->get('promo', 'description');
  }  
}
