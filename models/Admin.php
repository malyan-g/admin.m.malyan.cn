<?php

namespace app\models;

use Yii;
use yii\helpers\Html;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\base\NotSupportedException;
use app\components\helpers\MatchHelper;

/**
 * This is the model class for table "{{%admin}}".
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $real_name
 * @property string $email
 * @property string $mobile
 * @property integer $status_code
 * @property integer $create_admin_id
 * @property integer $create_date
 * @property integer $update_date
 * @property string $role
 * @property string $password
 */
class Admin extends ActiveRecord implements IdentityInterface
{
    /**
     * 正常
     */
    const STATUS_ACTIVE = 1;

    /**
     * 禁用
     */
    const STATUS_DISABLE = 2;

    /**
     * 删除
     */
    const STATUS_DELETE = 3;

    public $role;
    public $password;
    
    public static $statusCodeArray = [
        self::STATUS_ACTIVE => '正常',
        self::STATUS_DISABLE => '禁用'
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%admin}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'real_name', 'mobile', 'email', 'role', 'status_code'], 'required'],
            [['password'], 'required', 'when' => function(){
                return $this->isNewRecord;
            }, 'whenClient' => '
                    function(attribute,value){
                        if(!$("#' . Html::getInputId($this, 'id') . '").val()){
                            return yii.validation.required(value, messages, {"message":"密码不能为空。"});
                        }
                    }'
            ],
            [['create_admin_id', 'mobile', 'status_code', 'create_date'], 'integer'],
            [['username'], 'string', 'max' => 20],
            [['auth_key', 'role', 'password'], 'string', 'max' => 32],
            [['password_hash'], 'string', 'max' => 255],
            [['email'], 'string', 'max' => 50],
            [['password'], 'string', 'min' => 8, 'max' => 20],
            [['real_name'], 'string', 'min'=>2, 'max' => 6],
            [['email'], 'email'],
            [['password'], 'match', 'pattern' => MatchHelper::$password, 'message' => '{attribute}只能为字母和数字。'],
            [['real_name'], 'match', 'pattern' => MatchHelper::$chinese, 'message' => '{attribute}只能为汉字。'],
            [['mobile'], 'match', 'pattern' => MatchHelper::$mobile, 'message' => '{attribute}格式不正确的。'],
            [['username', 'mobile', 'email'], 'unique'],
            [['status_code'], 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DISABLE, self::STATUS_DELETE]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '用户名',
            'auth_key' => '身份验证码',
            'password_hash' => '密码',
            'real_name' => '真实姓名',
            'create_admin_id' => '创建人',
            'email' => '邮箱',
            'mobile' => '手机号',
            'status_code' => '状态',
            'create_date' => '创建时间',
            'update_date' => '修改时间',
            'password' => '密码',
            'role' => '角色',
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        $this->role = AuthAssignment::find()->select('item_name')->where(['admin_id' => $this->id])->scalar();
        parent::afterFind(); // TODO: Change the autogenerated stub
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if($this->isNewRecord){
            $this->create_admin_id =Yii::$app->user->id;
            $this->create_date = time();
        }else{
            $this->update_date = time();
        }
        if($this->password){
            $this->setPassword($this->password);
            $this->generateAuthKey();
        }
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    /**
     * @inheritdoc
     */
    public function getRoleName()
    {
        return $this->hasOne(AuthAssignment::className(), ['admin_id' => 'id']);
    }

    /**
     * 管理员
     * @return array
     */
    public static function adminArray()
    {
        return self::find()->select('real_name')->indexBy('id')->asArray()->column();
    }

    /**
     * 登录记录
     * @throws \Exception
     * @throws \Throwable
     */
    public static function loginRecord()
    {
        $userId = Yii::$app->user->id;
        $loginRecord = LoginRecord::find()->where(['admin_id' => $userId])->orderBy(['login_date' => SORT_DESC])->limit(1)->one();
        $role = AuthAssignment::find()->select('item_name')->where(['admin_id' => $userId])->scalar();
        // 记录上次登录记录
        $session = Yii::$app->session;
        $session->set('admin_username', Yii::$app->user->identity->username);
        $session->set('admin_role', $role);
        $session->set('login_date', $loginRecord->login_date);
        $session->set('login_ip', $loginRecord->login_ip);

        // 记录本次登录
        $model = new LoginRecord();
        $model->save();
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status_code' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status_code' => [self::STATUS_ACTIVE, self::STATUS_DISABLE]]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status_code' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
}
