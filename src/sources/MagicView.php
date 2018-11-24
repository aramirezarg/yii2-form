<?php
/**
 * User: Alfredo Ramirez
 * Date: 22/3/2018
 * Time: 17:59
 */

namespace app\components\magic\form\sources;

use app\modules\config\models\UserConfig;
use magicsoft\select\MagicSelectHelper;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class MagicView
{
    private $options;
    private $title;
    private $sub_title;
    private $breadcrumbs;
    private $model;
    private $model_id;
    private $column_description;
    private $buttons;

    private $content;
    private $view;
    private $form;

    private $view_render;
    private $view_render_options;
    private $action;
    private $set_sub_title;
    private $set_form;


    private $base_view = '@app/components/magic/view/';
    const SUB_TITLE_DEFAULT = true;

    private $sub_titles = [
        'view' => 'DETAIL VIEW', 'create' => 'CREATE', 'update' => 'UPDATE', 'index' => 'GENERAL VIEW',];

    public static function begin($options = [])
    {
        $format = new self();
        $format->runConfiguration($options);
        $format->htmlBegin();
    }

    public static function end($options = [])
    {
        $format = new self();
        $format->runConfiguration($options);

        $format->htmlEnd();
    }

    public function setTitle()
    {
        if ($this->action == 'index') {
            $this->title = ArrayHelper::getValue($this->options, 'title', $this->getTitle());
        } else {
            $this->title = ArrayHelper::getValue($this->options, 'title', $this->getTitle());
        }
    }

    private function runConfiguration($options)
    {
        $this->options = $options;
        $this->breadcrumbs = ArrayHelper::getValue($this->options, 'breadcrumbs', true);
        $this->view = \Yii::$app->view;
        $this->action = \Yii::$app->controller->action->id;
        $this->model = ArrayHelper::getValue($this->options, 'model', null);
        $this->model_id = ArrayHelper::getValue($this->options, 'model_id', null);
        $this->set_sub_title = ArrayHelper::getValue($this->options, 'sub_title', false) === false ? self::SUB_TITLE_DEFAULT : true;
        $this->sub_title = ArrayHelper::getValue($this->options, 'sub_title', false);
        $this->buttons = ArrayHelper::getValue($this->options, 'buttons', true);
        $this->model_id = ArrayHelper::getValue($this->options, 'model_id', 'id');
        $this->content = ArrayHelper::getValue($this->options, 'content', null);
        $this->set_form = ArrayHelper::getValue($this->options, 'set_form', null);
        $this->column_description = ArrayHelper::getValue($this->options, 'column_description', null);
        $this->setTitle();

        $this->setOptionsRenderView();
    }

    private function setOptionsRenderView()
    {
        if ($this->content) {
            if (isset($this->content['options'])) {
                $this->view_render_options = ArrayHelper::getValue($this->content, 'options', []);
                $this->view_render = ArrayHelper::getValue($this->content, 'view', null);
                $this->form = ArrayHelper::getValue($this->view_render_options, 'form', null);
            } else {
                $this->form = null;
            }
        }

        if ($this->form === null) {
            $this->form = ArrayHelper::getValue($this->options, 'form', null);
        }
    }

    private function getModelName()
    {
        return $this->model ? $this->model->formName() : Yii::$app->controller->id;
    }

    private function getSubTitle()
    {
        return $this->set_sub_title ? ($this->sub_title === false ? ArrayHelper::getValue($this->sub_titles, $this->action, '') : MagicFormatter::UpperCase($this->sub_title)) : '';
    }

    private function getTitle()
    {
        return $this->model ? $this->model->formName() : Yii::$app->controller->id;
    }

    private function getActionInModel()
    {
        return $this->model ? ($this->model->isNewRecord ? 'form' : $this->action) : $this->action;
    }

    private function renderContent()
    {
        return $this->view->render($this->view_render, $this->view_render_options);
    }

    private function renderHtml()
    {
        $this->htmlBegin();
        echo $this->renderContent();
        $this->htmlEnd();
    }

    private function htmlBegin()
    {
        if ($this->form) {
            echo Html::beginTag('div', ['class' => $this->model->formName() . '-form']);
            $this->form->begin(['id' => $this->getModelName() . '_form']);
        }

        echo Html::beginTag('div', ['class' => $this->getModelName() . '-' . $this->getActionInModel()]);
            echo Html::beginTag('div', ['class' => 'row', 'style' => 'margin-bottom: -20px;']);
                echo Html::beginTag('div', ['class' => 'col-md-12']);
                    echo Html::beginTag('div', ['class' => 'box box-default', 'style' => 'background-color: #d9dde2;']);
                        $this->renderHtmlTitle();
                        $this->htmlBodyBegin();
    }

    private function htmlEnd()
    {
                        $this->htmlBodyEnd();
                    echo Html::endTag('div');
                echo Html::endTag('div');
            echo Html::endTag('div');
        echo Html::endTag('div');

        if ($this->form) {
            $this->form->end();
            echo Html::endTag('div');
        }
    }

    private function renderHtmlTitle()
    {
        echo Html::beginTag('div', ['class' => 'box-header header-form modal-header', 'style' => 'background-color: whitesmoke; color: white']);
            $this->renderButtons();
        echo Html::endTag('div');
    }

    private function renderButtons()
    {
        $subtitle = $this->getSubTitle();
        $icon = $subtitle != '' ? '<i class="ion ion-ios-arrow-right" style="padding-right: 5px; padding-left: 5px;"></i>' : '';

        echo Html::beginTag('h3', ['class' => 'box-title']);
            echo Html::tag('i', '', ['class' => MagicSelectHelper::getIcon($this->model->formName()), 'style' => 'font-size: x-large; text-decoration: none; padding-right: 5px; color:orange;']);
            echo Html::tag('strong', $this->title, ['style' => 'font-size: x-large; text-decoration: none; color:black']);
            echo Html::tag('small', $icon . $this->getSubTitle(), ['style' => 'font-size: large; text-decoration:none;']);
        echo Html::endTag('h3');

        echo Html::beginTag('div', ['class' => 'box-tools pull-right for-buttons-modal', 'style' => 'top: 10px']);
        if (is_array($this->buttons)) {
            foreach ($this->buttons as $button) {
                echo $button;
            }
        } else if ($this->buttons === true or is_array($this->buttons)) {
            if ($this->action == 'create' or $this->action == 'update' or $this->set_form) {
                echo $this->view->render($this->base_view . 'views/footer_form', ['model' => $this->model, 'buttons' => $this->buttons]);
            }
        }
        echo Html::endTag('div');
    }

    private function htmlBodyBegin()
    {
        $background_color = UserConfig::getWindowsBackgroundColor();
        echo Html::beginTag('div', ['class' => '', 'style' => 'background-color:' . $background_color . '; border-radius: 0;padding: -12px; padding-right: -12px;']);
            echo Html::beginTag('div', ['class' => 'box-body pad content-in-modal']);
                echo Html::beginTag('div', ['class' => 'col-md-12', 'style' => 'padding-left: 6px; padding-right: 6px;']);
                    echo Html::beginTag('div', ['class' => 'row', 'style' => 'margin-bottom: -20px;']);
                        echo Html::beginTag('div', ['class' => 'col-md-12']);
                            echo Html::beginTag('div', []);
                                echo Html::beginTag('div', ['class' => 'box-body']);
    }

    private function htmlBodyEnd()
    {
                                echo Html::endTag('div');
                            echo Html::endTag('div');
                        echo Html::endTag('div');
                    echo Html::endTag('div');
                echo Html::endTag('div');
            echo Html::endTag('div');
        echo Html::endTag('div');
    }

    public function getOnClose()
    {
        return Yii::$app->request->isAjax ? ArrayHelper::getValue(Yii::$app->request->getQueryParams(), 'onclose', '') : null;
    }
}