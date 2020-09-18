<?php

use app\models\Admin;
use app\models\OperateRecord;
use app\models\CustPayment;
use app\components\widgets\GridView;

/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $searchModel \app\models\search\OperateSearch */

$this->title = Yii::t('module', 'Payment') . Yii::t('common', 'List Title');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('search', ['searchModel' => $searchModel]) ?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\CheckboxColumn'],
        'id',
        'cust_year',
        'cust_area',
        'cust_address',
        'amount',
        'paid_amount',
        ['attribute' => 'payment_method', 'format' => ['array', CustPayment::$paymentMethodArray]],
        ['attribute' => 'status_code', 'format' => ['array', CustPayment::$statusCodeArray]],
        ['attribute' => 'admin_id', 'format' => ['array', Admin::adminArray()]],
        'payment_date:datetime',
        'create_date:datetime',
        'update_date:datetime'
    ]
]) ?>
