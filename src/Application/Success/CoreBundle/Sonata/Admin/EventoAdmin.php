<?php

namespace Application\Success\CoreBundle\Sonata\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

use Application\Success\CoreBundle\Form\DataTransformer\StringToArrayTransformer;

class EventoAdmin extends Admin {

  public function getTemplate($name) {

    switch ($name) {
      case 'edit':
        return 'WebBundle:Admin/Evento:edit.html.twig';
        break;
      default:
        return parent::getTemplate($name);
        break;
    }
  }

  /**
   * Create and Edit
   * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
   *
   * @return void
   */
  protected function configureFormFields(FormMapper $formMapper) {
    $options = array('required' => false, 'label' => 'Flyer', 'data_class' => null);
    if (($subject = $this->getSubject()) && $subject->getFlyer()) {
      $path = $subject->getWebPath();
      $options['help'] = '<img src="' . $path . '" width="290" />';      
    }
    $transformer = new StringToArrayTransformer();
    $formMapper
          ->tab('General')
            ->add('name', 'text', array('label' => 'Nombre'))
            ->add('description', 'textarea', array('label' => 'Descripcion', 'attr' => array('rows' => 8)))
            ->add('timeAt', 'datetime', array('label' => 'Fecha', 'required' => false))
            ->add('address', 'text', array('label' => 'Direccion'))            
            ->add('price', 'text', array('label' => 'Precio', 'required' => false))
            ->add('unit', 'choice', array(
                'choices' => array('UYU' => 'UYU', 'USD' => 'USD'),
                'label' => 'Moneda', 
                'required' => false
            ))
            ->add('productora', 'sonata_type_model', array('required' => false))
            ->add($formMapper->create('lineup', 'text', array('required' => false, 'attr' => array('class' => 'span5')))->addModelTransformer($transformer))            
            ->add('latlng', 'oh_google_maps',array(
                  'label'   => 'Coordenadas',
                  'required' => false,
                  'lat_name'       => 'lat',   // the name of the lat field
                  'lng_name'       => 'lng',   // the name of the lng field
                  'map_width'      => 400,     // the width of the map
                  'map_height'     => 400,     // the height of the map
                  'default_lat'    => -34.46,    // the starting position on the map
                  'default_lng'    => -55.58, // the starting position on the map
                  'include_jquery' => false,   // jquery needs to be included above the field (ie not at the bottom of the page)
                  'include_gmaps_js'=>true     // is this the best place to include the google maps javascript?
            ))->end()
          ->end();
    
          $formMapper->tab('Anticipadas')
            ->add('priceAnticipada', 'text', array('label' => 'Precio', 'required' => false))
            ->add('descriptionAnticipada', 'textarea', array('label' => 'Descripcion', 'attr' => array('rows' => 8), 'required' => false))
            ->add('validateAtAnticipada', 'datetime', array('label' => 'Fecha', 'required' => false))
            ->add('sellJustrave', 'checkbox', array('label' => 'Vender en Justrave', 'required' => false))
            ->end()
          ->end();
          
          $formMapper->tab('Media')
            ->add('file', 'file', $options)                  
            ->add('images', 'sonata_type_collection', array(
                    'cascade_validation' => true,
                    'required' => false,
                    'by_reference' => false,
                    'label' => 'Imagenes'
                ), array(
                    'edit' => 'inline',
                    'inline' => 'table',
                    'sortable'  => 'position',
                    'allow_delete' => true,
                    'help' => 'Opcionalmente puedes agregar la cantidad de imagenes que quieras.'
                ))
            ->add('videos', 'sonata_type_collection', array(
                    'cascade_validation' => true,
                    'required' => false,
                    'by_reference' => false,
                    'label' => 'Videos'
                ), array(
                    'edit' => 'inline',
                    'inline' => 'table',
                    'sortable'  => 'position',
                    'allow_delete' => true,
                    'help' => 'Opcionalmente puedes agregar la cantidad de videos que quieras.'
                ))
            ->end()
          ->end();
          //->add($builder->create('avatar', 'hidden', array('required' => false))->addModelTransformer(new MediaToIntTransformer($this->em)))
    }

  /**
   * List
   * @param \Sonata\AdminBundle\Datagrid\ListMapper $listMapper
   *
   * @return void
   */
  protected function configureListFields(ListMapper $listMapper) {
    $listMapper
            ->addIdentifier('name')
            ->add('price')
            ->add('address')
    ;
  }

  /**
   * Filters
   * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $datagridMapper
   *
   * @return void
   */
  protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
    $datagridMapper->add('name');
  }

}