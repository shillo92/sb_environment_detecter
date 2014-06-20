<?php
/**
 * Contains information about the environment being used and how it's setup.
 */
abstract class EnvironmentDetecter_EnvironmentContext
{
    /**
     * @var string
     */
    private $baseUrl;

    public function __construct()
    {
    }

    /**
     * @param string $baseUrl
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @return string The absolute path to the context's config folder path in the site folder.
     */
    public function getSitePathToConfigFolder()
    {
        return DIR_BASE.'/config/'.$this->getConfigFolderPath();
    }

    /**
     * @return string A short nickname representing this environment.
     */
    abstract public function getNickname();

    /**
     * @return string Relative path to the environment configurations folder.
     */
    abstract public function getConfigFolderPath();
}


class EnvironmentDetecter_EnvironmentContext_Production extends EnvironmentDetecter_EnvironmentContext
{
    /**
     * @return string A short nickname representing this environment.
     */
    public function getNickname()
    {
        return 'production';
    }

    /**
     * @return string Where the environment configurations folder is.
     */
    public function getConfigFolderPath()
    {
        return 'production';
    }
}

class EnvironmentDetecter_EnvironmentContext_Development extends EnvironmentDetecter_EnvironmentContext
{
    /**
     * @return string A short nickname representing this environment.
     */
    public function getNickname()
    {
        return 'development';
    }

    /**
     * @return string Where the environment configurations folder is.
     */
    public function getConfigFolderPath()
    {
        return '';
    }
}

class EnvironmentDetecter_EnvironmentContext_Cli extends EnvironmentDetecter_EnvironmentContext_Development {
    public function getNickname()
    {
        return 'cli';
    }

}