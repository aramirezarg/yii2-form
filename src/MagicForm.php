<?php

namespace magicsoft\form;

use magicsoft\base\MagicSoftModule;
use magicsoft\base\TranslationTrait;
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

        $this->id = $this->formId ? $this->formId : strtolower(  (isset($this->model->formName) ? $this->model->formName() : '--') . '-form' );
        $this->setFormat = $this->setFormat ? $this->setFormat : $this->getSetFormat();

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
        //parent::end();
    }

    private function getSetFormat()
    {
        return ArrayHelper::getValue(\Yii::$app->request->getQueryParams(), 'magic_modal_name', true);
    }
}
