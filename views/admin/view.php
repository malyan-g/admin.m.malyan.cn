<?php

use app\models\Admin;
use app\components\widgets\DetailView;

/* @var $model \app\models\Menu */

$this->title = Yii::t('module', 'Admin') . Yii::t('common', 'View Title');
?>
<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        'username',
        'role',
        'real_name',
        'mobile',
        'email:email',
        ['attribute' => 'status_code', 'format' => ['array', $model::$statusCodeArray]],
        'create_date:datetime',
        'update_date:datetime'
    ]
]) ?>
