<?php
use webvimark\modules\UserManagement\UserManagementModule;
use yii\helpers\Html;
?>

<div class = 'footer-form control_modal'>
    <?=Html::submitButton(
        '<i class="ion ion-android-checkmark-circle"></i> ' . UserManagementModule::t('back',$model->isNewRecord ? 'Create' : 'Update'),
        [
            'id' => '_execute_form',
            'onClick' => 'return false;',
            'class' => 'btn btn-social btn-success'
        ]
    );?>
    <?=Html::a(
        '<i class="fa fa-sign-out"></i> ' . UserManagementModule::t('back','Back'),
        ['index'],
        [
            'class' => 'btn btn-social btn-danger operation-cancel _close_no_question'
        ]
    );?>
</div>