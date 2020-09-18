<?php

namespace app\models\search;

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
            [['admin_id', 'create_date'], 'integer'],
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
}
