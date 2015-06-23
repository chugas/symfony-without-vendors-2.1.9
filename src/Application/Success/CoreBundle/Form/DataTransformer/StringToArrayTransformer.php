<?php

namespace Application\Success\CoreBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class StringToArrayTransformer implements DataTransformerInterface {

  /**
   * Transforms an array to a string.
   *
   * @param  Array|null $array
   * @return string
   */
  public function transform($array) {
    if (null === $array) {
      return null;
    }
    
    if(!is_array($array)){
      throw new UnexpectedTypeException($array, 'Not Array');      
    }
    
    $r = implode(",", $array);

    return $r;
  }

  /**
   * Transforms a string to an array.
   *
   * @param  string $string
   * @return Array|null
   */
  public function reverseTransform($string) {
    if (null === $string || '' === $string) {
      return null;
    }

    if (!is_string($string)) {
      throw new UnexpectedTypeException($string, 'Not String');
    }

    $r = explode(",", str_replace(' ', '', $string));

    return $r;
  }

}

?>