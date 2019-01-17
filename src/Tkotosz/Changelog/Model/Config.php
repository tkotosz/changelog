<?php

namespace Tkotosz\Changelog\Model;

class Config
{
    public const CONFIG_CHANGELOGS_FOLDER = 'changelogs_folder';
    public const CONFIG_RELEASES_FOLDER = 'releases_folder';
    public const CONFIG_TICKET_BASE_URL = 'ticket_base_url';

    private const DEFAULT_CHANGELOGS_FOLDER = 'changelogs';
    private const DEFAULT_RELEASES_FOLDER = 'releases';
    private const DEFAULT_CHANGELOGS_SETTINGS_FILENAME = '.changelog-settings.yml';

    /**
     * @var string
     */
    private $changelogsFolder;

    /**
     * @var string
     */
    private $releasesFolder;

    /**
     * @var string|null
     */
    private $ticketBaseUrl;

    /**
     * @param array $config
     */
    public function __construct(array $configData)
    {
        $this->changelogsFolder = $configData[self::CONFIG_CHANGELOGS_FOLDER] ?? self::DEFAULT_CHANGELOGS_FOLDER;
        $this->releasesFolder = $configData[self::CONFIG_RELEASES_FOLDER] ?? self::DEFAULT_RELEASES_FOLDER;
        $this->ticketBaseUrl = $configData[self::CONFIG_TICKET_BASE_URL] ?? null;
    }

    public static function getConfigFilePath(): string
    {
        return self::getWorkingDirectoryPath() . DIRECTORY_SEPARATOR . self::DEFAULT_CHANGELOGS_SETTINGS_FILENAME;
    }

    public static function getWorkingDirectoryPath(): string
    {
        return rtrim(getcwd(), DIRECTORY_SEPARATOR);
    }

    public function getChangelogsFolder(): string
    {
        return rtrim($this->changelogsFolder, DIRECTORY_SEPARATOR);
    }

    public function getReleasesFolder(): string
    {
        return rtrim($this->releasesFolder, DIRECTORY_SEPARATOR);
    }

    public function getChangelogsFolderPath(): string
    {
        return self::getWorkingDirectoryPath() . DIRECTORY_SEPARATOR . $this->getChangelogsFolder();
    }

    public function getReleasesFolderPath(): string
    {
        return $this->getChangelogsFolderPath() . DIRECTORY_SEPARATOR . $this->getReleasesFolder();
    }

    public function getTicketBaseUrl(): ?string
    {
        if ($this->ticketBaseUrl === null) {
            return null;
        }
        
        return rtrim($this->ticketBaseUrl, '/');
    }

    public function getTicketUrl(string $ticketNumber): ?string
    {
        if ($this->getTicketBaseUrl() === null) {
            return null;
        }

        return $this->getTicketBaseUrl() . '/' . $ticketNumber;
    }

    public function toArray(): array
    {
        return [
            self::CONFIG_CHANGELOGS_FOLDER => $this->getChangelogsFolder(),
            self::CONFIG_TICKET_BASE_URL => $this->getTicketBaseUrl()
        ];
    }
}
