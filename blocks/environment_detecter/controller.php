<?php
defined('C5_EXECUTE') or die("Access Denied.");



class EnvironmentDetecterBlockController extends BlockController {


  protected $btTable                              = 'btEnvironmentDetecter';
  protected $btInterfaceWidth                     = "450";
  protected $btInterfaceHeight                    = "450";
  protected $btWrapperClass                       = 'ccm-ui';
  protected $btCacheBlockRecord                   = true;
  protected $btCacheBlockOutput                   = true;
  protected $btCacheBlockOutputOnPost             = true;
  protected $btCacheBlockOutputForRegisteredUsers = true;
  

  public function getBlockTypeName() {
    return t('EnvironmentDetecter');
  }

  public function getBlockTypeDescription() {
    return t('Detects which environment is currently being used (i.e development or production)');
  }

  

  

  


  

  


  



  


  


}



?>
