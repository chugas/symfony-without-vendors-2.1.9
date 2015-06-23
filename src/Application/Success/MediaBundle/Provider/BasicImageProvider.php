<?php

namespace Application\Success\MediaBundle\Provider;

use Sonata\MediaBundle\CDN\CDNInterface;

class BasicImageProvider {

  private $cdn;
  private $pathGenerator;

  public function __construct(CDNInterface $cdn, $generator) {
    $this->cdn = $cdn;
    $this->pathGenerator = $generator;
  }

  public function generatePublicUrl($id, $path, $context) {
    $path = $this->getReferenceImage($id, $path, $context);
    return $this->cdn->getPath($path, null);
  }

  public function getReferenceImage($id, $path, $context) {
    return sprintf('%s/%s', $this->generatePath($id, $context), $path);
  }

  public function generatePath($id, $context) {
    return $this->pathGenerator->generatePath($id, $context);
  }

}
