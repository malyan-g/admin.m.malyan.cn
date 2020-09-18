<?php

use app\models\Admin;
use app\components\widgets\Laydate;
use app\models\CustPayment;
use app\components\helpers\Html;
use app\components\widgets\ActiveForm;

/* @var $searchModel \app\models\search\PaymentSearch */
?>
<?php $form = ActiveForm::begin(['module' => ActiveForm::TYPE_SEARCH]) ?>
<?= $form->field($searchModel, 'cust_year') ?>
<?=  Laydate::widget(['form' => $form, 'searchType' => false, 'model' => $searchModel, 'label' => '缴费年份', 'date' => 'cust_year']) ?>
<?=  Laydate::widget(['form' => $form, 'model' => $searchModel, 'label' => '缴费日期', 'startDate' => 'startDate', 'endDate' => 'endDate']) ?>
<br>
<?= $form->field($searchModel, 'cust_area') ?>
<?= $form->field($searchModel, 'cust_address') ?>
<br>
<?= $form->field($searchModel, 'payment_method')->dropDownList(CustPayment::$paymentMethodArray, ['prompt' => Yii::t('common', 'All')]) ?>
<?= $form->field($searchModel, 'status_code')->dropDownList(CustPayment::$statusCodeArray, ['prompt' => Yii::t('common', 'All')]) ?>
<?= $form->field($searchModel, 'admin_id')->dropDownList(Admin::adminArray(), ['prompt' => Yii::t('common', 'All')]) ?>
<div class="form-group">
    <?= Html::submitButton(Yii::t('common', 'Search'), ['class' => 'btn btn-info mr-5']) ?>
</div>
<?php ActiveForm::end() ?>
<div class="hr dotted"></div>
