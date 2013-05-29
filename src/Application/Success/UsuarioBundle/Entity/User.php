<?php

namespace Application\Success\UsuarioBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\Common\Collections\ArrayCollection;
use Application\Success\PortalBundle\Util\Util;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Ibrows\Bundle\NewsletterBundle\Model\User\MandantUserInterface;

/**
 * User entity
 *
 * @ORM\Entity(repositoryClass="Application\Success\UsuarioBundle\Entity\Repository\UserRepository")
 * @ORM\Table(name="success_users")
 * @ORM\HasLifecycleCallbacks
 */
class User extends BaseUser implements MandantUserInterface {

  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * @ORM\Column(name="name", type="string", length=32, nullable=true)
   * @Assert\NotBlank()
   */
  protected $name;

  /**
   * @ORM\Column(name="surname", type="string", length=32, nullable=true)
   * @Assert\NotBlank()
   */
  protected $surname;

  // @Assert\Country()
  /**
   * @ORM\Column(name="country", type="string", length=2, nullable=true)
   */
  protected $country;

  /**
   * @ORM\Column(name="city", type="string", length=63, nullable=true)
   */
  protected $city;

  /**
   * @ORM\Column(name="phone", type="string", length=15, nullable=true)
   * @Assert\MinLength(limit = 8)
   */
  protected $phone;

  /**
   * @ORM\Column(name="gender", type="string", length=1, nullable=true)
   * @Assert\Choice(choices = {"m", "f", ""})
   */
  protected $gender;

  /**
   * @ORM\Column(name="birthday", type="date", nullable=true)
   * @Assert\Date()
   */
  protected $birthday;

  /**
   * @ORM\Column(name="description", type="string", length=255, nullable=true)
   */
  protected $description;

  /**
   * @ORM\Column(name="avatar", type="string", length=255, nullable=true)
   */
  protected $avatar;

  /**
   * @Assert\Image(maxSize="6000000")
   */
  protected $file;

  /**
   * @ORM\ManyToMany(targetEntity="Application\Success\UsuarioBundle\Entity\Group")
   * @ORM\JoinTable(name="success_users_groups",
   *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
   *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
   * )
   */
  protected $groups;

  /**
   *
   * @ORM\Column(name="is_columnista", type="boolean", options={ "default"= false }, nullable=true )
   */
  protected $is_columnista;

  /**
   *
   * @ORM\Column(name="receive_news", type="boolean", options={ "default"= false }, nullable=true )
   */
  protected $receive_news;

  /**
   *
   * @ORM\ManyToOne(targetEntity="Application\Success\CompanyBundle\Entity\Occupation")
   * @ORM\JoinColumn(name="occupation_id", referencedColumnName="id")
   */
  protected $occupation;

  /**
   *
   * @ORM\ManyToOne(targetEntity="Application\Success\CompanyBundle\Entity\Company")
   * @ORM\JoinColumn(name="company_work_id", referencedColumnName="id")
   */
  protected $company_work;
  
  /**
   *
   * @ORM\ManyToOne(targetEntity="Application\Success\CompanyBundle\Entity\Company")
   * @ORM\JoinColumn(name="company_owner_id", referencedColumnName="id")
   */
  protected $company_owner;

  /**
   *
   * @ORM\OneToMany(targetEntity="Application\Success\PortalBundle\Entity\News", mappedBy="columnista")
   * 
   */
  protected $news;

  /**
   * @ORM\Column(type="string", nullable=true)
   */
  protected $mandant;

  /**
   * Construct a new user
   */
  public function __construct() {
    parent::__construct();
    $this->groups = new ArrayCollection();
    $this->news = new ArrayCollection();
  }

  /**
   * Get id
   *
   * @return int
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set id
   *
   * @param int $id
   */
  public function setId($id) {
    $this->id = $id;
  }

  /**
   * @return string
   */
  public function getMandant() {
    return $this->mandant;
  }

  /**
   * Gets the groupIds for the user.
   *
   * @return array
   */
  public function getGroupIds() {
    $groups = $this->groups;

    $groupIds = array();
    if (count($groups) > 0) {
      /* @var $group GroupInterface */
      foreach ($groups as $group) {
        $groupIds[] = $group->getId();
      }
    }

    return $groupIds;
  }

  /**
   * Gets the groups the user belongs to.
   *
   * @return ArrayCollection
   */
  public function getGroups() {
    return $this->groups;
  }

  /**
   * Set country
   *
   * @param string $country
   * @return User
   */
  public function setCountry($country) {
    $this->country = $country;

    return $this;
  }

  /**
   * Get country
   *
   * @return string 
   */
  public function getCountry() {
    return $this->country;
  }

  /**
   * Set city
   *
   * @param string $city
   * @return User
   */
  public function setCity($city) {
    $this->city = $city;

    return $this;
  }

  /**
   * Get city
   *
   * @return string 
   */
  public function getCity() {
    return $this->city;
  }

  /**
   * Set phone
   *
   * @param string $phone
   * @return User
   */
  public function setPhone($phone) {
    $this->phone = $phone;

    return $this;
  }

  /**
   * Get phone
   *
   * @return string 
   */
  public function getPhone() {
    return $this->phone;
  }

  /**
   * Set gender
   *
   * @param string $gender
   * @return User
   */
  public function setGender($gender) {
    $this->gender = $gender;

    return $this;
  }

  /**
   * Get gender
   *
   * @return string 
   */
  public function getGender() {
    return $this->gender;
  }

  /**
   * Set birthday
   *
   * @param \DateTime $birthday
   * @return User
   */
  public function setBirthday($birthday) {
    $this->birthday = $birthday;

    return $this;
  }

