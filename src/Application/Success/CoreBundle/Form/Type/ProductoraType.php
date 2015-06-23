<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Success\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductoraType extends AbstractType {

  protected $dataClass;

  /**
   * Constructor.
   *
   * @param string $dataClass
   * @param array  $validationGroups
   */
  public function __construct($dataClass) {
    $this->dataClass = $dataClass;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder
            ->add('name', 'text', array('label' => 'Nombre'))
    ;
  }

  public function setDefaultOptions(OptionsResolverInterface $resolver) {
    $resolver
            ->setDefaults(array(
                'data_class' => $this->dataClass
            ))
    ;
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'success_productora_form_type';
  }

}
