<?php

namespace Tkotosz\Changelog\Repository;

use Symfony\Component\Yaml\Yaml;
use Tkotosz\Changelog\Model\Config;
use Tkotosz\Changelog\Model\Ticket;

class TicketRepository
{
    /**
     * @var Config
     */
    private $config;
    
    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }
    
    public function save(Ticket $ticket): bool
    {
        return file_put_contents(
            $this->getTicketFilePath($ticket->getTicketNumber()),
            json_encode($ticket->toArray(), JSON_PRETTY_PRINT)
        ) !== false;
    }

    public function getByTicketNumber(string $ticketNumber): Ticket
    {
        $ticket = $this->getByTicketFilePath($this->getTicketFilePath($ticketNumber));

        if ($ticket === null) {
            throw new \Exception(sprintf('Ticket with ticket number "%s" does not exists', $ticketNumber));
        }

        return $ticket;
    }

    public function getAll(): array
    {
        $tickets = [];

        foreach (glob($this->getChangelogsFolderPath() . '/*.json') as $ticketFile) {
            if (($ticket = $this->getByTicketFilePath($ticketFile)) !== null) {
                $tickets[] = $ticket;
            }
        }

        return $tickets;
    }

    public function deleteByTicketNumber(string $ticketNumber): bool
    {
        return unlink($this->getTicketFilePath($ticketNumber));
    }

    public function createTicketFromArray(array $ticketData): Ticket
    {
        $ticketNumber = $ticketData[Ticket::TICKET_NUMBER] ?? null;
        $ticketLink = $ticketData[Ticket::TICKET_LINK] ?? null;
        $description = $ticketData[Ticket::DESCRIPTION] ?? null;

        if ($ticketNumber === null) {
            throw new \Exception(Ticket::TICKET_NUMBER . ' is required');
        }

        if ($ticketLink === null) {
            throw new \Exception(Ticket::TICKET_LINK . ' is required');
        }

        if ($description === null) {
            throw new \Exception(Ticket::DESCRIPTION . ' is required');
        }

        return new Ticket($ticketNumber, $ticketLink, $description);
    }

    private function getByTicketFilePath(string $ticketFilePath): ?Ticket
    {
        $ticketData = file_get_contents($ticketFilePath);

        if ($ticketData === false) {
            return null;
        }

        return $this->createTicketFromArray(json_decode($ticketData, true));
    }

    private function getTicketFilePath(string $ticketNumber): string
    {
        return $this->getChangelogsFolderPath() . '/' . $ticketNumber . '.json';;
    }

    private function getChangelogsFolderPath(): string
    {
        $folder = $this->config->getChangelogsFolderPath();

        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        return $folder;
    }
}
