<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%login_record}}".
 *
 * @property int $id 主键ID
 * @property int $admin_id 管理员ID
 * @property int $login_ip 登录地址
 * @property int $login_date 登录时间
 */
class LoginRecord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%login_record}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['admin_id', 'login_ip', 'login_date'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键ID',
            'admin_id' => '管理员ID',
            'login_ip' => '登录地址',
            'login_date' => '登录时间',
        ];
    }
}
