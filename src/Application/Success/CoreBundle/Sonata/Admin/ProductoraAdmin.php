<?php

namespace Application\Success\CoreBundle\Sonata\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

class ProductoraAdmin extends Admin {
  
  /**
   * Create and Edit
   * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
   *
   * @return void
   */
  protected function configureFormFields(FormMapper $formMapper) {
    $formMapper
            ->add('name', 'text', array('label' => 'Nombre'));
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
            ->add('createdAt')
            ->add('updatedAt')
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