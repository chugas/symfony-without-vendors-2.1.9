<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Application\Success\CoreBundle\OAuth;

use FOS\UserBundle\Model\UserInterface as FOSUserInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Loading and ad-hoc creation of a user by an OAuth sign-in provider account.
 *
 * @author Fabian Kiss <fabian.kiss@ymc.ch>
 */
class UserProvider extends FOSUBUserProvider
{
    /**
     * {@inheritDoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        try {
            return parent::loadUserByOAuthUserResponse($response);
        } catch (UsernameNotFoundException $e) {
            if (null !== $response->getEmail()) {
                $user = $this->userManager->findUserByUsernameOrEmail($response->getEmail());
            } /*else {
                $user = $this->userManager->findUserByUsername($response->getNickname());
            }*/

            if ($user) {
                return $this->updateUserByOAuthUserResponse($user, $response);
            }

            return $this->createUserByOAuthUserResponse($response);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        $this->updateUserByOAuthUserResponse($user, $response);

        $this->userManager->updateUser($user);
    }

    /**
     * Ad-hoc creation of user
     *
     * @param UserResponseInterface $response
     *
     * @return User
     */
    protected function createUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $user = $this->userManager->createUser();
        $this->updateUserByOAuthUserResponse($user, $response);
        
        // Parseamos otros atributos
        $providerName = $response->getResourceOwner()->getName();
        $parserFunction = 'parse'.ucfirst($providerName);
        $this->$parserFunction($user, $response);

        if (!$user->getUsername()) {
          $this->userManager->generateUsername($user);
        }
        
        // set random password to prevent issue with not nullable field & potential security hole
        $user->setPlainPassword(substr(sha1($response->getAccessToken()), 0, 10));

        $user->setEnabled(true);

        $this->userManager->updateUser($user);

        return $user;
    }

    /**
     * Attach OAuth sign-in provider account to existing user
     *
     * @param FOSUserInterface      $user
     * @param UserResponseInterface $response
     *
     * @return FOSUserInterface
     */
    protected function updateUserByOAuthUserResponse(FOSUserInterface $user, UserResponseInterface $response)
    {
        $providerName = $response->getResourceOwner()->getName();
        $providerNameSetter = 'set'.ucfirst($providerName).'Uid';
        $user->$providerNameSetter($response->getUsername());

        return $user;
    }
    
    protected function parseFacebook(FOSUserInterface $user, UserResponseInterface $response){
      $data = $response->getResponse();
      $user->setFacebookData($data);
      $user->setFacebookName($data['username']);

      if (null !== $email = $response->getEmail()) {
        $user->setEmail($email);
      } else {
        $user->setEmail($data['username'] . '@justrave.com');
      }

      $user->setFirstname($data['first_name']);
      $user->setLastname($data['last_name']);
      $user->setGender($data['gender'] == 'male' ? 'm' : ($data['gender'] == 'female' ? 'f' : 'u') );

      return $user;
    }
    
    protected function parseGoogle(FOSUserInterface $user, UserResponseInterface $response){
      throw new \Exception('not implemented yet');
    }
    
    protected function parseAmazon(FOSUserInterface $user, UserResponseInterface $response){
      throw new \Exception('not implemented yet');
    }    
}
