<?php

namespace Application\Success\MediaBundle\Provider;

use Gaufrette\Filesystem;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\CDN\CDNInterface;
use Sonata\MediaBundle\Generator\GeneratorInterface;
use Sonata\MediaBundle\Thumbnail\ThumbnailInterface;
use Buzz\Browser;
use Sonata\MediaBundle\Metadata\MetadataBuilderInterface;

use Sonata\MediaBundle\Provider\BaseProvider;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\FormBuilder;

use Symfony\Component\HttpFoundation\RedirectResponse;

class SoundCloudProvider extends BaseProvider {

  protected $browser;
  protected $metadata;

  /**
   * @param string                                           $name
   * @param \Gaufrette\Filesystem                            $filesystem
   * @param \Sonata\MediaBundle\CDN\CDNInterface             $cdn
   * @param \Sonata\MediaBundle\Generator\GeneratorInterface $pathGenerator
   * @param \Sonata\MediaBundle\Thumbnail\ThumbnailInterface $thumbnail
   * @param \Buzz\Browser                                    $browser
   */
  public function __construct($name, Filesystem $filesystem, CDNInterface $cdn, GeneratorInterface $pathGenerator, ThumbnailInterface $thumbnail, Browser $browser, MetadataBuilderInterface $metadata = null) {
    parent::__construct($name, $filesystem, $cdn, $pathGenerator, $thumbnail);

    $this->browser = $browser;
    $this->metadata = $metadata;
  }
  
  public function buildCreateForm(FormMapper $formMapper) {
    $formMapper->add('binaryContent', array(), array('type' => 'string'));
  }

  public function buildEditForm(FormMapper $formMapper) {
    $formMapper->add('name');
    $formMapper->add('enabled');
    $formMapper->add('authorName');
    $formMapper->add('cdnIsFlushable');
    $formMapper->add('description');
    $formMapper->add('copyright');
    $formMapper->add('binaryContent', array(), array('type' => 'string'));
  }

  public function buildMediaType(FormBuilder $formBuilder) {
    parent::buildMediaType($formBuilder);
    $formBuilder->add('binaryContent', 'text');
  }

  /**
   * @throws \RuntimeException
   *
   * @param \Sonata\MediaBundle\Model\MediaInterface $media
   * @param string                                   $url
   *
   * @return mixed
   */
  protected function getMetadata(MediaInterface $media, $url) {
    try {
      $response = $this->browser->get($url);
    } catch (\RuntimeException $e) {
      throw new \RuntimeException('Unable to retrieve the soundcloud information for :' . $url, null, $e);
    }

    $metadata = json_decode($response->getContent(), true);

    if (!$metadata) {
      throw new \RuntimeException('Unable to decode the soundcloud information for :' . $url);
    }

    return $metadata;
  }

  public function prePersist(MediaInterface $media) {
    if (!$media->getBinaryContent()) {

      return;
    }
    $url = sprintf('http://soundcloud.com/oembed?format=json&url=%s', $media->getProviderReference());
    $metadata = $this->getMetadata($media, $url);

    // store provider information
    $media->setProviderName($this->name);
    $media->setProviderReference($media->getBinaryContent());
    $media->setProviderMetadata($metadata);

    // update Media common field from metadata
    $media->setName($metadata['title']);
    $media->setDescription($metadata['description']);
    $media->setAuthorName($metadata['author_name']);
    $media->setHeight($metadata['height']);
    $media->setWidth($metadata['width']);
    $media->setContentType('audio/webm');
    $media->setProviderStatus(1);

    $media->setCreatedAt(new \Datetime());
    $media->setUpdatedAt(new \Datetime());
  }

  public function preUpdate(MediaInterface $media) {
    if (!$media->getBinaryContent()) {

      return;
    }

    $url = sprintf('http://soundcloud.com/oembed?format=json&url=%s', $media->getProviderReference());
    $metadata = $this->getMetadata($media, $url);

    $media->setProviderReference($media->getBinaryContent());
    $media->setProviderMetadata($metadata);
    $media->setHeight($metadata['height']);
    $media->setWidth($metadata['width']);
    $media->setProviderStatus(1);

    $media->setUpdatedAt(new \Datetime());
  }

  public function postUpdate(MediaInterface $media) {
    $this->postPersist($media);
  }

  public function postPersist(MediaInterface $media) {
    if (!$media->getBinaryContent()) {

      return;
    }

    $this->generateThumbnails($media);
  }

  public function getReferenceImage(MediaInterface $media) {
    return $media->getMetadataValue('thumbnail_url');
  }

