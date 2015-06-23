<?php

namespace Application\Success\UserBundle\Form\Type;

//use Symfony\Component\OptionsResolver\OptionsResolverInterface;
//use Symfony\Component\Validator\Constraints\Email;
//use Symfony\Component\Validator\Constraints\Collection;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Formulario para crear entidades de tipo Usuario cuando los usuarios se
 * registran en el sitio.
 * Como se utiliza en la parte pública del sitio, algunas propiedades de
 * la entidad no se incluyen en el formulario.
 */
class RegistrationFormType extends BaseType {
  
  public function buildForm(FormBuilderInterface $builder, array $options) {
    parent::buildForm($builder, $options);
    $builder
        ->add('firstname', 'text', array(
            'required' => false, 
            'label' => 'form.name', 
            'translation_domain' => 'messages',
            'constraints' => array(
              new NotBlank(),
              new Length(array('min' => 2, 'max' => 16))
            )
        ))
        ->add('lastname', 'text', array(
            'required' => true, 
            'label' => 'form.surname', 
            'translation_domain' => 'messages',
            'constraints' => array(
              new NotBlank(),
              new Length(array('min' => 2, 'max' => 16))
            )
        ))
        ->remove('username')
        /*->add('email', 'text', array(
            'label' => 'form.email', 
            'translation_domain' => 'messages', 
            'attr' => array(
                'placeholder' => 'usuario@servidor',
                'autocomplete' => 'off'
            )
        ))
        ->add('plainPassword', 'repeated', array(
            'type' => 'password',
            'options' => array('translation_domain' => 'messages', 'always_empty' => false),
            'first_options' => array('label' => 'form.password'),
            'second_options' => array('label' => 'form.password_confirmation'),
            'invalid_message' => 'fos_user.password.mismatch', 'attr' => array(
            'autocomplete' => 'off'
        )))*/
        //->add('plainPassword', 'password', array('required' => true, 'label' => 'form.password', 'translation_domain' => 'messages'))
    ;
  }

  public function getName() {
    return 'success_user_registration';
  }

}
