<?php namespace Fw\EditMe\Components;

use BackendAuth;
use Cms\Classes\ComponentBase;
use RainLab\Translate\Classes\Translator;
use RainLab\Translate\Models\Message;
use System\Helpers\Cache as CacheHelper;
use Url;
use Backend;
use Fw\EditMe\Models\Settings;

class EditMe extends ComponentBase
{
    use \Backend\Traits\UploadableWidget;

    public $content;
    public $isEditor;
    public $message;
    public $model_class;
    public $model_id;
    public $ace_vendor_path;
    public $type;
    public $toolbarButtons;
    public $csrf_token;

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

            $this->addJs('/modules/system/assets/ui/js/foundation.baseclass.js');
            $this->addJs('/modules/system/assets/ui/js/foundation.controlutils.js');
            $this->addCss('/modules/backend/formwidgets/richeditor/assets/css/richeditor.css', 'core');
            $this->addJs('/modules/backend/formwidgets/richeditor/assets/js/build-min.js', 'core');
            $this->addJs('/modules/backend/formwidgets/richeditor/assets/js/build-plugins-min.js', 'core');
            $this->addJs('/modules/backend/formwidgets/codeeditor/assets/js/build-min.js', 'core');

            $froala_custom_defaults = Settings::get('froala_custom_defaults_file');
            if ($froala_custom_defaults) {
                $this->addJs('/storage/app/media/fw_editme/'.$froala_custom_defaults);
            }

            $this->addCss('assets/css/editme.css?v=1.0.8');
            $this->addJs('assets/js/editme.js?v=1.0.8');

            $this->ace_vendor_path = Url::asset('/modules/backend/formwidgets/codeeditor/assets/vendor/ace');

            $this->csrf_token = csrf_token();
        }
    }

    public function onRender()
    {
        $this->isEditor = $this->checkEditor();
        $this->message = $this->property('message');
        $this->type = $this->property('type');
        $this->toolbarButtons = $this->property('toolbarButtons');
        $this->setProperty('type', '');
        $this->setProperty('toolbarButtons', '');

        if ($this->property('model')) {
            $model = $this->property('model');
            $message = $this->message;
            $content = $model->$message;
            //reset optional model property, if we don't do this and the next component in template doesn't set it, it won't be empty!
            $this->setProperty('model', '');
        } else {
            //TODO: check if message already exists in db, and if not, load default message from theme config files if it exists
            $content = Message::trans($this->message);
        }

        if ($this->type != 'richeditor') {
            //replace paragraphs with break lines
            $content = str_replace(array('<p>','</p>'),array('','<br />'), $content);
            //remove all html tags except break lines
            $content = strip_tags($content, '<br>');
            //remove EOL
            $content = preg_replace( "/\r|\n/", "", $content);
            //remove excess <br> or <br /> from the end of the text
            $content = preg_replace('#(( ){0,}<br( {0,})(/{0,1})>){1,}$#i', '', $content);
        }

        if (!$this->isEditor)
            return $content;

        if (!$content) {
            $content = "[empty]";
        }

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
        if (!$this->checkEditor()) {
            return;
        }

        $locale = Translator::instance()->getLocale();

        $key = post('message');
        $content = post('content');

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
        return $backendUser && ($backendUser->hasAccess('rainlab.translate.manage_messages'));
    }

}