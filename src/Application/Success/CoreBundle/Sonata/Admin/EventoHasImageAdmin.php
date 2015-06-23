<?php

namespace Application\Success\CoreBundle\Sonata\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

class EventoHasImageAdmin extends Admin {

  protected function configureFormFields(FormMapper $formMapper) {
    $formMapper
            ->add('media', 'sonata_type_model_list', array('required' => false), array(
                'link_parameters' => array('context' => 'eventos_image')
            ))
            ->add('position', 'hidden')
    ;
  }

  protected function configureListFields(ListMapper $listMapper) {
    $listMapper
            ->add('media')
            ->add('evento')
            ->add('position')
            ->add('avatar')
    ;
  }

}
