<?php

namespace Application\Success\CoreBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Oh\GoogleMapFormTypeBundle\Validator\Constraints as OhAssert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Comentarios
 * ------------
 * ATRIBUTO UNIT - symfony no provee manejo de enumerado. Hay que hacerlo manual
 * ALTER TABLE  `success_evento` CHANGE  `unit`  `unit` ENUM(  'UYU',  'USD' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL 
 * 
 */
class Evento {

  const UPLOAD_DIR = 'uploads/eventos';
  
  /**
   * Product id.
   *
   * @var mixed
   */
  protected $id;

  /**
   * Product name.
   *
   * @var string
   */
  protected $name;

  /**
   * Permalink for the event.
   * Used in url to access it.
   *
   * @var string
   */
  protected $slug;

  /**
   * Product description.
   *
   * @var string
   */
  protected $description;
  
  /**
   *
   * @var string
   */
  protected $address;

  protected $place;

  /**
   *
   * @var string
   */
  protected $price;
  
  protected $unit;

  /**************************************/
  // Atributos para ventas anticipadas
  protected $priceAnticipada;
  
  protected $validateAtAnticipada;
  
  protected $descriptionAnticipada;
  
  protected $sellJustrave;  
  /**************************************/
  
  protected $timeAt;

  /**
   * Deletion time.
   *
   * @var \DateTime
   */
  protected $deletedAt;

  /**
   *
   * @var float
   */
  protected $lat;
  
  /**
   *
   * @var float
   */
  protected $lng;
  
  protected $productora;
  
  protected $user;
  
  protected $lineup;
  
  protected $flyer;
  
  protected $file;
  
  protected $videos;
  
  protected $images;
  
  public function __construct() {
    $this->images = new ArrayCollection();
    $this->videos = new ArrayCollection();
    $this->lineup = array();
    $this->timeAt = new \DateTime('now');
    $this->unit = 'UYU';
    $this->lat = -34.87;
    $this->lng = -56.171;
  }

  public function getId() {
    return $this->id;
  }
  
  public function __toString() {
    return (string)$this->getName();
  }

  public function getName() {
    return $this->name;
  }

  public function setName($name) {
    $this->name = $name;

    return $this;
  }

  public function getSlug() {
    return $this->slug;
  }

  public function setSlug($slug) {
    $this->slug = $slug;

    return $this;
  }

  public function getDescription() {
    return $this->description;
  }

  public function setDescription($description) {
    $this->description = $description;

    return $this;
  }

  public function getUnit(){
    return $this->unit;
  }
  
  public function setUnit($unit){
    $this->unit = $unit;
  }
  
  public function getTimeAt() {
    return $this->timeAt;
  }

  //\DateTime
  public function setTimeAt($timeAt) {
    $this->timeAt = $timeAt;

    return $this;
  }

  public function isDeleted() {
    return null !== $this->deletedAt && new \DateTime() >= $this->deletedAt;
  }

  public function getDeletedAt() {
    return $this->deletedAt;
  }

  public function setDeletedAt(\DateTime $deletedAt) {
    $this->deletedAt = $deletedAt;

    return $this;
  }

  public function getAddress() {
    return $this->address;
  }

  public function setAddress($address) {
    $this->address = $address;

    return $this;
  }

  public function getPlace() {
    return $this->place;
  }

  public function setPlace($place) {
    $this->place = $place;

    return $this;
  }

  public function getPrice() {
    return $this->price;
  }

  public function setPrice($price) {
    $this->price = $price;

    return $this;
  }

  public function getPriceAnticipada() {
    return $this->priceAnticipada;
  }

  public function setPriceAnticipada($priceAnticipada) {
    $this->priceAnticipada = $priceAnticipada;

    return $this;
  }

  public function getValidateAtAnticipada() {
    return $this->validateAtAnticipada;
  }

  public function setValidateAtAnticipada($validateAtAnticipada) {
    $this->validateAtAnticipada = $validateAtAnticipada;

    return $this;
  }

  public function getSellJustrave() {
    return $this->sellJustrave;
  }

  public function setSellJustrave($sellJustrave) {
    $this->sellJustrave = $sellJustrave;

    return $this;
  }

  public function getDescriptionAnticipada() {
    return $this->descriptionAnticipada;
  }

  public function setDescriptionAnticipada($descriptionAnticipada) {
    $this->descriptionAnticipada = $descriptionAnticipada;

    return $this;
  }
  
  public function getLatitud() {
    return $this->lat;
  }

  public function setLatitud($lat) {
    $this->lat = $lat;

    return $this;
  }
  
  public function getLongitud() {
    return $this->lng;
  }

  public function setLongitud($lng) {
    $this->lng = $lng;

    return $this;
  }  

  public function setLatLng($latlng)
  {
      $this->setLatitud($latlng['lat']);
      $this->setLongitud($latlng['lng']);
      return $this;
  }

  /**
   * @OhAssert\LatLng()
   */
  public function getLatLng()
  {
    return array('lat' => $this->getLatitud(), 'lng' => $this->getLongitud());
  }
  
  public function getLineup(){
    return $this->lineup;
  }
  
  //array
  public function setLineup($lineup){
    $this->lineup = $lineup;
    
    return $this;
  }
  
  public function getProductora(){
    return $this->productora;
  }
  
  public function setProductora($productora){
    $this->productora = $productora;
    
    return $this;
  }
  
  public function addImage(\Application\Success\CoreBundle\Entity\EventoHasImage $eventoHasImage) {
    if(is_null($eventoHasImage)) return;
    
    $eventoHasImage->setEvento($this);

    $this->images[] = $eventoHasImage;
  }

  /**
   * Remove removeImage
   *
   * @param \Application\Success\CoreBundle\Entity\EventoHasImage $newsHasMedias
   */
  public function removeImage(\Application\Success\CoreBundle\Entity\EventoHasImage $eventoHasImage) {
    $this->images->removeElement($eventoHasImage);
  }

  /**
   * Get eventoHasImage
   *
   * @return \Doctrine\Common\Collections\Collection 
   */
  public function getImages() {
    return $this->images;
  }

  public function addVideo(\Application\Success\CoreBundle\Entity\EventoHasVideo $eventoHasVideo) {
    if(is_null($eventoHasVideo)) return;
    
    $eventoHasVideo->setEvento($this);

    $this->videos[] = $eventoHasVideo;
  }

  /**
   * Remove removeImage
   *
   * @param \Application\Success\CoreBundle\Entity\EventoHasVideo $eventoHasVideo
   */
  public function removeVideo(\Application\Success\CoreBundle\Entity\EventoHasVideo $eventoHasVideo) {
    $this->videos->removeElement($eventoHasVideo);
  }

  /**
   * Get eventoHasVideo
   *
   * @return \Doctrine\Common\Collections\Collection 
   */
  public function getVideos() {
    return $this->videos;
  }
  
  public function getFlyer(){
    return (empty($this->flyer) ? NULL : $this->flyer);
  }
  
  public function setFlyer($flyer){
      $this->flyer = $flyer;
      
      return $this;
  }
  
  ##################################################################
  // Subida de Flyer
  ##################################################################
  public function getAbsolutePath() {
    return null === $this->flyer ? null : $this->getUploadRootDir() . '/' . $this->flyer;
  }

  public function getWebPath() {
    return null === $this->flyer ? null : '/' . $this->getUploadDir() . '/' . $this->flyer;
  }

  public function getUploadRootDir() {
    return __DIR__ . '/../../../../../web/' . $this->getUploadDir();
  }

  protected function getUploadDir() {
    // get rid of the __DIR__ so it doesn't screw when displaying uploaded doc/image in the view.
    return self::UPLOAD_DIR;
  }
  
  public function preUpload() {
    if (null !== $this->file) {
      // do whatever you want to generate a unique name
      $this->flyer = uniqid() . '.' . $this->file->guessExtension();
    }
  }

  public function upload() {
    if (null === $this->file) {
      return;
    }

    // you must throw an exception here if the file cannot be moved
    // so that the entity is not persisted to the database
    // which the UploadedFile move() method does automatically
    $this->file->move($this->getUploadRootDir(), $this->flyer);

    unset($this->file);
  }

  public function removeUpload() {
    if (!$file = $this->getAbsolutePath()) {
      return;
    }
    if (is_file($file)) {
      unlink($file);
    }
  }
  
  public function setFile($file) {
    if (!empty($file)) {
      $this->flyer = 'changed';
    }
    $this->file = $file;
    return $this;
  }

  public function getFile() {
    return $this->file;
  }
  
  public function getUser() {
    return $this->user;
  }

  public function setUser(\Symfony\Component\Security\Core\User\UserInterface $user) {
    $this->user = $user;

    return $this;
  }  
  
}
?>
