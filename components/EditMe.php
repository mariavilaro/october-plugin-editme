<?php namespace Fw\EditMe\Components;

use BackendAuth;
use Cms\Classes\Content;
use Cms\Classes\ComponentBase;
use RainLab\Translate\Classes\Translator;
use RainLab\Translate\Models\Message;
use System\Helpers\Cache as CacheHelper;

class EditMe extends ComponentBase
{
    public $content;
    public $isEditor;
    public $message;

    public function componentDetails()
    {
        return [
            'name' => 'fw.editme::lang.component_editme.name',
            'description' => 'fw.editme::lang.component_editme.description',
        ];
    }

    public function defineProperties()
    {
        return [
            'message' => [
                'title' => 'fw.editme::lang.component_editme.property_file.title',
                'description' => 'fw.editme::lang.component_editme.property_file.description',
                'default' => ''
            ]
        ];
    }

    public function onRun()
    {
        $this->isEditor = $this->checkEditor();

        if ($this->isEditor) {
            $this->addCss('assets/vendor/redactor/redactor.css');
            $this->addJs('assets/vendor/redactor/redactor.js');

            $this->addCss('assets/css/editme.css');
            $this->addJs('assets/js/editme.js');
        }
    }

    public function onRender()
    {
        $this->message = $this->property('message');

        $content = Message::trans($this->message);

        //replace paragraphs with break lines
        $content = str_replace(array('<p>','</p>'),array('','<br />'), $content);
        //remove all html tags except break lines
        $content = strip_tags($content, '<br>');
        //remove EOL
        $content = preg_replace( "/\r|\n/", "", $content);
        //remove excess <br> or <br /> from the end of the text
        $content = preg_replace('#(( ){0,}<br( {0,})(/{0,1})>){1,}$#i', '', $content);

        if (!$this->isEditor)
            return $content;

        $this->content = $content;
    }

    public function onSave()
    {
        if (!$this->checkEditor())
            return;
        $key = post('message');
        $content = post('content');
        $locale = Translator::instance()->getLocale();
        //Message::setContext($locale);
        $message = Message::where('code', Message::makeMessageCode($key))->first();
        if ($content != $message->forLocale($locale)) {
            $message->toLocale($locale, $content);
            CacheHelper::clear();
        }
    }

    public function checkEditor()
    {
        $backendUser = BackendAuth::getUser();
        return $backendUser && ($backendUser->hasAccess('cms.manage_content') || $backendUser->hasAccess('rainlab.pages.manage_content'));
    }

}