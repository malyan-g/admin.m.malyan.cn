<?php
/**
 * Created by PhpStorm.
 * User: M
 * Date: 17/6/20
 * Time: 下午5:02
 */

namespace app\controllers;

use app\models\AuthItem;
use app\models\AuthItemChild;
use Yii;
use yii\helpers\ArrayHelper;
use app\models\OperateRecord;
use yii\web\NotFoundHttpException;
use app\components\behaviors\AppBehavior;
use app\components\events\OperateRecordEvent;

class Controller extends \yii\web\Controller
{
    const ALERT_ERROR = 'error';
    const ALERT_DANGER = 'danger';
    const ALERT_SUCCESS = 'success';
    const ALERT_INFO = 'info';
    const ALERT_WARNING = 'warning';

    public $operateId = null;
    public $operateType;
    public $operateModule;
    public $operateDescribe = '';

    protected $operateAction = [
        'create' => OperateRecord::EVENT_TYPE_CREATE,
        'update' => OperateRecord::EVENT_TYPE_UPDATE,
        'delete' => OperateRecord::EVENT_TYPE_DELETE,
        'auth' => OperateRecord::EVENT_TYPE_AUTH,
        'disable' => OperateRecord::EVENT_TYPE_DISABLE,
        'enable' => OperateRecord::EVENT_TYPE_ENABLE,
        'sort' => OperateRecord::EVENT_TYPE_SORT,
    ];

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'app_behavior' => AppBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if(Yii::$app->user->isGuest){
            Yii::$app->end(Yii::$app->response->exitStatus, $this->redirect(['site/login']));
        }
        
        if(AuthItem::can()){
            $this->exception('对不起，您现在还没获此操作的权限！');
        }

        $this->operateType = ArrayHelper::getValue($this->operateAction, $action->id);
        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }

    /**
     * @inheritdoc
     */
    public function __destruct(){
        $this->operateRecord();
    }

    /**
     * 打印数据
     * @param $data
     */
    public function varDumpPre($data)
    {
        echo '<pre>';
        var_dump($data);
    }

    /**
     * alert提示
     * @param string $message
     * @param string $type
     */
    public function alert($message, $type = self::ALERT_DANGER)
    {
        if($message){
            Yii::$app->session->setFlash($type, $message);
        }
    }

    /**
     * 异常提示
     * @param string $message
     * @throws NotFoundHttpException
     */
    public function exception($message = '')
    {
        throw new NotFoundHttpException($message);
    }

    /**
     * 操作日志记录
     */
    protected function operateRecord()
    {
        if($this->operateId !== null){
            $OperateRecordEvent = new OperateRecordEvent();
            $OperateRecordEvent->operateId = $this->operateId;
            $OperateRecordEvent->operateType = $this->operateType;
            $OperateRecordEvent->operateModule = $this->operateModule;
            $OperateRecordEvent->operateDescribe = $this->operateDescribe;
            $this->trigger(OperateRecordEvent::EVENT_NAME, $OperateRecordEvent);
        }
    }
}
