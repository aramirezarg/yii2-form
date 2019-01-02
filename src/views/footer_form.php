<?php
use yii\helpers\Html;
?>
<div class="btn-group">
<?=Html::submitButton(
    Yii::t('magicform',$model->isNewRecord ? 'Create' : 'Update'),
    [
        'class' => 'magic-form-submit btn btn-success ',
        'style' => 'padding-right: 10px;'
    ]
);?>
</div>
<div class="btn-group">
<?=Html::a(
     yii::t('magicform','Back'),
    ['index'],
    [
        'class' => 'magic-form-cancel btn btn-danger'
    ]
);?>
</div>