  /**
   * Get birthday
   *
   * @return \DateTime 
   */
  public function getBirthday() {
    return $this->birthday;
  }

  /**
   * Set description
   *
   * @param string $description
   * @return User
   */
  public function setDescription($description) {
    $this->description = $description;

    return $this;
  }

  /**
   * Get description
   *
   * @return string 
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * Set avatar
   *
   * @param string $avatar
   * @return User
   */
  public function setAvatar($avatar) {
    $this->avatar = $avatar;

    return $this;
  }

  /**
   * Get avatar
   *
   * @return string 
   */
  public function getAvatar() {
    return $this->avatar;
  }

  public function setFile($file) {
    if (!empty($file)) {
      $this->avatar = 'changed';
    }
    $this->file = $file;
    return $this;
  }

  /**
   * Get file
   *
   * @return string
   */
  public function getFile() {
    return $this->file;
  }

  /**
   * Set is_columnista
   *
   * @param boolean $isColumnista
   * @return User
   */
  public function setIsColumnista($isColumnista) {
    $this->is_columnista = $isColumnista;

    return $this;
  }

  /**
   * Get is_columnista
   *
   * @return boolean 
   */
  public function getIsColumnista() {
    return $this->is_columnista;
  }

  /**
   * Set occupation
   *
   * @param \Application\Success\CompanyBundle\Entity\Occupation $occupation
   * @return User
   */
  public function setOccupation(\Application\Success\CompanyBundle\Entity\Occupation $occupation = null) {
    $this->occupation = $occupation;

    return $this;
  }

  /**
   * Get occupation
   *
   * @return \Application\Success\CompanyBundle\Entity\Occupation 
   */
  public function getOccupation() {
    return $this->occupation;
  }

  /**
   * Set company
   *
   * @param \Application\Success\CompanyBundle\Entity\Company $company
   * @return User
   */
  public function setCompanyWork(\Application\Success\CompanyBundle\Entity\Company $company = null) {
    $this->company_work = $company;

    return $this;
  }

  /**
   * Get company
   *
   * @return \Application\Success\CompanyBundle\Entity\Company 
   */
  public function getCompanyWork() {
    return $this->company_work;
  }
  
  /**
   * Set company
   *
   * @param \Application\Success\CompanyBundle\Entity\Company $company
   * @return User
   */
  public function setCompanyOwner(\Application\Success\CompanyBundle\Entity\Company $company = null) {
    $this->company_owner = $company;

    return $this;
  }

  /**
   * Get company
   *
   * @return Company 
   */
  public function getCompanyOwner() {
    return $this->company_owner;
  }

  /**
   * Set name
   *
   * @param string $name
   * @return User
   */
  public function setName($name) {
    $this->name = $name;

    return $this;
  }

  /**
   * Get name
   *
   * @return string 
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Set surname
   *
   * @param string $surname
   * @return User
   */
  public function setSurname($surname) {
    $this->surname = $surname;

    return $this;
  }

  /**
   * Get surname
   *
   * @return string 
   */
  public function getSurname() {
    return $this->surname;
  }

  /**
   * Set receive_news
   *
   * @param boolean $receiveNews
   * @return User
   */
  public function setReceiveNews($receiveNews) {
    $this->receive_news = $receiveNews;

    return $this;
  }

  /**
   * Get receive_news
   *
   * @return boolean 
   */
  public function getReceiveNews() {
    return $this->receive_news;
  }

  /**
   * Add news
   *
   * @param \Application\Success\PortalBundle\Entity\News $news
   * @return User
   */
  public function addNew(\Application\Success\PortalBundle\Entity\News $news) {
    $this->news[] = $news;

    return $this;
  }

  /**
   * Remove news
   *
   * @param \Application\Success\PortalBundle\Entity\News $news
   */
  public function removeNew(\Application\Success\PortalBundle\Entity\News $news) {
    $this->news->removeElement($news);
  }

  /**
   * Get news
   *
   * @return \Doctrine\Common\Collections\Collection 
   */
  public function getNews() {
    return $this->news;
  }

  public function getAbsolutePath() {
    return null === $this->avatar ? null : $this->getUploadRootDir() . '/' . $this->avatar;
  }

  public function getWebPath() {
    return null === $this->avatar ? null : '/' . $this->getUploadDir() . '/' . $this->avatar;
  }

  public function getUploadRootDir() {
    return __DIR__ . '/../../../../../web/' . $this->getUploadDir();
  }

  protected function getUploadDir() {
    // get rid of the __DIR__ so it doesn't screw when displaying uploaded doc/avatar in the view.
    return 'uploads/columnistas';
  }

  /**
   * @ORM\PrePersist()
   * @ORM\PreUpdate()
   */
  public function preUpload() {
    if (null !== $this->file) {
      // do whatever you want to generate a unique name
      $this->avatar = uniqid() . '.' . $this->file->guessExtension();
    }
  }

  /**
   * @ORM\PostPersist()
   * @ORM\PostUpdate()
   */
  public function upload() {
    if (null === $this->file) {
      return;
    }

    // you must throw an exception here if the file cannot be moved
    // so that the entity is not persisted to the database
    // which the UploadedFile move() method does automatically
    $this->file->move($this->getUploadRootDir(), $this->avatar);

    unset($this->file);
  }

  /**
   * @ORM\PostRemove()
   */
  public function removeUpload() {
    if (!$file = $this->getAbsolutePath()) {
      return;
    }
    if (is_file($file)) {
      unlink($file);
    }
  }

  public function getSlug() {
    return Util::getSlug($this->name . ' ' . $this->surname);
  }

  public function __toString() {
    return $this->name . ' ' . $this->surname;
  }

  public function haveToComplete() {
    return $this->description == "";
  }

}