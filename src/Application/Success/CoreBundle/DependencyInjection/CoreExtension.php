<?php

namespace Application\Success\CoreBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Application\Success\CoreBundle\CoreBundle;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class CoreExtension extends Extension {

  public function load(array $configs, ContainerBuilder $container) {
    $configuration = new Configuration();
    $config = $this->processConfiguration($configuration, $configs);

    $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
    $loader->load('services.yml');
    $loader->load('admin.yml');
    $loader->load('twig.yml');
    
    $driver = $config['driver'];
    
    $this->loadDriver($driver, $config, $loader);

    $container->setParameter('core.driver', $driver);
    $container->setParameter('core.driver.' . $driver, true);
    $container->setParameter('core.mailer.from', $config['mailer']['from']);
    $container->setParameter('core.mailer.to', $config['mailer']['to']);

    $classes = $config['classes'];

    $this->mapClassParameters($classes, $container);
  }

  /**
   * Load bundle driver.
   *
   * @param string        $driver
   * @param array         $config
   * @param XmlFileLoader $loader
   *
   * @throws \InvalidArgumentException
   */
  protected function loadDriver($driver, array $config, YamlFileLoader $loader) {
    if (!in_array($driver, CoreBundle::getSupportedDrivers())) {
      throw new \InvalidArgumentException(sprintf('Driver "%s" is unsupported by CoreBundle.', $driver));
    }

    $classes = $config['classes'];
    $loader->load(sprintf('container/driver/%s.yml', $driver));

    $loader->load('container/evento.yml');
    $loader->load('container/productora.yml');
    $loader->load('container/review.yml');
    //$loader->load('company.xml');
  }

  /**
   * Remap class parameters.
   *
   * @param array            $classes
   * @param ContainerBuilder $container
   */
  protected function mapClassParameters(array $classes, ContainerBuilder $container) {
    foreach ($classes as $model => $serviceClasses) {
      foreach ($serviceClasses as $service => $class) {
        $service = $service === 'form' ? 'form.type' : $service;
        $container->setParameter(sprintf('success.%s.%s.class', $service, $model), $class);
      }
    }
  }

}
