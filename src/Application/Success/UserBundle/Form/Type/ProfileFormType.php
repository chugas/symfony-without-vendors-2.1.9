<?php

namespace Application\Success\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProfileFormType extends AbstractType {

  private $class;

  /**
   * @param string $class The User class name
   */
  public function __construct($class) {
    $this->class = $class;
  }

  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder
            ->add('firstname', 'text', array(
                'required' => false,
                'constraints' => array(
                    new NotBlank(),
                    new Length(array('min' => 2, 'max' => 32))
                )
            ))
            ->add('lastname', 'text', array(
                'required' => true,
                'constraints' => array(
                    new NotBlank(),
                    new Length(array('min' => 2, 'max' => 32))
                )
            ))
            ->add('gender', 'choice', array(
                'choices' => array('m' => 'Masculino', 'f' => 'Femenino'),
                'required' => true,
                'expanded' => true
            ))
            ->add('email', 'text', array(
                'required' => true,
                'constraints' => array(
                    new NotBlank(),
                    new Length(array('min' => 2, 'max' => 255))
                )
            ))
            ->add('biography', 'textarea', array('required' => false))
            ->add('dateOfBirth', 'birthday', array(
                'required' => false,
                'years' => range(date('Y'), date('Y') - 100),
                'format' => 'dd-MM-yyyy',
                'empty_value' => false
            ))
            ->add('phone', 'text', array('required' => false))
            ->add('website', 'text', array('required' => false))
            ->add('facebook_name', 'text', array('required' => false))
            ->add('twitter_name', 'text', array('required' => false))
            ->add('isProductora', 'checkbox', array('required' => false))
            ->add('isDj', 'checkbox', array('required' => false))
            ->add('isVj', 'checkbox', array('required' => false))            
    ;
  }

  public function getName() {
    return 'success_user_profile';
  }

  public function setDefaultOptions(OptionsResolverInterface $resolver) {
    $resolver->setDefaults(array(
        'data_class' => $this->class
    ));
  }

}
