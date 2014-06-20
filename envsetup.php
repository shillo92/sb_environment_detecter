<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

/**
 * Setups the environment, should be called in site.php before every define() call.
 *
 * In site.php, before every define() call add the following line:
 * <code>
 * // This will stop the execution of the rest of the code if were in production environment.
 * if (SbEnvironmentDetecterPackage::setupEnvironment() instanceof EnvironmentDetecter_EnvironmentContext_Production) {
 *  return;
 * }
 * </code>
 *
 * @return EnvironmentDetecter_Environment Returns the environment that was setup.
 */
function SbEnvironmentDetecter_setupEnvironment()
{
    require_once __DIR__.'/libraries/environment.php';

    $env = EnvironmentDetecter_Environment::getActiveEnvironment();

    $env->setupIfNeeded();

    return $env;
}