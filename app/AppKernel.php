<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            // Symfony
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Symfony\WebpackEncoreBundle\WebpackEncoreBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            // Dependencies
            new Hautelook\TemplatedUriBundle\HautelookTemplatedUriBundle(),
            new Liip\ImagineBundle\LiipImagineBundle(),
            new FOS\HttpCacheBundle\FOSHttpCacheBundle(),
            new Nelmio\CorsBundle\NelmioCorsBundle(),
            new Oneup\FlysystemBundle\OneupFlysystemBundle(),
            new JMS\TranslationBundle\JMSTranslationBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Bazinga\Bundle\JsTranslationBundle\BazingaJsTranslationBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),
            new Gregwar\CaptchaBundle\GregwarCaptchaBundle(),
            new Lexik\Bundle\JWTAuthenticationBundle\LexikJWTAuthenticationBundle(),
            // eZ Systems
            new EzSystems\PlatformHttpCacheBundle\EzSystemsPlatformHttpCacheBundle(),
            new EzSystems\PlatformFastlyCacheBundle\EzSystemsPlatformFastlyCacheBundle(),
            new eZ\Bundle\EzPublishCoreBundle\EzPublishCoreBundle(),
            new eZ\Bundle\EzPublishLegacySearchEngineBundle\EzPublishLegacySearchEngineBundle(),
            new eZ\Bundle\EzPublishIOBundle\EzPublishIOBundle(),
            new eZ\Bundle\EzPublishRestBundle\EzPublishRestBundle(),
            new EzSystems\EzSupportToolsBundle\EzSystemsEzSupportToolsBundle(),
            new EzSystems\PlatformInstallerBundle\EzSystemsPlatformInstallerBundle(),
            new EzSystems\DoctrineSchemaBundle\DoctrineSchemaBundle(),
            new EzSystems\RepositoryFormsBundle\EzSystemsRepositoryFormsBundle(),
            new EzSystems\EzPlatformSolrSearchEngineBundle\EzSystemsEzPlatformSolrSearchEngineBundle(),
            new EzSystems\EzPlatformDesignEngineBundle\EzPlatformDesignEngineBundle(),
            new EzSystems\EzPlatformStandardDesignBundle\EzPlatformStandardDesignBundle(),
            new EzSystems\EzPlatformRichTextBundle\EzPlatformRichTextBundle(),
            new EzSystems\EzPlatformAdminUiBundle\EzPlatformAdminUiBundle(),
            new EzSystems\EzPlatformUserBundle\EzPlatformUserBundle(),
            new EzSystems\EzPlatformAdminUiModulesBundle\EzPlatformAdminUiModulesBundle(),
            new EzSystems\EzPlatformAdminUiAssetsBundle\EzPlatformAdminUiAssetsBundle(),
            new EzSystems\EzPlatformCronBundle\EzPlatformCronBundle(),
            new EzSystems\EzPlatformEncoreBundle\EzSystemsEzPlatformEncoreBundle(),
            new EzSystems\EzPlatformGraphQL\EzSystemsEzPlatformGraphQLBundle(),
            new EzSystems\EzPlatformMatrixFieldtypeBundle\EzPlatformMatrixFieldtypeBundle(),
            // OverblogGraphQLBundle has to be loaded after EzSystemsEzPlatformGraphQLBundle
            new Overblog\GraphQLBundle\OverblogGraphQLBundle(),
            // eZ Platform EE
            new EzSystems\EzPlatformPageBuilderBundle\EzPlatformPageBuilderBundle(),
            new EzSystems\EzPlatformPageFieldTypeBundle\EzPlatformPageFieldTypeBundle(),
            new EzSystems\EzPlatformFormBuilderBundle\EzPlatformFormBuilderBundle(),
            new EzSystems\DateBasedPublisherBundle\EzSystemsDateBasedPublisherBundle(),
            new EzSystems\FlexWorkflowBundle\EzSystemsFlexWorkflowBundle(),
            new EzSystems\EzPlatformEnterpriseEditionInstallerBundle\EzPlatformEnterpriseEditionInstallerBundle(),
            new EzSystems\EzPlatformWorkflowBundle\EzPlatformWorkflowBundle(),
            // Application
            new AppBundle\AppBundle(),
        ];

        switch ($this->getEnvironment()) {
            case 'test':
            case 'behat':
                $bundles[] = new EzSystems\BehatBundle\EzSystemsBehatBundle();
                $bundles[] = new EzSystems\PlatformBehatBundle\EzPlatformBehatBundle();
            // No break, test also needs dev bundles
            case 'dev':
                $bundles[] = new eZ\Bundle\EzPublishDebugBundle\EzPublishDebugBundle();
                $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
                $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
                $bundles[] = new Symfony\Bundle\WebServerBundle\WebServerBundle();
                $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
                $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
                $bundles[] = new Overblog\GraphiQLBundle\OverblogGraphiQLBundle();
        }

        return $bundles;
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        if (!empty($_SERVER['SYMFONY_TMP_DIR'])) {
            return rtrim($_SERVER['SYMFONY_TMP_DIR'], '/') . '/var/cache/' . $this->getEnvironment();
        }

        return dirname(__DIR__) . '/var/cache/' . $this->getEnvironment();
    }

    public function getLogDir()
    {
        if (!empty($_SERVER['SYMFONY_TMP_DIR'])) {
            return rtrim($_SERVER['SYMFONY_TMP_DIR'], '/') . '/var/logs';
        }

        return dirname(__DIR__) . '/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir() . '/config/config_' . $this->getEnvironment() . '.yml');
    }
}
