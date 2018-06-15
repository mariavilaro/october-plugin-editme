<?php namespace Fw\EditMe\Components;

use BackendAuth;
use Cms\Classes\ComponentBase;
use RainLab\Translate\Classes\Translator;
use RainLab\Translate\Models\Message;
use System\Helpers\Cache as CacheHelper;

class EditMe extends ComponentBase
{
    public $content;
    public $isEditor;
    public $message;
    public $model_class;
    public $model_id;

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
                'title' => 'fw.editme::lang.component_editme.property_message.title',
                'description' => 'fw.editme::lang.component_editme.property_message.description',
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
            $this->addJs('assets/js/editme.js?v=1.0.2');
        }
    }

    public function onRender()
    {
        $this->message = $this->property('message');

        if ($this->property('model')) {
            $model = $this->property('model');
            $message = $this->message;
            $content = $model->$message;
            //reset optional model property, if we don't do this and the next component in template doesn't set it, it won't be empty!
            $this->setProperty('model', '');
        } else {
            $content = Message::trans($this->message);
        }

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

        if (isset($model)) {
            $this->model_class = get_class($model);
            $this->model_id = $model->id;
        } else {
            //reset properties for next component
            $this->model_class = NULL;
            $this->model_id = NULL;
        }
    }

    public function onSave()
    {
        if (!$this->checkEditor())
            return;
        $key = post('message');
        $content = post('content');
        $locale = Translator::instance()->getLocale();

        if (post('model')) {
            $modelClass = post('model')['model'];
            $model = $modelClass::findOrFail(post('model')['id']);
            $model->$key = $content;
            $model->save();
        } else {
            $message = Message::where('code', Message::makeMessageCode($key))->first();
            if ($content != $message->forLocale($locale)) {
                $message->toLocale($locale, $content);
                CacheHelper::clear();
            }
        }
    }

    public function checkEditor()
    {
        $backendUser = BackendAuth::getUser();
        return $backendUser && ($backendUser->hasAccess('cms.manage_content') || $backendUser->hasAccess('rainlab.pages.manage_content'));
    }

}