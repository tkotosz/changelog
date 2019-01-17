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
use Tkotosz\Changelog\Repository\TicketRepository;

class ReleaseTicketAddCommand extends Command
{
    /**
     * @var ReleaseRepository
     */
    private $releaseRepository;
    
    /**
     * @var TicketRepository
     */
    private $ticketRepository;
    
    /**
     * @param ReleaseRepository $releaseRepository
     * @param TicketRepository  $ticketRepository
     */
    public function __construct(ReleaseRepository $releaseRepository, TicketRepository $ticketRepository)
    {
        $this->releaseRepository = $releaseRepository;
        $this->ticketRepository = $ticketRepository;

        parent::__construct();
    }
    
    protected function configure()
    {
        $this->setName('release:ticket:add')
            ->setDescription('Add ticket to release')
            ->addOption('releaseversion', '-r', InputOption::VALUE_OPTIONAL, 'Release version')
            ->addOption('ticketnumber', '-t', InputOption::VALUE_OPTIONAL, 'Ticket Number');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $releaseVersion = $input->getOption('releaseversion');
        $ticketNumber = $input->getOption('ticketnumber');

        if ($releaseVersion === null) {
            $releaseVersion = $io->ask('Release version', null, function (?string $value) {
                if (empty($value)) {
                    throw new \InvalidArgumentException('Release version is required');
                }

                return $value;
            });
        }

        if ($ticketNumber === null) {
            $ticketNumber = $io->ask('Ticket Number', null, function (?string $value) {
                if (empty($value)) {
                    throw new \InvalidArgumentException('Ticket number is required');
                }

                return $value;
            });
        }

        $release = $this->releaseRepository->getByReleaseVersion($releaseVersion);
        $ticket = $this->ticketRepository->getByTicketNumber($ticketNumber);

        $release->addTicket($ticket);

        $this->releaseRepository->save($release);

        $io->success(sprintf('Ticket "%s" successfully added to the release', $ticketNumber));
    }
}