  public function getReferenceFile(MediaInterface $media) {
    $key = $this->generatePrivateUrl($media, 'reference');

    // the reference file is remote, get it and store it with the 'reference' format
    if ($this->getFilesystem()->has($key)) {
      $referenceFile = $this->getFilesystem()->get($key);
    } else {
      $referenceFile = $this->getFilesystem()->get($key, true);
      $metadata = $this->metadata ? $this->metadata->get($media, $referenceFile->getName()) : array();
      $referenceFile->setContent($this->browser->get($this->getReferenceImage($media))->getContent(), $metadata);
    }

    return $referenceFile;
  }

  public function generatePublicUrl(MediaInterface $media, $format) {
    return $this->getCdn()->getPath(sprintf('%s/thumb_%d_%s.jpg', $this->generatePath($media), $media->getId(), $format
                    ), $media->getCdnIsFlushable());
  }

  /**
   * {@inheritdoc}
   */
  public function generatePrivateUrl(MediaInterface $media, $format) {
    return sprintf('%s/thumb_%d_%s.jpg', $this->generatePath($media), $media->getId(), $format
    );
  }

  protected function fixBinaryContent(MediaInterface $media) {
    if (!$media->getBinaryContent()) {
      return;
    }

    /* if (preg_match("/soundcloud.com\/(\d+)/", $media->getBinaryContent(), $matches)) {
      $media->setBinaryContent($matches[1]);
      } */
  }

  protected function doTransform(MediaInterface $media) {
    //$this->fixBinaryContent($media);

    if (!$media->getBinaryContent()) {
      return;
    }

    // store provider information
    $media->setProviderName($this->name);
    $media->setProviderReference($media->getBinaryContent());
    $media->setProviderStatus(MediaInterface::STATUS_OK);

    $this->updateMetadata($media, true);
  }

  /**
   * {@inheritdoc}
   */
  public function updateMetadata(MediaInterface $media, $force = false) {
    $url = sprintf('http://soundcloud.com/oembed?format=json&url=%s', $media->getProviderReference());

    try {
      $metadata = $this->getMetadata($media, $url);
    } catch (\RuntimeException $e) {
      $media->setEnabled(false);
      $media->setProviderStatus(MediaInterface::STATUS_ERROR);

      return;
    }

    // store provider information
    $media->setProviderMetadata($metadata);

    // update Media common fields from metadata
    if ($force) {
      $media->setName($metadata['title']);
      $media->setDescription($metadata['description']);
      $media->setAuthorName($metadata['author_name']);
    }

    $media->setHeight($metadata['height']);
    $media->setWidth($metadata['width']);
    $media->setContentType('audio/webm');
  }

  public function getHelperProperties(MediaInterface $media, $format) {
    // documentation : http://vimeo.com/api/docs/moogaloop
    /* $defaults = array(
      // (optional) Flash Player version of app. Defaults to 9 .NEW!
      // 10 - New Moogaloop. 9 - Old Moogaloop without newest features.
      'fp_version' => 10,
      // (optional) Enable fullscreen capability. Defaults to true.
      'fullscreen' => true,
      // (optional) Show the byline on the video. Defaults to true.
      'title' => true,
      // (optional) Show the title on the video. Defaults to true.
      'byline' => 0,
      // (optional) Show the user's portrait on the video. Defaults to true.
      'portrait' => true,
      // (optional) Specify the color of the video controls.
      'color' => null,
      // (optional) Set to 1 to disable HD.
      'hd_off' => 0,
      // Set to 1 to enable the Javascript API.
      'js_api' => null,
      // (optional) JS function called when the player loads. Defaults to vimeo_player_loaded.
      'js_onLoad' => 0,
      // Unique id that is passed into all player events as the ending parameter.
      'js_swf_id' => uniqid('vimeo_player_'),
      );

      $player_parameters = array_merge($defaults, isset($options['player_parameters']) ? $options['player_parameters'] : array());

      $params = array(
      'src' => http_build_query($player_parameters),
      'id' => $player_parameters['js_swf_id'],
      'frameborder' => isset($options['frameborder']) ? $options['frameborder'] : 0,
      'width' => isset($options['width']) ? $options['width'] : $media->getWidth(),
      'height' => isset($options['height']) ? $options['height'] : $media->getHeight(),
      ); */

    return array();
  }

  public function getDownloadResponse(MediaInterface $media, $format, $mode, array $headers = array()) {
    return new RedirectResponse($media->getProviderReference(), 302, $headers);
  }

}
