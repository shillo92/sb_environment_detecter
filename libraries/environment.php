<?php
require_once __DIR__.'/context.php';

/**
 * Represents the environment the client uses.
 */
class EnvironmentDetecter_Environment
{
    /**
     * @var EnvironmentDetecter_Environment
     */
    private static $activeEnvironment = null;
    private static $envNamesToClasses = array(
        'development'   => 'EnvironmentDetecter_EnvironmentContext_Development',
        'cli'           => 'EnvironmentDetecter_EnvironmentContext_Cli',
        'production'    => 'EnvironmentDetecter_EnvironmentContext_Production'
    );
    /**
     * @var EnvironmentDetecter_EnvironmentContext
     */
    private $context;

    /**
     * @return string The path to the environment configurations file path.
     */
    public static function getEnvironmentConfigFilename()
    {
        return DIR_BASE.'/config/environment.php';
    }

    /**
     * @return EnvironmentDetecter_Environment The environment on which the system is running at this moment.
     */
    public static function getActiveEnvironment()
    {
        (self::$activeEnvironment !== null) ?: self::initActiveEnvironment();

        return self::$activeEnvironment;
    }

    private static function initActiveEnvironment()
    {
        $settings = self::loadEnvironmentConfigFilename();
        // Try to fetch from URL first
        if (($context = self::detectEnvironmentContextFromRequest($settings)) === null) {
            $context = self::detectEnvironmentContext($settings);
        }

        self::$activeEnvironment = new self($context);
    }

    /**
     * @return array The content returned from the environment config file.
     * @throws UnexpectedValueException Value returned from the environment.php file is not array.
     */
    public static function loadEnvironmentConfigFilename()
    {
        (!self::needToSetupEnvironmentFiles()) ?: self::setupEnvironmentFiles();

        $content = (require self::getEnvironmentConfigFilename());

        if (!is_array($content)) {
            throw new UnexpectedValueException(
                'The value return from environment.php needs to be array, '.gettype($content).' given. '.
                    'Environment path: '.self::getEnvironmentConfigFilename()
            );
        }

        return ($content);
    }

    /**
     * Checks whether the environment config file exists and is indeed a file.
     *
     * @return bool True if environment config file setup is needed.
     */
    public static function needToSetupEnvironmentFiles()
    {
        $confFile = self::getEnvironmentConfigFilename();

        return !(file_exists($confFile) && is_file($confFile));
    }

    /**
     * Copies the original environment configuration file to the site's config folder.
     *
     * @return boolean True if succeeded.
     */
    public static function setupEnvironmentFiles()
    {
        $sourceFile = __DIR__.'/../config/environment.php';
        $destFile   = self::getEnvironmentConfigFilename();

        // Copy the original configuration file
        return (boolean)file_put_contents($destFile, file_get_contents($sourceFile));
    }

    /**
     * Extracts the PASSWORD_SALT wrapped in define() call in a site configurations file.
     *
     * @param string $siteConfigData Site configurations file dta.
     * @return string|null The extracted password or null if no such define found.
     */
    private static function extractPasswordSaltFromSiteConfig($siteConfigData)
    {
        // Match the define of the PASSSWORD_SALT part in the configurations file
        $pattern = "/define\(\'PASSWORD_SALT\'\s*\,\s*\'([^\']*)\'\s*\)/";
        $matches = array();
        $match   = preg_match($pattern, $siteConfigData, $matches);

        return ($match) ? $matches[1] : null;
    }

    /**
     * @param EnvironmentDetecter_EnvironmentContext $context
     */
    public function __construct(EnvironmentDetecter_EnvironmentContext $context)
    {
        $this->setContext($context);
    }

    /**
     * Returns the EnvironmentDetecter_EnvironmentContext associated with the current environment, based on the
     * current request's URL host and rules defined in the 'environment.php'.
     *
     * @param array $settings
     * @return EnvironmentDetecter_EnvironmentContext Either a development or production environment context. If
     * no context detected, 'production' environment is assumed.
     */
    public static function detectEnvironmentContext($settings = null)
    {
        if ($settings === null) {
            $settings = self::loadEnvironmentConfigFilename();
        }

        $uri = (defined('ENVDET_CLI')) ?: parse_url($_SERVER['HTTP_HOST'], PHP_URL_HOST);
        $currentContextName = ($uri) ? array_search($uri, $settings, true) : 'production';
        $contextClassname = ($currentContextName && isset(self::$envNamesToClasses[$currentContextName])) ?
            self::$envNamesToClasses[$currentContextName] : 'EnvironmentDetecter_EnvironmentContext_Production';

        return (new $contextClassname());
    }

