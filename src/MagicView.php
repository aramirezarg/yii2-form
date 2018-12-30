<?php
/**
 * User: Alfredo Ramirez
 * Date: 22/3/2018
 * Time: 17:59
 */

namespace magicsoft\form;

use magicsoft\base\MagicSelectHelper;
use magicsoft\base\MagicSoftModule;
use magicsoft\base\TranslationTrait;
use webvimark\modules\UserManagement\components\GhostHtml;
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

    public $sources = '@vendor/magicsoft/yii2-form/src/views';

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

    public static function begin($config = [])
    {
        return parent::begin($config);
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
        if ($this->form) {
            $html .= Html::beginTag('div', ['class' => $this->getModelName() . '-form']);
        }

        $html .= Html::beginTag('div', ['class' => 'box box-default']);
        $html .= $this->headPanel();
        if($this->subContainer) $html .= Html::beginTag('div', ['class' => 'box-body']);

        echo $html;
    }

    private function endPanel(){
        $html = '';

        if($this->subContainer) $html .= Html::endTag('div');

        if($this->form) $html .= $this->footerPanel();

        $html .= Html::endTag('div');
        
        if ($this->form) {
            $html .= Html::endTag('div');
        }
        echo $html;
    }

    private function getModelName()
    {
        return $this->model ? $this->model->formName() : Yii::$app->controller->id;
    }

    private function headPanel()
    {
        return Html::tag('div',$this->getHtmlTitle(), ['class' => 'box-header header-form modal-header', 'style' => 'background-color: whitesmoke']);
    }

    private function bodyPanel(){}

    private function footerPanel(){
        return Html::tag('div', $this->getFormButtons(), ['class' => 'box-footer footer-form control_modal no-margin', 'style' => 'background-color: whitesmoke']);
    }

    private function getHtmlTitle(){
        return Html::tag('h3', $this->getIcon() . $this->getTitle() . $this->getDivisor() . $this->getSubTitle(), ['class' => 'box-title']) . $this->getButtons();
    }

    private function getIcon()
    {
        return Html::tag('i', '', ['class' => 'fa fa-list-ul', 'style' => 'font-size: x-large; text-decoration: none; padding-right: 5px; color:orange;']);
    }

    private function getTitle()
    {
        return Html::tag('strong',
            ucwords($this->title ? $this->title : ($this->model ? $this->model->formName() : Yii::$app->controller->id)),
            ['style' => 'font-size: x-large; text-decoration: none; color:black']
        );
    }

    private function getDivisor()
    {
        return Html::tag('i', '', ['class' => 'ion ion-ios-arrow-right', 'style' => 'padding-right: 5px; padding-left: 5px;']);
    }

    private function getSubTitle()
    {
        return Html::tag('small',
            $this->subTitle ? MagicFormatter::UpperCase($this->subTitle) : $this->getDefaultMessage($this->getAction()),
            ['style' => 'font-size: large; text-decoration:none;']
        );
    }

    private function getAction(){
        return Yii::$app->controller->action->id;
    }

    private function getButtons()
    {
        $thml = Html::beginTag('div', ['class' => 'box-tools pull-right for-buttons-modal']);
        if (is_array($this->buttons)){
            foreach ($this->buttons as $button) {
                $thml .= $button;
            }
        }else if ($this->buttons !== false) {
            switch ($this->getAction()){
                case 'index' :$thml .= $this->setButtonsIndex(); break;
                case 'view' : $thml .= $this->setButtonsView(); break;
            }
        }
        return $thml .= Html::endTag('div');
    }

    private function getFormButtons()
    {
        $thml = Html::beginTag('div', ['class' => 'box-tools pull-right']);
        if (is_array($this->buttons)){
            foreach ($this->buttons as $button){
                $thml .= $button;
            }
        }else if ($this->buttons !== false) {
            if ($this->getAction() == 'create' or $this->getAction() == 'update' or $this->form) {
                $thml .= $this->view->render($this->sources . '/footer_form', ['model' => $this->model]);
            }else{
                switch ($this->getAction()){
                    case 'index' :$thml .= $this->setButtonsIndex(); break;
                    case 'view' : $thml .= $this->setButtonsView(); break;
                    case 'create' : $thml .= '<a class="btn btn-defautl">Save</a>'; break;
                    case 'update' : $thml .= '<a class="btn btn-defautl">Save</a>'; break;
                }
            }
        }
        return $thml .= Html::endTag('div');
    }

    private function isModal(){
        return ArrayHelper::getValue(\Yii::$app->request->getQueryParams(), 'magic_modal_name', false);
    }

    public function setButtonsIndex()
    {
        $buttons = '';
        $unsetButtons = [];

        $html = Html::beginTag('div', ['class' => 'box-tools pull-right']);
        if(is_array($this->buttons)){
            $unsetButtons = ArrayHelper::getValue($this->buttons, 'unsetButtons', []);

            $_buttons = ArrayHelper::getValue($this->buttons, 'buttons', []);
            foreach ($_buttons as $button) {
                $buttons .= $button;
            }
        }

        if (method_exists($this->getController(), 'actionCreate') && !in_array('create', $unsetButtons)) {
            $buttons .= GhostHtml::a(
                '<span class="ion ion-android-add"></span> ' . Yii::t('magicview', 'Create'),
                ['create'],
                [
                    'class' =>  'magic-modal btn btn-social btn-flat btn-success btn-group',
                    'ajaxOptions' => 'confirmToClose:true,confirmToSend:true',
                    'jsFunctions'   => 'afterExecute:location.reload(),beforeLoad:false,whenClose:location.reload(),activeWhenClose:false',
                ]
            );
        }

        return $html . $buttons . Html::endTag('div');
    }

    private function setButtonsView()
    {
        $requestIsAjax = $this->isModal();
        $buttons = '';
        if (method_exists($this->getController(), 'actionUpdate')) {
            $buttons .= GhostHtml::a(
                '<i class="' . (!$requestIsAjax ? 'ion ion-android-create' : 'fa fa-pencil') . '"></i> ' . Yii::t('magicview', 'Update'),
                ['update', 'id' => $this->model->id],
                [
                    'ajaxOptions' => 'send:' . ($requestIsAjax ? 'true' : 'false') . ', response:false, from:false',
                    'jsFunctions' => 'afterExecute:' . ($requestIsAjax ? ($this->getCallBack() ? $this->getCallBack() : $this->model->formName() . '.execute()') : 'location.reload()') . ',beforeLoad:false,whenClose:' . ($requestIsAjax ? $this->model->formName() . '.setActiveWhenClose()' : '') . ',activeWhenClose:true',
                    'class' =>  ($this->isModal() ? 'magic-modal' : '') . ' btn btn-link',
                    'type' => 'button'
                ]
            );
        }

        if (method_exists($this->getController(), 'actionDelete')) {
            $buttons .= GhostHtml::a(
                '<i class="' . (!$requestIsAjax ? 'ion ion-ios-trash-outline' : 'fa fa-trash') . '"></i> ' . (!$requestIsAjax ? Yii::t('magicview', 'Delete') : ''),
                ['delete', 'id' => $this->model->id],
                [
                    'class' => 'btn btn-warning btn-flat execute_delete' . ($requestIsAjax ? '' : ' btn-social'),
                    'onClick' => 'return false;',
                    'type' => 'button'
                ]
            );
        }

        if (method_exists($this->getController(), 'actionPdf')) {
            $buttons .= GhostHtml::a(
                '<i class="fa fa-file-pdf-o"></i> ' . (!$requestIsAjax ? 'PDF' : ''),
                ['pdf', 'id' => $this->model->id],
                [
                    'class' => 'btn btn-danger btn-flat' . ($requestIsAjax ? '' : ' btn-social'),
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