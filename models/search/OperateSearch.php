<?php

namespace app\models\search;

use yii\base\Model;
use yii\helpers\ArrayHelper;
use app\models\OperateRecord;
use yii\data\ActiveDataProvider;

/**
 * Class OperateSearch
 * @package app\models\search
 */
class OperateSearch extends OperateRecord
{
    public $startDate;
    public $endDate;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'module', 'admin_id'], 'integer'],
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
            'type' => $this->type,
            'module' => $this->module,
            'admin_id' => $this->admin_id
        ]);

        if(empty($params['OperateSearch']['startDate']) && empty($params['OperateSearch']['endDate'])){
            $this->startDate = date('Y-m-d', strtotime('-1 day'));
            $this->endDate = date('Y-m-d', time());
        }

        // 创建时间
        if($this->startDate){
            $query->andFilterWhere(['>=', self::tableName() . '.created_at', strtotime($this->startDate)]);
        }

        if($this->endDate){
            $query->andFilterWhere(['<=', self::tableName() . '.created_at', strtotime($this->endDate) + 86400]);
        }

        return $dataProvider;
    }
}
