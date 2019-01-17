<?php

namespace Tkotosz\Changelog\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tkotosz\Changelog\Model\Config;
use Tkotosz\Changelog\Repository\TicketRepository;

class TicketDeleteCommand extends Command
{
    /**
     * @var TicketRepository
     */
    private $ticketRepository;
    
    /**
     * @param TicketRepository $ticketRepository
     */
    public function __construct(TicketRepository $ticketRepository)
    {
        $this->ticketRepository = $ticketRepository;

        parent::__construct();
    }
    
    protected function configure()
    {
        $this->setName('ticket:delete')
            ->setDescription('Remove ticket changelog file')
            ->addOption('ticketnumber', '-t', InputOption::VALUE_OPTIONAL, 'Ticket number');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $ticketNumber = $input->getOption('ticketnumber');

        if ($ticketNumber === null) {
            $ticketNumber = $io->ask('Ticket Number', null, function (?string $value) {
                if (empty($value)) {
                    throw new \InvalidArgumentException('Ticket number is required');
                }

                return $value;
            });
        }

        $this->ticketRepository->deleteByTicketNumber($ticketNumber);

        $io->success('Ticket changelog file deleted successfully');
    }
}
