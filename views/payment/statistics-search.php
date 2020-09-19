<?php

use app\models\Admin;
use app\components\widgets\Laydate;
use app\models\CustPayment;
use app\components\helpers\Html;
use app\components\widgets\ActiveForm;

/* @var $searchModel \app\models\search\PaymentSearch */
?>
<?php $form = ActiveForm::begin(['module' => ActiveForm::TYPE_SEARCH]) ?>
<?= $form->field($searchModel, 'cust_year')->dropDownList(CustPayment::getYear(), ['prompt' => Yii::t('common', 'All')]) ?>
<?=  Laydate::widget(['form' => $form, 'model' => $searchModel, 'label' => '收费日期', 'startDate' => 'startDate', 'endDate' => 'endDate']) ?>
<div class="form-group">
    <?= Html::submitButton(Yii::t('common', 'Search'), ['class' => 'btn btn-info mr-5']) ?>
</div>
<?php ActiveForm::end() ?>
<div class="hr dotted"></div>
