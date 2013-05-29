<?php

namespace Application\Success\UsuarioBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Application\Success\UsuarioBundle\Form\Type\UsuarioRegistroType as BaseType;

class ProfileFormType extends BaseType {
  
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $options_file = array('required' => false);
    $options_file['data_class'] = null;    
    parent::buildForm($builder, $options);
    $builder
          ->add('file', 'file', $options_file)
          ->add('plainPassword', 'text', array('required' => false, 'label' => 'Nuevo Password'));
  }
  
  public function getName() {
    return 'success_user_profile';
  }

}
