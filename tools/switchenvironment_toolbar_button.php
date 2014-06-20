<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

$u   = new User();
$package = Loader::package('sb_environment_detecter');
$envContextName = $package->getEnvironment()->getContext()->getNickname();
$ih = Loader::helper('concrete/interface');
$uh  = Loader::helper('concrete/urls');
$nh = Loader::helper('navigation');
$baseUrl = DIR_REL.'?environment=';
?>

<div class="switchenvironment-actions ccm-ui">
    <div>
        <?php echo t('Currently used environment: <b>%s</b>', $envContextName); ?>
    </div>
    <div>
        <a class="switchenvironment-trigger btn btn-large" href="<?php echo $baseUrl.'production'; ?>" data-envtarget="production"><?php echo t('Switch to production'); ?></a>
        <a class="switchenvironment-trigger btn btn-large" href="<?php echo $baseUrl.'development'; ?>" data-envtarget="development"  class="btn primary"><?php echo t('Switch to development'); ?></a>
    </div>
</div>

<div class="dialog-buttons">
    <a href="javascript:void(0)" class="btn primary ccm-button-right" onclick="jQuery.fn.dialog.closeTop();">
    <?php echo t('Close'); ?>
    </a>
</div>