<?php

namespace magicsoft\form;

use magicsoft\base\MagicSoftModule;
use magicsoft\base\TranslationTrait;
use ReflectionClass;
use yii\base\InvalidConfigException;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;

class MagicForm extends \kartik\form\ActiveForm
{
    use TranslationTrait;

    public $formatOptions = [];
    public $model;
    public $formId;
    public $setFormat;
    public $magicView;
    public $baseView = '@vendor/magicsoft/yii2-form/src/views';

    public function init()
    {
        $this->initI18N(MagicSoftModule::getSorceLangage(), 'magicform');

        $this->id = $this->formId ? $this->formId : $this->getModelFormName();

        $this->setFormat = ($this->setFormat === null ? true : ($this->setFormat === false ? false : $this->getSetFormat()));

        parent::init();

        if($this->setFormat) $this->magicView = MagicView::begin(array_merge($this->formatOptions, ['form' => $this, 'model' => $this->model]));
    }

    public static function end()
    {
        $widget = end(self::$stack);

        if (get_class($widget) === get_called_class()) {
            /* @var $widget Widget */
            if($widget->setFormat){
                parent::end();
                $widget->magicView::end();

            }else{
                parent::end();
            }
        }
    }

    private function getSetFormat()
    {
        return ArrayHelper::getValue(\Yii::$app->request->getQueryParams(), 'magic_modal_name', true);
    }

    private function getModelFormName()
    {
        $reflector = new ReflectionClass($this->model);
        if (PHP_VERSION_ID >= 70000 && $reflector->isAnonymous()) {
            throw new InvalidConfigException('The "formName()" method should be explicitly defined for anonymous models');
        }

       return strtolower($reflector->getShortName() . '-form');
    }
}
