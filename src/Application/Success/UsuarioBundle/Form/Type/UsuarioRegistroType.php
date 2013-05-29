<?php

namespace Application\Success\UsuarioBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;


/**
 * Formulario para crear entidades de tipo Usuario cuando los usuarios se
 * registran en el sitio.
 * Como se utiliza en la parte pública del sitio, algunas propiedades de
 * la entidad no se incluyen en el formulario.
 */

/**
  protected $avatar;
 */

class UsuarioRegistroType extends BaseType {
  
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder
        ->add('name', 'text', array('required' => true))
        ->add('surname', 'text', array('required' => true))
        ->add('username', 'text', array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle', 'required' => true))
        ->add('email', 'email', array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle', 'attr' => array(
                'placeholder' => 'usuario@servidor',
                'autocomplete' => 'off'
                )))
        ->add('plainPassword', 'repeated', array(
            'type' => 'password',
            'options' => array('translation_domain' => 'FOSUserBundle', 'always_empty' => false),
            'first_options' => array('label' => 'form.password'),
            'second_options' => array('label' => 'form.password_confirmation'),
            'invalid_message' => 'fos_user.password.mismatch', 'attr' => array(
            'autocomplete' => 'off'
        )))
        ->add('phone')
        ->add('birthday', 'birthday', array(
            'years' => range(date('Y'), date('Y') - 120),
            'format' => 'dd-MMMM-yyyy'
        ))
        ->add('gender', 'choice', array(
            'choices'   => array('m' => 'Masculino', 'f' => 'Femenino'),
            'required'  => false,
            'expanded' => true
        ))
        ->add('description', 'textarea')
//        ->add('occupation')
//        ->add('company')
        ->add('country', 'choice', array(
                  'choices'   => array(
                                  'UY' => 'Uruguay', 
                                  'AR' => 'Argentina',
                                  'BR' => 'Brasil',
                                  'PY' => 'Paraguay',
                                  'CL' => 'Chile',
                                  'PE' => 'Peru',
                                  'VE' => 'Venezuela',
                                  'BO' => 'Bolivia',
                                  'CO' => 'Colombia',
                                  'EC' => 'Ecuador',
                                  'MX' => 'Mexico',                      
                                  ),
                  'required'  => false
              ))
        ->add('city')            
        ->add('receive_news', 'checkbox', array('required' => false))
    ;
  }

  public function setDefaultOptions(OptionsResolverInterface $resolver) {
    $resolver->setDefaults(array(
        'data_class' => 'Application\Success\UsuarioBundle\Entity\User',
        'validation_groups' => array('default', 'registro')
    ));
  }

  public function getName() {
    return 'success_user_registration';
  }

}
