<?php

namespace app\models\search;

use app\models\Admin;
use yii\base\Model;
use yii\db\ActiveQuery;
use app\models\CustPayment;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

/**
 * Class PaymentSearch
 * @package app\models\search
 */
class PaymentSearch extends CustPayment
{
    public $startDate;
    public $endDate;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cust_year'], 'required', 'on' => ['admin', 'statistics']],
            [['cust_year'], 'integer', 'on' => ['search', 'admin', 'statistics']],
            [['admin_id'], 'integer', 'on' => ['search', 'admin']],
            [['create_date'], 'integer', 'on' => ['search']],
            [['cust_area'], 'string', 'max' => 20, 'on' => ['search']],
            [['cust_address'], 'string', 'max' => 200, 'on' => ['search']],
            [['payment_method'], 'in', 'range' => array_keys(self::$paymentMethodArray), 'on' => ['search', 'admin', 'statistics']],
            [['status_code'], 'in', 'range' => array_keys(self::$statusCodeArray), 'on' => ['search', 'admin', 'statistics']],
            [['startDate', 'endDate'], 'date', 'format' => 'php:Y-m-d', 'message'=>'{attribute}不符合格式。', 'on' => ['search', 'admin', 'statistics']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'search'=>[
                'cust_year', 'admin_id', 'create_date', 'cust_area', 'cust_address', 'paid_amount', 'amount', 'payment_method', 'payment_date', 'update_date', 'status_code', 'startDate', 'endDate'
            ],
            'admin' => [
                'cust_year', 'admin_id',  'paid_amount', 'amount', 'payment_method', 'payment_date', 'status_code', 'startDate', 'endDate'
            ],
            'statistics' => [
                'cust_year', 'paid_amount', 'amount', 'payment_method', 'status_code', 'startDate', 'endDate'
            ]
        ];
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search(Array $params)
    {
        $this->scenario = 'search';
        $query = self::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => ArrayHelper::getValue($params, 'per-page', 10)
            ],
        ]);

        $this->load($params);

        if(!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'cust_year' => $this->cust_year,
            'payment_method' => $this->payment_method,
            'status_code' => $this->status_code,
            'admin_id' => $this->admin_id
        ]);

        if($this->cust_area){
            $query->andFilterWhere(['like', 'cust_area', $this->cust_area]);
        }

        if($this->cust_area){
            $query->andFilterWhere(['like', 'cust_address', $this->cust_address]);
        }

        // 创建时间
        if($this->startDate){
            $query->andFilterWhere(['>=', 'payment_date', strtotime($this->startDate)]);
        }

        if($this->endDate){
            $query->andFilterWhere(['<=', 'payment_date', strtotime($this->endDate) + 86400]);
        }

        return $dataProvider;
    }

    /**
     * @param array $params
     * @return array|null
     */
    public function adminSearch(Array $params)
    {
        $this->scenario = 'admin';
        $query = self::find();

        $this->cust_year = date('Y', time());
        $this->load($params);

        if(!$this->validate()) {
            return null;
        }

        $query->andFilterWhere([
            'cust_year' => $this->cust_year,
            'status_code' => self::STATUS_NOT_CHARGE
        ]);

        // 创建时间
        if($this->startDate){
            $query->andFilterWhere(['>=', 'payment_date', strtotime($this->startDate)]);
        }

        if($this->endDate){
            $query->andFilterWhere(['<=', 'payment_date', strtotime($this->endDate) + 86400]);
        }

        $payment = $query->select(['admin_id','payment_method','count(id) as num', 'sum(paid_amount) as paid_amount','sum(amount) as amount'])
            ->groupBy(['admin_id', 'payment_method'])
            ->orderBy(['admin_id' => SORT_ASC])
            ->all();

        $data = [];
        foreach ($payment as $key => $val){
            if(isset($data[$val['admin_id']])) {
                $admin = Admin::findone($val['admin_id']);
                $data[$val['admin_id']] = [
                    'realName' => $admin->real_name,
                    'xjNum' => 0,
                    'xjAmount' => 0,
                    'wxNum' => 0,
                    'wxAmount' => 0,
                    'zfbNum' => 0,
                    'zfbAmount' => 0,
                    'totalNumber' => 0,
                    'totalPaidAmount' => 0,
                    'totalAmount' => 0
                ];
            }

            if($val['payment_method'] == self::PAYMENT_XJ){
                $data[$val['admin_id']]['xjNum'] += $val['num'];
                $data[$val['admin_id']]['xjAmount'] += $val['amount'];
            }elseif ($val['payment_method'] == self::PAYMENT_WX){
                $data[$val['admin_id']]['wxNum'] += $val['num'];
                $data[$val['admin_id']]['wxAmount'] += $val['amount'];
            }elseif($val['payment_method'] == self::PAYMENT_ZFB){
                $data[$val['admin_id']]['zfbNum'] += $val['num'];
                $data[$val['admin_id']]['zfbAmount'] += $val['amount'];
            }

            $data[$val['admin_id']]['totalNum'] += $val['num'];
            $data[$val['admin_id']]['totalPaidAmount'] += $val['paid_amount'];
            $data[$val['admin_id']]['totalAmount'] += $val['amount'];

        }

        return $data;
    }

    /**
     * @param array $params
     * @return array|null
     */
    public function statisticsSearch(Array $params)
    {
        $this->scenario = 'statistics';
        $query = self::find();
        $this->cust_year = date('Y', time());

        $this->load($params);

        if(!$this->validate()) {
            return null;
        }

        $query->andFilterWhere([
            'cust_year' => $this->cust_year
        ]);

        $payment = $query->select(['admin_id','payment_method','count(id) as num', 'sum(paid_amount) as paid_amount','sum(amount) as amount'])
            ->groupBy(['payment_method', 'status_cd'])
            ->orderBy(['admin_id' => SORT_ASC])
            ->all();

        $data = [
            'year' => 0,
            'xjNum' => 0,
            'xjAmount' => 0,
            'wxNum' => 0,
            'wxAmount' => 0,
            'zfbNum' => 0,
            'zfbAmount' => 0,
            'notNumber' => 0,
            'notAmount' => 0,
            'chargeNumber' => 0,
            'chargePaidAmount' => 0,
            'chargeAmount' => 0,
            'notChargeNumber' => 0,
            'notChargeAmount' => 0,
            'vacantHouseNumber' => 0,
            'vacantHouseAmount' => 0,
            'cutHeatingNumber' => 0,
            'cutHeatingPaidAmount' => 0,
            'cutHeatingAmount' => 0,
            'totalNum' => 0,
            'totalPaidAmount' => 0,
            'totalAmount' => 0
        ];

        foreach ($payment as $key => $val){
            // 未收取金额
            if($val['status_code'] == self::STATUS_NOT_COLLECTED){
                $data['notNumber'] += $val['num'];
                $data['notAmount'] += $val['paid_amount'];
            }
            // 已缴金额
            if($val['status_code'] == self::STATUS_CHARGE){
                if($val['payment_method'] == self::PAYMENT_XJ){
                    $data['xjNum'] += $val['num'];
                    $data['xjAmount'] += $val['amount'];
                }elseif ($val['payment_method'] == self::PAYMENT_WX){
                    $data['wxNum'] += $val['num'];
                    $data['wxAmount'] += $val['amount'];
                }elseif($val['payment_method'] == self::PAYMENT_ZFB){
                    $data['zfbNum'] += $val['num'];
                    $data['zfbAmount'] += $val['amount'];
                }

                $data['chargeNumber'] += $val['num'];
                $data['vacantHousePaidAmount'] += $val['paid_amount'];
                $data['vacantHouseAmount'] += $val['amount'];
            }

            // 未缴金额
            if($val['status_code'] == self::STATUS_NOT_CHARGE){
                $data['notChargeNumber'] += $val['num'];
                $data['notChargeAmount'] += $val['paid_amount'];
            }

            // 空置房金额
            if($val['status_code'] == self::STATUS_VACANT_HOUSE){
                $data['vacantHouseNumber'] += $val['num'];
                $data['chargePaidAmount'] += $val['paid_amount'];
                $data['chargeAmount'] += $val['amount'];
            }

            // 切断供暖金额
            if($val['status_code'] == self::STATUS_CUT_HEATING){
                $data['cutHeatingNumber'] += $val['num'];
                $data['cutHeatingAmount'] += $val['paid_amount'];
            }

            // 总金额
            $data['totalNum'] += $val['num'];
            $data['totalPaidAmount'] += $val['paid_amount'];
            $data['totalAmount'] += $val['amount'];
        }


        return $data;
    }
}
