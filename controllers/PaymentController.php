<?php

namespace app\controllers;

use Yii;
use app\models\Menu;
use app\models\OperateRecord;
use app\models\search\PaymentSearch;

/**
 * 缴费管理
 * Class PaymentController
 * @package app\controllers
 */
class PaymentController extends Controller
{
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $this->operateModule = OperateRecord::EVENT_MODULE_PAYMENT;
        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }

    /**
     * 列表
     * @return string
     */
    public function actionList()
    {
        $searchModel = new PaymentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('list',[
            'searchModel'=>$searchModel,
            'dataProvider'=>$dataProvider
        ]);
    }

    /**
     * 收费
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionCharge()
    {
        return $this->render('charge',[
            'model' => $this->findModel()
        ]);
    }

    /**
     * 未收取
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionNotCharge()
    {
        return $this->render('not-charge',[
            'model' => $this->findModel()
        ]);
    }

    /**
     * 空置房
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionVacantHouse()
    {
        return $this->render('vacant-house',[
            'model' => $this->findModel()
        ]);
    }

    /**
     * 切断供暖
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionCutHeating()
    {
        return $this->render('cut-heating',[
            'model' => $this->findModel()
        ]);
    }

    /**
     * 详情
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionView()
    {
        return $this->render('view',[
            'model' => $this->findModel()
        ]);
    }

    /**
     * 查询
     * @return  Menu the loaded model
     * @throws \yii\web\NotFoundHttpException
     */
    public function findModel()
    {
        $id = (int) Yii::$app->request->get('id', 0);
        if($id){
            if(($model = Menu::findOne($id)) !== null){
                return $model;
            }
        }
        $this->exception(Yii::t('common', 'Illegal Request'));
    }
}
