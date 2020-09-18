<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%payment_record}}".
 *
 * @property int $id ID
 * @property int $cust_id 客户ID
 * @property int $admin_id 收费员ID
 * @property string $describe 描述
 * @property int $create_date 创建时间
 */
class PaymentRecord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%payment_record}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['admin_id', 'create_date'], 'integer'],
            [['cust_id'], 'string', 'max' => 4],
            [['describe'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cust_id' => '客户ID',
            'admin_id' => '收费员ID',
            'describe' => '描述',
            'create_date' => '创建时间',
        ];
    }
}
