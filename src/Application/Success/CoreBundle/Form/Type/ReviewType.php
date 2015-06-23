<?php

namespace Application\Success\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ReviewType extends AbstractType {

  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder
            ->add('title', 'text', array(
                'constraints' => array(
                    new NotBlank()
                )                
            ))
            ->add('description', 'textarea', array(
                'constraints' => array(
                    new NotBlank()
                )                
            ))
    ;
  }

  /**
   * {@inheritdoc}
   */
  public function setDefaultOptions(OptionsResolverInterface $resolver) {
    $resolver
            ->setDefaults(array(
                'data_class' => 'Application\Success\CoreBundle\Entity\Review'
            ))
    ;
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'review';
  }

}
