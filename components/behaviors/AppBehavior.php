<?php
/**
 * Created by PhpStorm.
 * User: M
 * Date: 17/6/26
 * Time: 下午4:44
 */

namespace app\components\behaviors;

use yii\base\Behavior;
use app\models\OperateRecord;
use app\components\events\OperateRecordEvent;

class AppBehavior extends Behavior
{
    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            OperateRecordEvent::EVENT_NAME => [OperateRecord::className(), 'record'],
        ];
    }
}