<?php

namespace Application\Success\CoreBundle\Entity;

/**
 * EventoHasVideo
 */
class EventoHasVideo {

  protected $id;

  protected $media;

  protected $evento;

  protected $position;
  
  protected $avatar;  

  public function __construct() {
    $this->position = 0;
    $this->avatar = false;
  }

  public function __toString() {
    return (string) $this->getEvento() . ' | ' . $this->getMedia();
  }

  public function getId() {
    return $this->id;
  }

  public function setPosition($position) {
    $this->position = $position;

    return $this;
  }

  public function getPosition() {
    return $this->position;
  }

  /**
   * Set media
   *
   * @param \Application\Success\MediaBundle\Entity\Media $media
   * @return EventoHasVideo
   */
  public function setMedia(\Application\Success\MediaBundle\Entity\Media $media = null) {
    $this->media = $media;

    return $this;
  }

  /**
   * Get media
   *
   * @return \Application\Success\MediaBundle\Entity\Media 
   */
  public function getMedia() {
    return $this->media;
  }

  /**
   * Set evento
   *
   * @param \Application\Success\MediaBundle\Entity\Evento $evento
   * @return EventoHasVideo
   */
  public function setEvento(\Application\Success\CoreBundle\Entity\Evento $evento) {
    $this->evento = $evento;

    return $this;
  }

  /**
   * Get evento
   *
   * @return \Application\Success\CoreBundle\Entity\Evento
   */
  public function getEvento() {
    return $this->evento;
  }
  
  public function setAvatar($avatar) {
    $this->avatar = $avatar;

    return $this;
  }

  public function getAvatar() {
    return $this->avatar;
  }  

}