<?php

namespace Tkotosz\Changelog\Model;

class Ticket
{
    public const TICKET_NUMBER = 'ticket_number';
    public const TICKET_LINK = 'ticket_link';
    public const DESCRIPTION = 'description';

    /**
     * @var string
     */
    private $ticketNumber;
    
    /**
     * @var string
     */
    private $ticketLink;
    
    /**
     * @var string
     */
    private $description;
    
    /**
     * @param string $ticketNumber
     * @param string $ticketLink
     * @param string $description
     */
    public function __construct(string $ticketNumber, string $ticketLink, string $description)
    {
        $this->ticketNumber = $ticketNumber;
        $this->ticketLink = $ticketLink;
        $this->description = $description;
    }
    
    public function getTicketNumber(): string
    {
        return $this->ticketNumber;
    }

    public function getTicketLink(): string
    {
        return $this->ticketLink;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function toArray(): array
    {
        return [
            self::TICKET_NUMBER => $this->getTicketNumber(),
            self::TICKET_LINK => $this->getTicketLink(),
            self::DESCRIPTION => $this->getDescription()
        ];
    }
}