    /**
     * Detects the environment context from request's URL parameter called 'environment', differs from
     * detectEnvironmentContext(), which is based on the rules defined in the 'environment.php'.
     *
     * Notice: In CLI mode, this method automatically returns null as it's not supported by CLI (and shouldn't be).
     *
     * @return EnvironmentDetecter_EnvironmentContext|null Either a development or production environment context.
     * If no context detected, null is returned.
     * @throws UnexpectedValueException Unauthorized access.
     */
    public static function detectEnvironmentContextFromRequest()
    {
        if (php_sapi_name() === 'cli' || (!class_exists('User'))) {
            return;
        }

        $u = new User();
        $ui = UserInfo::getByID($u->getUserID());
        $cfgContextName = $u->config('environment_context');
        $requestContextName = (empty($_GET['environment'])) ? $cfgContextName : strtolower($_GET['environment']);

        if ($requestContextName === null) {
            return;
        }

        if ((!$u->isSuperUser()) && (!$ui->canAdmin())) {
            throw new UnexpectedValueException('Unauthorized access to switch environments. Access denied.');
        }

        $u->saveConfig('environment_context', $requestContextName);

        $contextClassname = (isset(self::$envNamesToClasses[$requestContextName])) ?
            self::$envNamesToClasses[$requestContextName] : null;

        return ($contextClassname !== null) ? new $contextClassname : null;
    }

    /**
     * Performs forceSetup() only if shouldSetup() returns true.
     *
     * @see forceSetup()
     * @see shouldSetup()
     */
    public function setupIfNeeded()
    {
        (!$this->shouldSetup()) ?: $this->forceSetup();

        require_once $this->getSiteConfig();
    }

    /**
     * Checks whether the context configurations directory not exists or is not a directory.
     *
     * @return bool True if setup should be done.
     */
    public function shouldSetup()
    {
        $configPath = $this->getContext()->getSitePathToConfigFolder();

        return (!file_exists($configPath) || !is_dir($configPath));
    }

    /**
     * Forces setup of the environment configuration files, based on the context settings.
     *
     * This method does not create the 'environment.php' file but the context folder itself, for example:
     * If our environment context was production (i.e EnvironmentDetecter_Environment_Production) then a folder
     * named 'production' would be created with a settings file called 'site.php'.
     *
     * @return boolean True if setup succeeded.
     * @see setupIfNeeded()
     */
    public function forceSetup()
    {
        $configDirPath  = $this->getContext()->getSitePathToConfigFolder();
        $pkgConfigPath  = $this->getSiteConfig(true);
        $siteConfigPath = $this->getSiteConfig();
        $pkgSiteConfigData = file_get_contents($pkgConfigPath);
        $siteConfigContents = str_replace(
            '%SALT%',
            self::extractPasswordSaltFromSiteConfig(file_get_contents(DIR_BASE.'/config/site.php')),
            $pkgSiteConfigData
        ); // Use the same salt key as used in the original site.php

        (file_exists($configDirPath)) ?: mkdir($this->getContext()->getSitePathToConfigFolder());
        $flag = file_put_contents($siteConfigPath, $siteConfigContents);

        require_once $siteConfigPath;

        return $flag;
    }

    /**
     * @param \EnvironmentDetecter_EnvironmentContext $context
     */
    protected function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * @return \EnvironmentDetecter_EnvironmentContext
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param boolean $pkg
     * @return string The absolute path to the environment site's configuration, based on the context config folder
     * path.
     */
    public function getSiteConfig($pkg = false)
    {
        $basepath = ($pkg) ? __DIR__.'/../config' : DIR_BASE.'/config';

        return $basepath.'/'.$this->getContext()->getConfigFolderPath().'/site.php';
    }
}