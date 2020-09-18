<?php
/**
 * Created by PhpStorm.
 * User: M
 * Date: 17/6/26
 * Time: 下午4:44
 */

namespace app\components\behaviors;


use app\models\OperateRecord;
use yii\base\Behavior;
use app\models\OperateLog;
use app\components\events\OperateLogEvent;

class AppBehavior extends Behavior
{
    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            OperateLogEvent::EVENT_NAME => [OperateRecord::className(), 'record'],
        ];
    }
}