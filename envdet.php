#!php -q
<?php
require_once 'libraries/cli/cli.php';
require_once 'libraries/environment.php';

defined('DIR_BASE') OR define('DIR_BASE', realpath(__DIR__.'/../../'));
defined('C5_EXECUTE') OR define('C5_EXECUTE', true);
(php_sapi_name() !== "cli") ?: define('ENVDET_CLI', true);

final class EnvdetCli extends CLI
{
    /**
     * @var EnvironmentDetecter_Environment
     */
    private $environment;

    public function __construct($appname = null, $author = null, $copyright = null) {
        parent::__construct(
            'Environment Detecter CLI Tool for Concrete5',
            'Shillo Ben David',
            '(c) Shillo Ben David 2014');
    }

    /**
     * @return EnvironmentDetecter_Environment
     */
    public function getEnvironment()
    {
        if (!$this->environment) {
            $this->environment = EnvironmentDetecter_Environment::getActiveEnvironment();
        }

        return $this->environment;
    }

    public function main()
    {
        echo "\r\n";
//        $input = '';
//
//        while($input != 'yes'){
//            $input = $this->getInput('Type yes to continue. Type no to exit.');
//            if($input == ''){
//                exit();
//            }
//        }
    }

    # Vars
    private $flag_force = false;
    private $flag_info  = false;
    private $flag_env   = false;
    private $flag_production = false;

    # Options
    public function argument_setupProduction($opt = null)
    {
        if ($opt === 'help') {
            return 'Setups the \'production\' environment configurations folder.';
        }

        $env = $this->getEnvironment();

        if ((!$env->shouldSetup()) && (!$this->flag_force)) {
            echo $this->colorText('Production environment folder already exist, use the -f to force setup.', 'RED');
            return;
        }

        echo ($env->forceSetup()) ?
            $this->colorText(
                'Successfully setup the production environment! You can view the folder\'s contents using the \'-p\' flag',
                'GREEN') :
            $this->colorText('Could not setup the production environment due to unknown error.', 'RED');
    }

    public function argument_setupEnv($opt = null)
    {
        if ($opt === 'help') {
            return "Setups the environment.php in the site's config folder.";
        }

        if ((!EnvironmentDetecter_Environment::needToSetupEnvironmentFiles()) && (!$this->flag_force)) {
            echo $this->colorText('Environment file already exist, use the -f to force setup.', 'RED');
            return;
        }

        echo (EnvironmentDetecter_Environment::setupEnvironmentFiles()) ?
            $this->colorText(
                'Successfully setup the environment file! You can view it\'s contents using the \'-e\' flag',
                'GREEN') :
            $this->colorText('Could not setup the environment file due to unknown error.', 'RED');
    }

    # Flags
    public function flag_f($opt = null)
    {
        if ($opt === 'help') {
            return 'Forces actions such as setup';
        }

        $this->flag_force = true;
    }

    public function flag_e($opt = null)
    {
        if ($opt === 'help') {
            return 'Shows the environments defined in the config/environments.php file.';
        }

        $this->flag_env = true;

        $environments = require EnvironmentDetecter_Environment::getEnvironmentConfigFilename();

        if (empty($environments)) {
            return $this->colorText('No environments set.', 'MAGENTA');
        }

        $mask = "|%-20.30s |%-30.30s |\r\n";

        printf($mask, 'Name', 'Host');

        foreach ($environments as $name => $host) {
            printf($mask, $name, $host);
        }
    }

    public function flag_p($opt = null)
    {
        if ($opt === 'help') {
            return 'Shows the contents of the config/production/site.php file.';
        }

        $this->flag_production = true;

        $env = $this->getEnvironment();

        if ($env->shouldSetup()) {
            echo $this->colorText('Production environment folder was not setup. Use \'setupProduction\'', 'RED');
            return;
        }

        echo file_get_contents($env->getSiteConfig());
    }
}

new EnvdetCli();