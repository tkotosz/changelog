<?php

namespace Tkotosz\Changelog\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tkotosz\Changelog\Model\Config;
use Tkotosz\Changelog\Model\Ticket;
use Tkotosz\Changelog\Repository\TicketRepository;

class TicketListCommand extends Command
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
        $this->setName('ticket:list')
            ->setDescription('List all ticket changelog file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        
        $ticketsData = [];
        foreach ($this->ticketRepository->getAll() as $ticket) {
            $ticketsData[] = $ticket->toArray();
        }

        $io->table([Ticket::TICKET_NUMBER, Ticket::TICKET_LINK, Ticket::DESCRIPTION], $ticketsData);
    }
}
