<?php
/**
 * User: Alfredo Ramirez
 * Date: 22/3/2018
 * Time: 17:59
 */

namespace magicsoft\form;

use magicsoft\base\MagicFormatter;
use magicsoft\base\MagicSelectHelper;
use magicsoft\base\MagicSoftModule;
use magicsoft\base\TranslationTrait;
use Yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class MagicView extends Widget
{
    use TranslationTrait;
    
    public $model;
    public $form;

    public $title;
    public $subTitle;
    public $buttons;
    public $subContainer = true;

    public $baseView = '@vendor/magicsoft/yii2-form/src/views';

    public function init()
    {
        $this->initI18N(MagicSoftModule::getSorceLangage(), 'magicview');
        
        $this->beginPanel();
        parent::init();
    }

    private function getDefaultMessage($message){
        $defultSubTitles = [
            'view' => Yii::t('magicview', 'DETAIL VIEW'),
            'create' => Yii::t('magicview', 'CREATE'),
            'update' => Yii::t('magicview', 'UPDATE'),
            'index' => Yii::t('magicview', 'GENERAL VIEW'),
            'print' => Yii::t('magicview', 'PRINT'),
            'pdf' => Yii::t('magicview', 'PDF'),
        ];
        return ArrayHelper::getValue($defultSubTitles, $message, '');
    }

    public static function end()
    {
        $widget = end(self::$stack);

        if (get_class($widget) === get_called_class()) {
            /* @var $widget Widget */
            $widget->endPanel();
        }

        parent::end();
    }


    private function beginPanel()
    {
        $html = '';
        $html .= Html::beginTag('div', ['class' => $this->panelClass()]);
        $html .= $this->headPanel();
        $html .= Html::beginTag('div', ['class' => $this->bodyClass()]);

        echo $html;
    }

    private function panelClass(){return $this->isModal() ? 'modal-content' : 'panel panel-default card';}
    private function headClass(){return $this->isModal() ? 'magic-head modal-header' : 'panel-heading card-header';}
    private function bodyClass(){return $this->isModal() ? 'modal-body' : 'panel-body card-body';}
    private function footerClass(){return $this->isModal() ? 'modal-footer' : 'panel-footer card-footer';}

    private function endPanel()
    {
        $html = '';

        $html .= Html::endTag('div');

        $html .= $this->footerPanel();

        $html .= Html::endTag('div');

        echo $html;
    }

    private function getModelName()
    {
        return $this->model ? $this->model->formName() : Yii::$app->controller->id;
    }

    private function headPanel()
    {
        return Html::tag('div', $this->getHtmlTitle(), ['class' => $this->headClass(), 'style' => 'padding-top: 10px; ' . ($this->bsVersion() == 4 ? '' : 'padding-left: 25px; padding-right: 25px')]);
    }

    private function bsVersion()
    {
        return substr(ArrayHelper::getValue(Yii::$app->params, 'bsVersion', '3'), 0, 1);
    }

    private function footerPanel()
    {
        return Html::tag('div', $this->getButtons(), ['class' => $this->footerClass(), 'style' => 'text-align: right;']);
    }

    private function getHtmlTitle(){
        return Html::tag('div',
            Html::tag('div',
                Html::tag('div',
                    Html::tag('a',
                        $this->getTitle() . " | " . $this->getSubTitle(),
                        ['class' => 'title card-title', 'style' => 'font-size: 22px']
                    ),
                    ['class' => 'col-md-7 col-8', 'style' => 'text-align: left; padding-left: 0']
                ) .
                Html::tag('div',
                    (!$this->hasForm() || !$this->isModal()) ? $this->getButtons() : '',
                    ['class' => 'col-md-5 col-4 magic-modal-buttons', 'style' => 'text-align: right; padding-right: 0;']
                ),
                ['class' => 'row']
            ),
            ['class' => 'col-12']
        );
    }

    private function getIcon()
    {
        return Html::tag('i', '', ['class' => 'fa fa-list-ul', 'style' => 'padding-right: 5px; ']);
    }

    private function getTitle()
    {
        return  Html::tag('strong', ucwords($this->title ? $this->title : ($this->model ? $this->model->formName() : Yii::$app->controller->id)));
    }

    private function getSubTitle()
    {
        return Html::tag('small',
            $this->subTitle ? MagicFormatter::UpperCase($this->subTitle) : $this->getDefaultMessage($this->getAction()), ['style' => '']
        );
    }

    private function getAction(){
        return Yii::$app->controller->action->id;
    }

    private function getButtons()
    {
        $thml = '';
        if (is_array($this->buttons)){
            foreach ($this->buttons as $button) {
                $thml .= $button;
            }
        }else if ($this->buttons !== false) {
            if ($this->hasForm()) {
                $thml .= $this->view->render($this->baseView . '/footer_form', ['model' => $this->model]);
            }else{
                switch ($this->getAction()){
                    case 'index' :$thml .= $this->setButtonsIndex(); break;
                    case 'view' : $thml .= $this->setButtonsView(); break;
                }
            }
        }
        return $thml;
    }

    private function hasForm(){
        return $this->getAction() == 'create' or $this->getAction() == 'update' or $this->form;
    }

    public function setButtonsIndex()
    {
        $buttons = '';
        $unsetButtons = [];

        $html = '';
        if(is_array($this->buttons)){
            $unsetButtons = ArrayHelper::getValue($this->buttons, 'unsetButtons', []);

            $_buttons = ArrayHelper::getValue($this->buttons, 'buttons', []);
            foreach ($_buttons as $button) {
                $buttons .= $button;
            }
        }

        if (method_exists($this->getController(), 'actionCreate') && !in_array('create', $unsetButtons)) {
            $buttons .= Html::a(
                Yii::t('magicview', 'Create'),
                ['create'],
                [
                    'class' => ($this->configModelIsAjax() ? 'magic-modal' : '') . ' btn btn-success',
                    'jsFunctions'   => 'afterExecute:location.reload(),beforeLoad:false,whenClose:location.reload(),activeWhenClose:false',
                ]
            );
        }

        return $html . $buttons;
    }

    private function requestIsAjax(){
        return Yii::$app->request->isAjax;
    }

    private function configModelIsAjax(){
        return $this->isModal() || MagicSelectHelper::isAjax();
    }

    private function isModal(){
        return ArrayHelper::getValue(\Yii::$app->request->getQueryParams(), 'magic_modal_name', false);
    }

    private function setButtonsView()
    {
        $requestIsAjax = $this->requestIsAjax();
        $buttons = '';
        if (method_exists($this->getController(), 'actionUpdate')) {
            $buttons .= Html::a(
                Yii::t('magicview', 'Update'),
                ['update', 'id' => $this->model->id],
                [
                    'magic-modal-name' => $this->model->formName(),
                    'jsFunctions' => 'afterExecute:' . (
                        $requestIsAjax ? (
                                $this->getCallBack() ? $this->getCallBack() : 'View' . $this->model->formName() . '.execute()'
                            ) : 'location.reload()'
                        ) . ',beforeLoad:false,whenClose:' . ($requestIsAjax ? $this->model->formName() . '.setActiveWhenClose()' : '') . ',activeWhenClose:true',
                    'class' =>  ($this->configModelIsAjax() ? 'magic-modal' : '') . ' btn btn-primary',
                    'type' => 'button'
                ]
            );
        }

        if (method_exists($this->getController(), 'actionDelete')) {
            $buttons .= Html::a(
                Yii::t('magicview', 'Delete'),
                ['delete', 'id' => $this->model->id],
                [
                    'class' => 'magic-confirm btn btn-warning',
                    'type' => 'button'
                ]
            );
        }

        if (method_exists($this->getController(), 'actionPdf')) {
            $buttons .= Html::a(
                'PDF ',
                ['pdf', 'id' => $this->model->id],
                [
                    'class' => 'btn btn-danger',
                    'target' => '_blank',
                    'type' => 'button'
                ]
            );
        }

        return $buttons;
    }

    public function getClassController()
    {
        return get_class(Yii::$app->controller);
    }

    public function getController()
    {
        $_controller = Yii::$app->controller->id;

        $class_controller = $this->getClassController();

        return new $class_controller(1, $_controller, '');
    }

    /**
     * @return mixed|string
     */
    private function getModule()
    {
        if($this->model){
            foreach ($_class = explode('\\', $this->model->className()) as $key => $value){
                if($value == 'modules'){
                    return ArrayHelper::getValue($_class, $key + 1, null);
                    break;
                }
            }
        }else{
            $_controller = Yii::$app->controller->id;

            $class_controller = $this->getClassController();

            return new $class_controller(1, $_controller, '');
        }

        return null;
    }

    private function getCallBack()
    {
        return Yii::$app->request->isAjax ? ArrayHelper::getValue(Yii::$app->request->getQueryParams(), 'callback', '') : null;
    }
}