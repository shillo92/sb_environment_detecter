<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

class SbEnvironmentDetecterPackage extends Package
{
  protected $pkgHandle = 'sb_environment_detecter';
  protected $dependencies = array();
  protected $appVersionRequired = '5.6.1';
  protected $pkgVersion = '0.0.1';
  protected $blockHandles = array('environment_detecter');

    /**
     * @var EnvironmentDetecter_Environment
     */
    private $environment = null;


    public function on_start()
    {
        $ihm = Loader::helper('concrete/interface/menu');
        $uh  = Loader::helper('concrete/urls');
        $u   = new User();
        $env = $this->getEnvironment();

        $env->setupIfNeeded();

        $envContextName = $env->getContext()->getNickname();

        $ihm->addPageHeaderMenuItem('switchenvironment', t('Switch Environment (%s)', $envContextName), 'right', array(
            'dialog-title'    => t('Switch Environment'),
            'href'            => $uh->getToolsUrl('switchenvironment_toolbar_button', $this->getPackageHandle()),
            'dialog-on-open'  => "$(\'#ccm-page-edit-nav-switchenvironment\').removeClass(\'ccm-nav-loading\')",
            'dialog-modal'    => "false",
            'dialog-width'    => '360',
            'dialog-height'   => "100",
            'class'           => 'dialog-launch'
        ), $this->getPackageHandle());
    }

    /**
     * @return EnvironmentDetecter_Environment
     */
    public function getEnvironment()
    {
        if ($this->environment === null) {
            Loader::library('environment', $this->getPackageHandle());

            try {
                $context = EnvironmentDetecter_Environment::detectEnvironmentContextFromRequest();
            } catch(UnexpectedValueException $e) {
                $val = Loader::helper('validation/error');
                $val->add($e->getMessage());
                $val->output();
                return;
            }

            // If the context couldn't be selected through the URL (the user may not have appropriate permissions)
            // then use the default method where it's selected based on the URL host
            $this->environment = new EnvironmentDetecter_Environment(
                ($context === null) ? EnvironmentDetecter_Environment::detectEnvironmentContext() : $context
            );
        }

        return $this->environment;
    }

  public function getPackageName()
  {
    return t('Environment Detecter');
  }

  public function getPackageDescription()
  {
    return t('Detects which environment is currently being used (i.e development or production) Package');
  }

  public function install()
  {

    parent::install();
  }

  public function upgrade()
  {

    parent::upgrade();

    
  }


}
