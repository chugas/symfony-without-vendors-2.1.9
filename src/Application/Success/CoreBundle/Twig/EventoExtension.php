<?php

namespace Application\Success\CoreBundle\Twig;

//use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class EventoExtension extends \Twig_Extension {

  //private $container;
  private $repository_evento;
  private $templating;

  public function __construct($repository_evento, $templating) {
    //$this->container = $container;
    $this->templating = $templating;
    $this->repository_evento   = $repository_evento;
  }

  public function getName() {
    return 'core_utils';
  }
  
  public function getFunctions() {
    return array(
        'eventos_pasados'    => new \Twig_Function_Method($this, 'eventosPasados', array('is_safe' => array('html'))),
        'eventos_pasados_key'    => new \Twig_Function_Method($this, 'eventosPasadosKey', array('is_safe' => array('html')))        
    );
  }
  
  public function eventosPasados($limit){
    $eventos = $this->repository_evento->findPasadas($limit);

    return $this->templating->render('WebBundle:Frontend/Evento:eventos_pasados.html.twig', array('eventos' => $eventos));    
  }

  public function eventosPasadosKey($productora, $evento_id, $limit){
    $eventos = $this->repository_evento->findByProductora($productora->getId(), $evento_id, $limit);

    return $this->templating->render('WebBundle:Frontend/Evento:eventos_pasados_key.html.twig', array('eventos' => $eventos, 'productora' => $productora));
  }
}
