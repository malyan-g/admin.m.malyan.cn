<?php

use app\components\helpers\Html;
use app\components\widgets\GridView;

/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $searchModel \app\models\search\AdminSearch */

$this->title = Yii::t('module', 'Admin') . Yii::t('common', 'List Title');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('search', ['searchModel' => $searchModel]) ?>
<p>
    <?= Html::a(Yii::t('button', 'Create Admin'), ['admin/create'], ['class' => 'btn btn-success mr-5', 'target' => '_blank']) ?>
</p>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\CheckboxColumn'],
        'id',
        'username',
        'roleName.item_name',
        'real_name',
        'mobile:mobile',
        'email:email',
        ['attribute' => 'create_admin_id', 'format' => ['array', $searchModel::adminArray()]],
        ['attribute' => 'status_code', 'format' => ['array', $searchModel::$statusArray]],
        'create_date:datetime',
        ['class' => 'app\components\grid\ActionColumn', 'module' => Yii::t('module', 'Admin')]
    ]
]) ?>
