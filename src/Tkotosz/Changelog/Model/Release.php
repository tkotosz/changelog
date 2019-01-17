<?php

namespace Tkotosz\Changelog\Model;

use Tkotosz\Changelog\Model\Ticket;

class Release
{
    public const RELEASE_VERSION = 'release_version';
    public const RELEASE_DATE = 'release_date';
    public const TICKETS = 'tickets';

    /**
     * @var string
     */
    private $releaseVersion;
    
    /**
     * @var string
     */
    private $releaseDate;
    
    /**
     * @var array
     */
    private $tickets;
    
    /**
     * @param string $releaseVersion
     * @param string $releaseDate
     * @param array  $tickets
     */
    public function __construct(string $releaseVersion, string $releaseDate, array $tickets = [])
    {
        $this->releaseVersion = $releaseVersion;
        $this->releaseDate = $releaseDate;
        $this->tickets = $tickets;
    }

    public function getReleaseVersion(): string
    {
        return $this->releaseVersion;
    }

    public function getReleaseDate(): string
    {
        return $this->releaseDate;
    }

    public function getTickets(): array
    {
        return $this->tickets;
    }

    public function setReleaseDate(string $releaseDate)
    {
        $this->releaseDate = $releaseDate;
    }

    public function addTicket(Ticket $ticket)
    {
        if (!$this->contains($ticket)) {
            $this->tickets[] = $ticket;
        }
    }

    public function removeTicket(string $ticketNumber)
    {
        foreach ($this->tickets as $key => $releaseTicket) {
            if ($releaseTicket->getTicketNumber() === $ticketNumber) {
                unset($this->tickets[$key]);
            }
        }
    }

    public function contains(Ticket $ticket)
    {
        foreach ($this->tickets as $releaseTicket) {
            if ($releaseTicket->getTicketNumber() === $ticket->getTicketNumber()) {
                return true;
            }
        }

        return false;
    }
    
    public function toArray(): array
    {
        $tickets = [];
        foreach ($this->getTickets() as $ticket) {
            $tickets[] = $ticket->toArray();
        }

        return [
            self::RELEASE_VERSION => $this->getReleaseVersion(),
            self::RELEASE_DATE => $this->getReleaseDate(),
            self::TICKETS => $tickets
        ];
    }
}
