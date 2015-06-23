<?php

namespace Application\Success\UserBundle\Form\DataTransformer;

use Application\Success\UsuarioBundle\Entity\Invitation;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Transforms an Invitation to an invitation code.
 */
class InvitationToCodeTransformer implements DataTransformerInterface {

  protected $entityManager;

  public function __construct(EntityManager $entityManager) {
    $this->entityManager = $entityManager;
  }

  public function transform($value) {
    if (null === $value) {
      return null;
    }

    if (!$value instanceof Invitation) {
      throw new UnexpectedTypeException($value, 'Application\Success\UserBundle\Entity\Invitation');
    }

    return $value->getCode();
  }

  public function reverseTransform($value) {
    if (null === $value || '' === $value) {
      return null;
    }

    if (!is_string($value)) {
      throw new UnexpectedTypeException($value, 'string');
    }

    $invitation = $this->entityManager
                    ->getRepository('Application\Success\UserBundle\Entity\Invitation')
                    ->findOneBy(array(
                        'code' => $value,
                        'user' => null,
                        'sent' => 1
                    ));
    if(is_null($invitation)){
      throw new TransformationFailedException(sprintf(
                'The code "%s" does not exist!',
                $value
            ));
    }

    return $invitation;
  }

}