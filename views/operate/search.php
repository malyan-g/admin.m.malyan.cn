<?php

use app\models\Admin;
use app\models\OperateRecord;
use app\components\helpers\Html;
use app\components\widgets\ActiveForm;

/* @var $searchModel \app\models\search\OperateSearch */
?>
<?php $form = ActiveForm::begin(['module' => ActiveForm::TYPE_SEARCH]) ?>
<?= $form->field($searchModel, 'type')->dropDownList(OperateRecord::typeArray(), ['prompt' => Yii::t('common', 'All')]) ?>
<?= $form->field($searchModel, 'module')->dropDownList(OperateRecord::moduleArray(), ['prompt' => Yii::t('common', 'All')]) ?>
<?= $form->field($searchModel, 'admin_id')->dropDownList(Admin::adminArray(), ['prompt' => Yii::t('common', 'All')]) ?>
<div class="form-group">
    <?= Html::submitButton(Yii::t('common', 'Search'), ['class' => 'btn btn-info mr-5']) ?>
</div>
<?php ActiveForm::end() ?>
<div class="hr dotted"></div>
