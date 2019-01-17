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

class ReleaseShowCommand extends Command
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
        $this->setName('release:show')
            ->setDescription('Show release')
            ->addOption('releaseversion', '-r', InputOption::VALUE_OPTIONAL, 'Release version');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $releaseVersion = $input->getOption('releaseversion');

        if ($releaseVersion === null) {
            $releaseVersion = $io->ask('Release version', null, function (?string $value) {
                if (empty($value)) {
                    throw new \InvalidArgumentException('Release version is required');
                }

                return $value;
            });
        }

        $release = $this->releaseRepository->getByReleaseVersion($releaseVersion);

        $io->title(sprintf('[%s] - %s', $release->getReleaseVersion(), $release->getReleaseDate()));

        $io->section('Tickets');
        
        $ticketsData = [];
        foreach ($release->getTickets() as $ticket) {
            $ticketsData[] = $ticket->toArray();
        }

        $io->table([Ticket::TICKET_NUMBER, Ticket::TICKET_LINK, Ticket::DESCRIPTION], $ticketsData);
    }
}
