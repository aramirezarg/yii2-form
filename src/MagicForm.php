<?php

namespace magicsoft\form;

use kartik\form\ActiveForm;
use yii\helpers\ArrayHelper;

class MagicForm extends ActiveForm
{
    public $optionsView = [];
    public $model;
    public $setFormat = true;

    public function init()
    {
        $this->model = ArrayHelper::getValue($this->optionsView, 'model', $this->model);
        $this->id = $this->id ? $this->id : $this->model->formName() . '-form';
        parent::init();
        if($this->setFormat) MagicView::begin(array_merge($this->optionsView, ['set_form' => true, 'model' => $this->model]));
    }

    public static function end($set_format = true){
        if($set_format) MagicView::end();
        parent::end();
    }
}
