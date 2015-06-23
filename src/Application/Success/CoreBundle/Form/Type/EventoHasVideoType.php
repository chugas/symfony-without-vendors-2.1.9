<?php

namespace Application\Success\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Application\Success\CoreBundle\Form\DataTransformer\MediaToIntTransformer;

class EventoHasVideoType extends AbstractType {
  
  protected $em;

  public function __construct($em) {
    $this->em = $em;
  }

  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder
            ->add($builder->create('media', 'hidden')->addModelTransformer(new MediaToIntTransformer($this->em)))
    ;
  }

  public function setDefaultOptions(OptionsResolverInterface $resolver) {
    $resolver->setDefaults(array(
      'data_class' => 'Application\Success\CoreBundle\Entity\EventoHasVideo'
    ));
  }

  /**
   * @{inheritDoc}
   */
  public function getName() {
    return 'success_evento_video';
  }

}
