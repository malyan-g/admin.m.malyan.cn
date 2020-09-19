<?php

use app\models\Admin;
use app\models\CustPayment;
use app\components\widgets\GridView;

/* @var $data */
/* @var $searchModel \app\models\search\PaymentSearch */

$this->title = Yii::t('module', 'AdminPayment') . Yii::t('common', 'List Title');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('admin-search', ['searchModel' => $searchModel]) ?>
<table class="table table-striped table-bordered table-hover dataTable">
    <thead>
        <tr>
            <th class="center">收费员姓名</th>
            <th class="center">现金缴费人数（户）</th>
            <th class="center">现金缴费金额（元）</th>
            <th class="center">微信缴费人数（户）</th>
            <th class="center">微信缴费金额（元）</th>
            <th class="center">支付宝缴费人数（户）</th>
            <th class="center">支付宝缴费金额（元）</th>
            <th class="center">缴费总人数（户）</th>
            <th class="center">应缴总金额（元）</th>
            <th class="center">实缴总金额（元）</th>
        </tr>
    </thead>
    <tbody>
    <tr>
        <?php if($data == null): ?>
            <td class="center" colspan="10">暂无数据</td>
        <?php else: ?>
            <?php foreach ($data as $key =>$val): ?>
                <td class="center"><?= $val['realName']?></td>
                <td class="center"><?= $val['xjNum']?></td>
                <td class="center"><?= $val['xjAmount']?></td>
                <td class="center"><?= $val['wxNum']?></td>
                <td class="center"><?= $val['wxAmount']?></td>
                <td class="center"><?= $val['zfbNum']?></td>
                <td class="center"><?= $val['zfbAmount']?></td>
                <td class="center"><?= $val['totalNumber']?></td>
                <td class="center"><?= $val['totalPaidAmount']?></td>
                <td class="center"><?= $val['totalAmount']?></td>
            <?php endforeach; ?>
        <?php endif; ?>
    </tr>
    </tbody>
</table>
