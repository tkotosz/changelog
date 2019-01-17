<?php

namespace Tkotosz\Changelog\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tkotosz\Changelog\Model\Config;
use Tkotosz\Changelog\Model\Release;
use Tkotosz\Changelog\Model\Ticket;
use Tkotosz\Changelog\Repository\ReleaseRepository;

class ReleaseUpdateReleaseDateCommand extends Command
{
    /**
     * @var ReleaseRepository
     */
    private $releaseRepository;
    
    /**
     * @param ReleaseRepository $releaseRepository
     */
    public function __construct(ReleaseRepository $releaseRepository)
    {
        $this->releaseRepository = $releaseRepository;

        parent::__construct();
    }
    
    protected function configure()
    {
        $this->setName('release:update:release-date')
            ->setDescription('Update release date for the release')
            ->addOption('releaseversion', '-r', InputOption::VALUE_OPTIONAL, 'Release version')
            ->addOption('releasedate', '-d', InputOption::VALUE_OPTIONAL, 'Release date');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $releaseVersion = $input->getOption('releaseversion');
        $releaseDate = $input->getOption('releasedate');

        if ($releaseVersion === null) {
            $releaseVersion = $io->ask('Release version', null, function (?string $value) {
                if (empty($value)) {
                    throw new \InvalidArgumentException('Release version is required');
                }

                return $value;
            });
        }

        if ($releaseDate === null) {
            $releaseDate = $io->ask('Release date', null, function (?string $value) {
                if (empty($value)) {
                    throw new \InvalidArgumentException('Release date is required');
                }

                return $value;
            });
        }

        $release = $this->releaseRepository->getByReleaseVersion($releaseVersion);

        $release->setReleaseDate($releaseDate);

        $this->releaseRepository->save($release);
    }
}
