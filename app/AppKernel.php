<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel {
  
  public function __construct($environment, $debug) {
    parent::__construct($environment, $debug);
    ini_set('date.timezone', 'America/Montevideo');
  }

  public function registerBundles() {
    $bundles = array(
        // WEB CORE
        //new Application\Success\ExceptionBundle\ExceptionBundle(),
        new Application\Success\WebBundle\WebBundle(),
        new Application\Success\CoreBundle\CoreBundle(),
        // SYMFONY STANDARD EDITION
        new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
        new Symfony\Bundle\SecurityBundle\SecurityBundle(),
        new Symfony\Bundle\TwigBundle\TwigBundle(),
        new Symfony\Bundle\MonologBundle\MonologBundle(),
        new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
        new Symfony\Bundle\AsseticBundle\AsseticBundle(),
        new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
        new Sonata\CoreBundle\SonataCoreBundle(),
        // DOCTRINE
        new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
        // KNP HELPER BUNDLES
        new Knp\Bundle\MenuBundle\KnpMenuBundle(),
        // USER
        new FOS\UserBundle\FOSUserBundle(),
        new Sonata\UserBundle\SonataUserBundle('FOSUserBundle'),
        new Application\Success\UserBundle\SuccessUserBundle(),
        // MEDIA
        new Sonata\MediaBundle\SonataMediaBundle(),
        new Application\Success\MediaBundle\SuccessMediaBundle(),
        // SONATA CORE & HELPER BUNDLES
        new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),
        new Sonata\AdminBundle\SonataAdminBundle(),
        new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
        new Sonata\IntlBundle\SonataIntlBundle(),
        new Sonata\CacheBundle\SonataCacheBundle(),
        new Sonata\BlockBundle\SonataBlockBundle(),
        new Sonata\SeoBundle\SonataSeoBundle(),
        // TERCEROS
        new Avalanche\Bundle\ImagineBundle\AvalancheImagineBundle(),
        new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
        new HWI\Bundle\OAuthBundle\HWIOAuthBundle(),
        new Oh\GoogleMapFormTypeBundle\OhGoogleMapFormTypeBundle(),
        new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
        new Hpatoio\DeployBundle\DeployBundle(),
        new Spy\TimelineBundle\SpyTimelineBundle(),
        new FOS\RestBundle\FOSRestBundle(),
        new FOS\CommentBundle\FOSCommentBundle(),
        new JMS\SerializerBundle\JMSSerializerBundle($this),
        // Foro + Mensajero
        new CCDNComponent\CommonBundle\CCDNComponentCommonBundle(),
        new CCDNComponent\DashboardBundle\CCDNComponentDashboardBundle(),
        //new CCDNComponent\BBCodeBundle\CCDNComponentBBCodeBundle(),
        //new CCDNMessage\MessageBundle\CCDNMessageMessageBundle(),
        new CCDNForum\ForumBundle\CCDNForumForumBundle(),
        // ITSUCCESS
        new Success\RelationBundle\SuccessRelationBundle(),
        new Success\InviteBundle\SuccessInviteBundle(),
    );

    if (in_array($this->getEnvironment(), array('dev', 'test'))) {
      $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
      $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
      $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
    }

    return $bundles;
  }

  public function registerContainerConfiguration(LoaderInterface $loader) {
    $loader->load(__DIR__ . '/config/config_' . $this->getEnvironment() . '.yml');
  }

}
