<?php

/**
 * This file is part of the <name> project.
 *
 * (c) <yourname> <youremail>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Success\UserBundle\Entity;

use Sonata\UserBundle\Entity\BaseUser as BaseUser;
use Success\InviteBundle\Model\RefererInterface;
use Application\Success\CoreBundle\Util\Util;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * This file has been generated by the Sonata EasyExtends bundle ( http://sonata-project.org/bundles/easy-extends )
 *
 * References :
 *   working with object : http://www.doctrine-project.org/projects/orm/2.0/docs/reference/working-with-objects/en
 *
 * @author <yourname> <youremail>
 */
//implements RefererInterface 
class User extends BaseUser {

  /**
   * @var integer $id
   */
  protected $id;
  protected $isProductora;
  protected $isDj;
  protected $isVj;
  protected $avatar;
  protected $song;
  protected $youtube;
  protected $youtubes;
  protected $songs;
  
  public function __construct() {
    parent::__construct();
    $this->youtubes = new ArrayCollection();
    $this->songs = new ArrayCollection();     
  }

  /**
   * Get id
   *
   * @return integer $id
   */
  public function getId() {
    return $this->id;
  }

  public function setIsProductora($is_productora){
    $this->isProductora = $is_productora;
    
    return $this;
  }
  
  
  public function getIsProductora() {
    return $this->isProductora;
  }

  public function setIsDj($is_dj){
    $this->isDj = $is_dj;
    
    return $this;
  }
  
  public function getIsDj() {
    return $this->isDj;
  }
  
  public function setIsVj($is_vj){
    $this->isVj = $is_vj;
    
    return $this;
  }  

  public function getIsVj() {
    return $this->isVj;
  }

  public function getSlug() {
    return Util::getSlug($this->usernameCanonical);
  }

  public function __toString() {
    return $this->getFirstname() . ' ' . $this->getLastname();
  }
  
  public function haveToComplete(){
    return true;
  }
  
  public function setAvatar($avatar) {
    $this->avatar = $avatar;

    return $this;
  }

  public function getAvatar() {
    return $this->avatar;
  }

  public function setYoutube($youtube) {
    $this->youtube = $youtube;

    return $this;
  }

  public function getYoutube() {
    return $this->youtube;
  }
  
  public function setSong($song) {
    $this->song = $song;

    return $this;
  }

  public function getSong() {
    return $this->song;
  }

  public function addYoutube(\Application\Success\MediaBundle\Entity\Media $youtube) {
    $this->youtubes[] = $youtube;

    return $this;
  }

  /**
   * Remove youtubes
   *
   * @param \Application\Success\MediaBundle\Entity\Media $youtube
   */
  public function removeYoutube(\Application\Success\MediaBundle\Entity\Media $youtube) {
    $this->youtubes->removeElement($youtube);
  }

  /**
   * Get youtubes
   *
   * @return \Doctrine\Common\Collections\Collection 
   */
  public function getYoutubes() {
    return $this->youtubes;
  }
  
  public function addSong(\Application\Success\MediaBundle\Entity\Media $song) {
    $this->songs[] = $song;

    return $this;
  }

  /**
   * Remove songs
   *
   * @param \Application\Success\MediaBundle\Entity\Media $songs
   */
  public function removeSong(\Application\Success\MediaBundle\Entity\Media $song) {
    $this->songs->removeElement($song);
  }

  /**
   * Get songs
   *
   * @return \Doctrine\Common\Collections\Collection 
   */
  public function getSongs() {
    return $this->songs;
  }
  
}
