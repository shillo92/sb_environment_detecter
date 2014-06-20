<?php
defined('C5_EXECUTE') or die('Access Denied.');

class SwitchEnvironmentConcreteInterfaceMenuItemController extends ConcreteInterfaceMenuItemController
{
    public function displayItem()
    {
        // button is always enabled
        return true;
    }

    public function switchEnvironment()
    {

    }
}