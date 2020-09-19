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
            [['cust_year', 'admin_id', 'create_date'], 'integer'],
            [['cust_area'], 'string', 'max' => 20],
            [['cust_address'], 'string', 'max' => 200],
            [['payment_method'], 'in', 'range' => array_keys(self::$paymentMethodArray)],
            [['status_code'], 'in', 'range' => array_keys(self::$statusCodeArray)],
            [['startDate', 'endDate'], 'date', 'format' => 'php:Y-m-d', 'message'=>'{attribute}不符合格式。']
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search(Array $params)
    {
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
     * @return array|ActiveDataProvider
     */
    public function adminSearch(Array $params)
    {
        $query = self::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        $this->load($params);

        if(!$this->validate()) {
            return $dataProvider;
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
}
