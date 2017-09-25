<?php namespace Fw\EditMe;

use System\Classes\PluginBase;

/**
 * EditMe Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * @var array Plugin dependencies
     */
    public $require = ['RainLab.Translate'];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name' => 'fw.editme::lang.plugin.name',
            'description' => 'fw.editme::lang.plugin.description',
            'author' => 'Maria Vilaró',
            'icon' => 'icon-leaf'
        ];
    }

    public function registerComponents()
    {
        return [
            'Fw\EditMe\Components\EditMe' => 'editme'
        ];
    }
}