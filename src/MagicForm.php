<?php

namespace magicsoft\form;

use kartik\form\ActiveForm;
use magicsoft\base\MagicSoftModule;
use magicsoft\base\TranslationTrait;
use yii\helpers\ArrayHelper;

class MagicForm extends ActiveForm
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

        $this->id = $this->formId ? $this->formId : strtolower($this->model->formName() . '-form');
        $this->setFormat = $this->setFormat ? $this->setFormat : $this->getSetFormat();

        parent::init();

        if($this->setFormat) $this->magicView = MagicView::begin(array_merge($this->formatOptions, ['form' => $this, 'model' => $this->model]));
    }

    public static function end()
    {
        $widget = end(self::$stack);
        parent::end();
        if (get_class($widget) === get_called_class()) {
            /* @var $widget Widget */
            if($widget->setFormat) $widget->magicView::end();
        }
    }

    private function getSetFormat()
    {
        return ArrayHelper::getValue(\Yii::$app->request->getQueryParams(), 'magic_modal_name', true);
    }
}
