<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Success\UserBundle\Form\Handler;

use FOS\UserBundle\Model\UserInterface;

class InvitationRegistrationFormHandler extends RegistrationFormHandler {

  protected function onSuccess(UserInterface $user, $confirmation) {
    // Note: if you plan on modifying the user then do it before calling the
    // parent method as the parent method will flush the changes
    // Si fue invitado este usuario agregar SONATA_COLUMINSTA
    //$user->addRole('ROLE_COLUMNISTA');
    //$user->setIsColumnista(true);

    //$user->getInvitation()->setUser($user);

    parent::onSuccess($user, $confirmation);

    // otherwise add your functionality here
  }

}
