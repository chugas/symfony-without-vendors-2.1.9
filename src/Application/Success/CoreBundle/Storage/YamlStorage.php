<?php
namespace Application\Success\CoreBundle\Storage;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Dumper;

class YamlStorage {

  protected $path;
  protected $parser;
  
  public function __construct($path) {
    $this->path = $path;
    $this->parser = new Parser();
  }
  
  public function getPath(){
    return $this->path;
  }
  
  public function getAll(){
    try {
      return $this->parser->parse(file_get_contents($this->path));
    }  catch (ParseException $e) {
      echo $e->getMessage();
      return null;
    }
  }
  
  public function get($context, $key){
    try {
      $info = $this->parser->parse(file_get_contents($this->path));
      $settings = $info[$context];
      return (array_key_exists($key, $settings) ? $settings[$key] : null);
    }  catch (ParseException $e) {
      return null;
    }
  }
  
  public function set($context, $key, $value){
    $info = $this->parser->parse(file_get_contents($this->path));
    
    $settings = $info[$context];
    $settings[$key] = $value;
    
    $info[$context] = $settings;
    
    $dumper = new Dumper();
    $yaml = $dumper->dump($info, 2);

    file_put_contents($this->path, $yaml);
  }

}





