<?php

use app\models\Admin;
use app\models\CustPayment;
use app\components\widgets\GridView;

/* @var $data */
/* @var $searchModel \app\models\search\PaymentSearch */

$this->title = Yii::t('module', 'Statistics') . Yii::t('common', 'List Title');
$this->params['breadcrumbs'][] = $this->title;
?>
<?= $this->render('statistics-search', ['searchModel' => $searchModel]) ?>
<table class="table table-striped table-bordered table-hover dataTable">
    <thead>
    <tr>
        <th class="center" colspan="9"><?= $data['year']?>年份收费统计</th>
    </tr>
    </thead>
    <thead>
        <tr>
            <th class="center">现金缴费数</th>
            <th class="center">微信缴费数</th>
            <th class="center">支付宝缴费数</th>
            <th class="center">未收取总数</th>
            <th class="center">已缴费总数</th>
            <th class="center">未缴费总数</th>
            <th class="center">空置房总数</th>
            <th class="center">切断供暖总数</th>
            <th class="center">总数</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <?php if($data == null): ?>
                <td class="center" colspan="9">暂无数据</td>
            <?php else: ?>
                <td class="center"><?= $data['xjNum']?>（户）</td>
                <td class="center"><?= $data['wxNum']?>（户）</td>
                <td class="center"><?= $data['zfbNum']?>（户）</td>
                <td class="center"><?= $data['notNumber']?>（户）</td>
                <td class="center"><?= $data['chargeNumber']?>（户）</td>
                <td class="center"><?= $data['notChargeNumber']?>（户）</td>
                <td class="center"><?= $data['vacantHouseNumber']?>（户）</td>
                <td class="center"><?= $data['cutHeatingNumber']?>（户）</td>
                <td class="center"><?= $data['totalNum']?>（户）</td>
            <?php endif; ?>
        </tr>
    </tbody>
    <thead>
    <tr>
        <th class="center">现金缴费金额</th>
        <th class="center">微信缴费金额</th>
        <th class="center">支付宝缴费金额</th>
        <th class="center">未收取总金额</th>
        <th class="center">已缴费总金额</th>
        <th class="center">未缴总金额</th>
        <th class="center">空置房总金额</th>
        <th class="center">切断供暖总金额</th>
        <th class="center">总金额</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <?php if($data == null): ?>
            <td class="center" colspan="9">暂无数据</td>
        <?php else: ?>
            <td class="center" rowspan="2" style="vertical-align: middle !important;"><?= $data['xjAmount']?>（元）</td>
            <td class="center" rowspan="2"  style="vertical-align: middle !important;"><?= $data['wxAmount']?>（元）</td>
            <td class="center" rowspan="2"  style="vertical-align: middle !important;"><?= $data['zfbAmount']?>（元）</td>
            <td class="center" rowspan="2"  style="vertical-align: middle !important;"><?= $data['notAmount']?>（元）</td>
            <td class="center">应收：<?= $data['chargeAmount']?>（元）</td>
            <td class="center" rowspan="2"  style="vertical-align: middle !important;"><?= $data['notChargeAmount']?>（元）</td>
            <td class="center">应收：<?= $data['vacantHousePaidAmount']?>（元）</td>
            <td class="center" rowspan="2"  style="vertical-align: middle !important;"><?= $data['cutHeatingAmount']?>（元）</td>
            <td class="center">应收：<?= $data['totalPaidAmount']?>（元）</td>
        <?php endif; ?>
    </tr>
    <?php if($data != null): ?>
        <tr>
            <td class="center">实收：<?= $data['chargeAmount']?>（元）</td>
            <td class="center">实收：<?= $data['vacantHouseAmount']?>（元）</td>
            <td class="center">实收：<?= $data['totalAmount']?>（元）</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
