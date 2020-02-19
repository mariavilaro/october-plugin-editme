<?php namespace Fw\EditMe;

use System\Classes\PluginBase;
use Fw\EditMe\Models\Settings;

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

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'fw.editme::lang.settings.name',
                'description' => 'fw.editme::lang.settings.description',
                'category'    => \System\Classes\SettingsManager::CATEGORY_SYSTEM,
                'icon'        => 'icon-code',
                'class'       => 'Fw\EditMe\Models\Settings',
                'permissions' => ['backend.manage_editor'],
                'order'       => 600,
            ]
        ];
    }

    public function boot()
    {
        $froala_custom_defaults = Settings::get('froala_custom_defaults_file');
        if ($froala_custom_defaults) {
            \Backend\Classes\Controller::extend(function ($controller) use ($froala_custom_defaults) {
                $controller->addJs('/storage/app/media/fw_editme/'.$froala_custom_defaults);
            });
        }
    }

}