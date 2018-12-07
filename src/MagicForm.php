<?php

namespace magicsoft\form;

use kartik\form\ActiveForm;
use yii\helpers\ArrayHelper;

class MagicForm extends ActiveForm
{
    public $formatOptions = [];
    public $model;
    public $formId;
    public $setFormat;

    public function init()
    {
        $this->id = $this->formId ? $this->formId : strtolower($this->model->formName() . '-form');
        $this->setFormat = $this->setFormat ? $this->setFormat : $this->getSetFormat();
        parent::init();

        if($this->setFormat) MagicView::begin(array_merge($this->formatOptions, ['set_form' => true, 'model' => $this->model]));
    }

    public static function end(){
        $widget = end(self::$stack);
        if (get_class($widget) === get_called_class()) {
            /* @var $widget Widget */
            if($widget->setFormat) MagicView::end();
        }
        parent::end();
    }

    private function getSetFormat(){
        return ArrayHelper::getValue(\Yii::$app->request->getQueryParams(), 'magic_modal_name', false);
    }
}
