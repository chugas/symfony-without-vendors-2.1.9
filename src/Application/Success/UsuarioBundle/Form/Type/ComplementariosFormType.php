<?php

namespace Application\Success\UsuarioBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;

class ComplementariosFormType extends AbstractType {
  
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $options_file = array('required' => false);
    $options_file['data_class'] = null;
    
    $builder
        ->add('description', 'textarea')
        ->add('occupation')
        ->add('file', 'file', $options_file)            
    ;
  }

  public function setDefaultOptions(OptionsResolverInterface $resolver) {
    $resolver->setDefaults(array(
        'data_class' => 'Application\Success\UsuarioBundle\Entity\User',
        'validation_groups' => array('default')
    ));
  }

  public function getName() {
    return 'success_user_complementarios';
  }

}
