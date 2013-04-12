<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new JMS\AopBundle\JMSAopBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
            
            // SonataAdminBundle + FosUserBundle
            new FOS\UserBundle\FOSUserBundle(),
            new Sonata\jQueryBundle\SonatajQueryBundle(),
            new Sonata\AdminBundle\SonataAdminBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),
            /****************/

            new Sonata\IntlBundle\SonataIntlBundle(),
            
            // SonataMediaBundle
            new Sonata\MediaBundle\SonataMediaBundle(),
            /*****************/
            new Application\Sonata\MediaBundle\ApplicationSonataMediaBundle(),
            
            // Behaviours BASICOS
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            
            // Manejo de Imagenes Fisicas
            new Avalanche\Bundle\ImagineBundle\AvalancheImagineBundle(),
            
            // Translator
            new Lexik\Bundle\TranslationBundle\LexikTranslationBundle(),
            
            // Embebed translators forms
            new A2lix\TranslationFormBundle\A2lixTranslationFormBundle(),
            
            // Tiny MCE
            new Stfalcon\Bundle\TinymceBundle\StfalconTinymceBundle(),

            // Manejo Basico de Locales
            new Success\LocaleBundle\LocaleBundle(),
            
            // Sonata Custom Admin by Success
            new Success\AdminBundle\SuccessAdminBundle(),
            
            // Frontend
            new Application\Success\FrontendBundle\FrontendBundle(),
            
            // Para poder utilizar enumerados
            new Fresh\Bundle\DoctrineEnumBundle\FreshDoctrineEnumBundle(),

            // Formulario de Contacto
            new FrequenceWeb\Bundle\ContactBundle\FrequenceWebContactBundle(),

            // Extraer i18n de Templates
            new BCC\ExtraToolsBundle\BCCExtraToolsBundle(),
            
            // Puesta en Produccion ssh - project:deploy
            new Hpatoio\DeployBundle\DeployBundle(),            
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
