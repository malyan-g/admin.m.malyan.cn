<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%cust_payment}}".
 *
 * @property int $id ID
 * @property int $cust_year 客户年份
 * @property string $cust_area 客户小区
 * @property string $cust_address 客户地址
 * @property string $amount 应缴金额
 * @property string $paid_amount 实缴金额
 * @property int $payment_date 缴费时间
 * @property int $payment_method 缴费方式（1 - 现金，2 - 微信，3 - 支付宝）
 * @property int $status_code 状态码（0 - 未收取，1 - 已缴费，2 - 未缴费，3 - 空置房，4 - 切断供暖）
 * @property int $admin_id 收费员ID
 * @property int $create_date 创建时间
 * @property int $update_date 更新时间
 */
class CustPayment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%cust_payment}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['payment_date', 'admin_id', 'create_date', 'update_date'], 'integer'],
            [['cust_year'], 'string', 'max' => 4],
            [['cust_area', 'amount', 'paid_amount'], 'string', 'max' => 80],
            [['cust_address'], 'string', 'max' => 200],
            [['payment_method', 'status_code'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cust_year' => '客户年份',
            'cust_area' => '客户小区',
            'cust_address' => '客户地址',
            'amount' => '应缴金额',
            'paid_amount' => '实缴金额',
            'payment_date' => '缴费时间',
            'payment_method' => '缴费方式（1 - 现金，2 - 微信，3 - 支付宝）',
            'status_code' => '状态码（0 - 未收取，1 - 已缴费，2 - 未缴费，3 - 空置房，4 - 切断供暖）',
            'admin_id' => '收费员ID',
            'create_date' => '创建时间',
            'update_date' => '更新时间',
        ];
    }
}