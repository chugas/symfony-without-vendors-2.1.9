<?php

namespace Application\Success\CoreBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CoreBundle extends Bundle {

  // Bundle driver list.
  const DRIVER_DOCTRINE_ORM = 'doctrine/orm';
  const DRIVER_DOCTRINE_MONGODB_ODM = 'doctrine/mongodb-odm';

  public static function getSupportedDrivers() {
    return array(
        self::DRIVER_DOCTRINE_ORM
    );
  }
  
  public function build(ContainerBuilder $container){
    $mappings = array(
        realpath(__DIR__ . '/Resources/config/doctrine/model') => 'Application\Success\CoreBundle\Entity',
    );

    $container->addCompilerPass(DoctrineOrmMappingsPass::createYamlMappingDriver($mappings, array('doctrine.orm.entity_manager'), 'core.driver.doctrine/orm')); 
    //$container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($mappings, array('doctrine.orm.entity_manager'), 'core.driver.doctrine/orm'));     
  }

}
