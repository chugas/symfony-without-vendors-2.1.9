<?php

namespace Application\Success\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

class ContactType extends AbstractType {

  /**
   * @{inheritDoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder
            ->add('name', 'text', array(
                'constraints' => array(
                    new NotBlank()
                )
            ))
            ->add('email', 'email', array(
                'constraints' => array(
                    new NotBlank(),
                    new Email()
                )
            ))
            ->add('subject', 'text', array(
                'constraints' => array(
                    new NotBlank()
                )
            ))
            ->add('body', 'textarea', array(
                'constraints' => array(
                    new NotBlank()
                )
            ))
    ;
  }

  /**
   * @{inheritDoc}
   */
  public function getName() {
    return 'contact';
  }

}