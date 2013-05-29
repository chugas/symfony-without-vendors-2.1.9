<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Success\UsuarioBundle\Form\Handler;

use FOS\UserBundle\Form\Handler\RegistrationFormHandler as BaseHandler;
use FOS\UserBundle\Model\UserInterface;

class RegistrationFormHandler extends BaseHandler {

  protected function onSuccess(UserInterface $user, $confirmation) {
    // Note: if you plan on modifying the user then do it before calling the
    // parent method as the parent method will flush the changes
    // Si fue invitado este usuario agregar SONATA_COLUMINSTA

    //, 'ROLE_COLUMNISTA'
    //$user->setIsColumnista(true);

    // Si quiere recibir el boletin registrarlo al newsletter

    parent::onSuccess($user, $confirmation);

    // otherwise add your functionality here
  }

}
