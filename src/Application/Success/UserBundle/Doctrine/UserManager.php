<?php
namespace Application\Success\UserBundle\Doctrine;

use FOS\UserBundle\Doctrine\UserManager as BaseUserManager;
use FOS\UserBundle\Model\UserInterface;

class UserManager extends BaseUserManager
{
  const SEPARATOR   = '-';
  const MAX_CHANCE  = 10;
  
  public function generateUsername(UserInterface $user){
    $name = str_replace(' ', self::SEPARATOR, strtolower($user->getFirstname()));
    $surname = str_replace(' ', self::SEPARATOR, strtolower($user->getLastname()));
    
    $username = $name . self::SEPARATOR . $surname;

    $time = 0;
    while($time < self::MAX_CHANCE && $this->available($username) === false){
      $token = rand(0, 999);
      $username = $name . self::SEPARATOR . $surname . self::SEPARATOR . $token;
      $time++;
    }
    
    if($time >= self::MAX_CHANCE){
      $user->setUsername($user->getEmail());
    }else {
      $user->setUsername($username);
    }
  }
  
  public function available($username){
    return ($this->findUserByUsername($username) === NULL);
  }

  public function getList($page = 1, $user_id){
    return $this->repository->getList($page, $user_id);
  }
}