<?php

namespace Tkotosz\Changelog\Configurator;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use Tkotosz\Changelog\Model\Config;
use Tkotosz\CliAppBuilder\CliAppConfig;
use Tkotosz\CliAppBuilder\Configurator\ConfiguratorInterface;

class RegisterApplicationConfigurationConfigurator implements ConfiguratorInterface
{
    public function configure(ContainerBuilder $containerBuilder, CliAppConfig $cliAppConfig)
    {
        try {
            $configData = Yaml::parseFile(Config::getConfigFilePath());
        } catch (ParseException $e) {
            $configData = [];
        }

        $containerBuilder->setDefinition(Config::class, new Definition(Config::class, [$configData]));
    }
}
