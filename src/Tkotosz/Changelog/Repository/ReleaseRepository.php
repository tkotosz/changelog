<?php

namespace Tkotosz\Changelog\Repository;

use Tkotosz\Changelog\Model\Config;
use Tkotosz\Changelog\Model\Release;
use Tkotosz\Changelog\Repository\TicketRepository;

class ReleaseRepository
{
    /**
     * @var Config
     */
    private $config;
    
    /**
     * @var TicketRepository
     */
    private $ticketRepository;
    
    /**
     * @param Config           $config
     * @param TicketRepository $ticketRepository
     */
    public function __construct(Config $config, TicketRepository $ticketRepository)
    {
        $this->config = $config;
        $this->ticketRepository = $ticketRepository;
    }
    
    public function save(Release $release): bool
    {
        if ($this->exists($release)) {
            $existingRelease = $this->getByReleaseVersion($release->getReleaseVersion());

            foreach ($existingRelease->getTickets() as $ticket) {
                if (!$release->contains($ticket)) {
                    $this->ticketRepository->save($ticket);
                }
            }

            foreach ($release->getTickets() as $ticket) {
                if (!$existingRelease->contains($ticket)) {
                    $this->ticketRepository->deleteByTicketNumber($ticket->getTicketNumber());
                }
            }
        }

        return file_put_contents(
            $this->getReleaseFilePath($release->getReleaseVersion()),
            json_encode($release->toArray(), JSON_PRETTY_PRINT)
        ) !== false;
    }

    public function getByReleaseVersion(string $releaseVersion): Release
    {
        $release = $this->getByReleaseFilePath($this->getReleaseFilePath($releaseVersion));

        if ($release === null) {
            throw new \Exception(sprintf('Release with release version "%s" does not exists', $releaseVersion));
        }

        return $release;
    }

    public function getAll(): array
    {
        $releases = [];

        foreach (glob($this->getReleasesFolderPath() . '/*.json') as $releaseFile) {
            if (($release = $this->getByReleaseFilePath($releaseFile)) !== null) {
                $releases[] = $release;
            }
        }

        return $releases;
    }

    private function exists(Release $release): bool
    {
        return $this->getByReleaseFilePath($this->getReleaseFilePath($release->getReleaseVersion())) !== null;
    }

    private function getByReleaseFilePath(string $releaseFilePath): ?Release
    {
        $releaseData = @file_get_contents($releaseFilePath);

        if ($releaseData === false) {
            return null;
        }

        return $this->createReleaseFromArray(json_decode($releaseData, true));
    }

    private function getReleaseFilePath(string $releaseVersion): string
    {
        return $this->getReleasesFolderPath() . '/' . $releaseVersion . '.json';;
    }

    private function getReleasesFolderPath(): string
    {
        $folder = $this->config->getReleasesFolderPath();

        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        return $folder;
    }

    private function createReleaseFromArray(array $releaseData): Release
    {
        $releaseVersion = $releaseData[Release::RELEASE_VERSION] ?? null;
        $releaseDate = $releaseData[Release::RELEASE_DATE] ?? null;
        $ticketsData = $releaseData[Release::TICKETS] ?? [];

        if ($releaseVersion === null) {
            throw new \Exception(Release::RELEASE_VERSION . ' is required');
        }

        if ($releaseDate === null) {
            throw new \Exception(Release::RELEASE_DATE . ' is required');
        }

        $tickets = [];
        foreach ($ticketsData as $ticketData) {
            $tickets[] = $this->ticketRepository->createTicketFromArray($ticketData);
        }

        return new Release($releaseVersion, $releaseDate, $tickets);
    }
}
