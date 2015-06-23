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
use Symfony\Component\Validator\Constraints\NotBlank;

class EventoType extends AbstractType {

  private $dataClass;

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
    $options_file = array('required' => false);
    $options_file['data_class'] = null;
    $builder
            ->add('name', 'text', array(
                'constraints' => array(
                    new NotBlank()
                )
            ))
            ->add('description', 'textarea', array(
                'constraints' => array(
                    new NotBlank()
                )
            ))
            ->add('address', 'text', array(
                'constraints' => array(
                    new NotBlank()
                )
            ))
            ->add('price', 'text', array('label' => 'Precio', 'required' => false))
            /*->add('unit', 'choice', array(
                'choices' => array('UYU' => 'UYU', 'USD' => 'USD'),
                'label' => 'Moneda',
                'required' => false
            ))*/
            ->add('productora', 'entity', array(
                'class' => 'Application\Success\CoreBundle\Entity\Productora',
                'attr' => array('class' => 'selector')
            ))
            ->add('timeAt', 'datetime', array(
                'required' => false
            ))
            ->add('latlng', 'oh_google_maps', array(
                'label' => 'Coordenadas',
                'required' => false,
                'lat_name' => 'lat', // the name of the lat field
                'lng_name' => 'lng', // the name of the lng field
                'map_width' => 216, // the width of the map
                'map_height' => 210, // the height of the map
                'default_lat' => -34.870, // the starting position on the map -34.862126745455484
                'default_lng' => -56.171, // the starting position on the map -56.178754882812484
                'include_jquery' => false, // jquery needs to be included above the field (ie not at the bottom of the page)
                'include_gmaps_js' => true     // is this the best place to include the google maps javascript?
            ))
            ->add('priceAnticipada', 'text', array(
                'required' => false
            ))
            ->add('descriptionAnticipada', 'textarea', array(
                'attr' => array('rows' => 8), 
                'required' => false
            ))
            ->add('validateAtAnticipada', 'date', array(
                'required' => false
            ))
            ->add('sellJustrave', 'checkbox', array(
                'required' => false
            ))
            ->add('file', 'file', $options_file)
            ->add('images', 'collection', array(
                'required' => false,
                'type' => 'success_evento_image',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ))
            ->add('videos', 'collection', array(
                'required' => false,
                'type' => 'success_evento_video',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ))
    ;
  }

  /**
   * {@inheritdoc}
   */
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
    return 'success_evento';
  }

}
