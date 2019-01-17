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

class TicketCreateCommand extends Command
{
    /**
     * @var TicketRepository
     */
    private $ticketRepository;
    
    /**
     * @var Config
     */
    private $config;
    
    /**
     * @param TicketRepository $ticketRepository
     * @param Config           $config
     */
    public function __construct(TicketRepository $ticketRepository, Config $config)
    {
        $this->ticketRepository = $ticketRepository;
        $this->config = $config;

        parent::__construct();
    }
    
    protected function configure()
    {
        $this->setName('ticket:create')
            ->setDescription('Create ticket changelog file')
            ->addOption('ticketnumber', '-t', InputOption::VALUE_OPTIONAL, 'Ticket number')
            ->addOption('ticketlink', '-l', InputOption::VALUE_OPTIONAL, 'Ticket link')
            ->addOption('description', '-d', InputOption::VALUE_OPTIONAL, 'Description of the change');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $ticketNumber = $input->getOption('ticketnumber');
        $ticketLink = $input->getOption('ticketlink');
        $description = $input->getOption('description');

        if ($ticketNumber === null) {
            $ticketNumber = $io->ask('Ticket Number', null, function (?string $value) {
                if (empty($value)) {
                    throw new \InvalidArgumentException('Ticket number is required');
                }

                return $value;
            });
        }

        if ($ticketLink === null) {
            $ticketLink = $io->ask('Ticket Link', $this->config->getTicketUrl($ticketNumber), function (?string $value) {
                if (empty($value)) {
                    throw new \InvalidArgumentException('Ticket link is required');
                }

                return $value;
            });
        }

        if ($description === null) {
            $description = $io->ask('Description', null, function (?string $value) {
                if (empty($value)) {
                    throw new \InvalidArgumentException('Description is required');
                }

                return $value;
            });
        }

        $this->ticketRepository->save(new Ticket($ticketNumber, $ticketLink, $description));

        $io->success('Ticket changelog file created successfully');
    }
}
