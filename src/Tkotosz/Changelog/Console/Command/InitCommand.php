<?php

namespace Tkotosz\Changelog\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;
use Tkotosz\Changelog\Model\Config;

class InitCommand extends Command
{
    protected function configure()
    {
        $this->setName('init')
            ->setDescription('Initialize configuration');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $changelogsFolder = $io->ask('Changelogs folder', 'changelogs');
        $ticketBaseUrl = $io->ask('Ticket base url');

        $newConfig = new Config(
            [
                Config::CONFIG_CHANGELOGS_FOLDER => $changelogsFolder,
                Config::CONFIG_TICKET_BASE_URL => $ticketBaseUrl
            ]
        );

        file_put_contents(Config::getConfigFilePath(), Yaml::dump($newConfig->toArray()));

        $io->success('Changelog settings successfully saved to ' . Config::getConfigFilePath());
    }
}
