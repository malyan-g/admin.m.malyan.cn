<?php

use app\models\Admin;
use app\models\CustPayment;
use app\components\widgets\GridView;

/* @var $data */
/* @var $searchModel \app\models\search\PaymentSearch */

$this->title = Yii::t('module', 'Statistics') . Yii::t('common', 'List Title');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('admin-search', ['searchModel' => $searchModel]) ?>
<table class="table table-striped table-bordered table-hover dataTable">
    <thead>
    <tr>
        <th class="center" colspan="9"><?= $data['year']?>年份</th>
    </tr>
    </thead>
    <thead>
        <tr>
            <th class="center">现金缴费人数（户）</th>
            <th class="center">微信缴费人数（户）</th>
            <th class="center">支付宝缴费人数（户）</th>
            <th class="center">未收取总人数（户）</th>
            <th class="center">已缴费总人数（户）</th>
            <th class="center">未缴费总人数（户）</th>
            <th class="center">空置房总人数（户）</th>
            <th class="center">切断供暖总人数（户）</th>
            <th class="center">应收总人数（户）</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <?php if($data == null): ?>
                <td class="center" colspan="9">暂无数据</td>
            <?php else: ?>
                <td class="center"><?= $data['xjNum']?></td>
                <td class="center"><?= $data['wxNum']?></td>
                <td class="center"><?= $data['zfbNum']?></td>
                <td class="center"><?= $data['notNumber']?></td>
                <td class="center"><?= $data['chargeNumber']?></td>
                <td class="center"><?= $data['notChargeNumber']?></td>
                <td class="center"><?= $data['vacantHouseNumber']?></td>
                <td class="center"><?= $data['cutHeatingNumber']?></td>
                <td class="center"><?= $data['totalNum']?></td>
            <?php endif; ?>
        </tr>
    </tbody>
    <thead>
    <tr>
        <th class="center">现金缴费金额（元）</th>
        <th class="center">微信缴费金额（元）</th>
        <th class="center">支付宝缴费金额（元）</th>
        <th class="center">未收取总金额（元）</th>
        <th class="center">已缴费总金额（元）</th>
        <th class="center">未缴总金额（元）</th>
        <th class="center">空置房总金额（元）</th>
        <th class="center">切断供暖总金额（元）</th>
        <th class="center">总金额（元）</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <?php if($data == null): ?>
            <td class="center" colspan="9">暂无数据</td>
        <?php else: ?>
            <td class="center" rowspan="2"><?= $data['xjAmount']?></td>
            <td class="center" rowspan="2"><?= $data['wxAmount']?></td>
            <td class="center" rowspan="2"><?= $data['zfbAmount']?></td>
            <td class="center" rowspan="2"><?= $data['notAmount']?></td>
            <td class="center">应收：<?= $data['chargeAmount']?></td>
            <td class="center" rowspan="2"><?= $data['notChargeAmount']?></td>
            <td class="center">应收：<?= $data['vacantHousePaidAmount']?></td>
            <td class="center" rowspan="2"><?= $data['cutHeatingAmount']?></td>
            <td class="center">应收：<?= $data['totalPaidAmount']?></td>
        <?php endif; ?>
    </tr>
    <?php if($data != null): ?>
        <tr>
            <td class="center"></td>
            <td class="center"></td>
            <td class="center"></td>
            <td class="center"></td>
            <td class="center">实收：<?= $data['chargeAmount']?></td>
            <td class="center"></td>
            <td class="center">实收：<?= $data['vacantHouseAmount']?></td>
            <td class="center"></td>
            <td class="center">实收：<?= $data['totalAmount']?></td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
