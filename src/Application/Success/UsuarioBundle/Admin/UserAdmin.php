<?php

namespace Application\Success\UsuarioBundle\Admin;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Success\AdminBundle\Admin\UserAdmin as SuccessUserAdmin;

class UserAdmin extends SuccessUserAdmin {

  /**
   * Create and Edit
   * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
   *
   * @return void
   */
  protected function configureFormFields(FormMapper $formMapper) {
    $options = array('required' => false);
    $options['data_class'] = null;
    if (($subject = $this->getSubject()) && $subject->getAvatar()) {
      $path = $subject->getWebPath();
      $options['help'] = '<img src="' . $path . '" width="150" />';      
    }
    parent::configureFormFields($formMapper);
    $formMapper
      ->with('General')
        ->add('name', null, array('required' => false))
        ->add('surname', null, array('required' => false))
        ->add('city', null, array('required' => false))
        ->add('is_columnista')            
        ->add('phone')
        ->add('file', 'file', $options)
        ->add('description')
        ->add('receive_news')
      ->end();
  }

  /**
   * List
   * @param \Sonata\AdminBundle\Datagrid\ListMapper $listMapper
   *
   * @return void
   */
  protected function configureListFields(ListMapper $listMapper) {
    parent::configureListFields($listMapper);
  }

  /**
   * Filters
   * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $datagridMapper
   *
   * @return void
   */
  protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
    parent::configureDatagridFilters($datagridMapper);
    $datagridMapper
            ->add('is_columnista');
  }


}