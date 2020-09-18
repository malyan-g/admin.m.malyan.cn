<?php

namespace app\controllers;

use Yii;
use app\models\Menu;
use yii\db\ActiveQuery;
use app\models\AuthItem;
use app\models\OperateLog;
use app\models\AuthItemChild;
use app\models\search\PermissionSearch;

/**
 * 权限管理
 * Class PermissionController
 * @package app\controllers
 */
class PermissionController extends Controller
{
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $this->operateModule = OperateLog::EVENT_MODULE_PERMISSION;
        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }

    /**
     * 列表
     * @return string
     */
    public function actionList()
    {
        $searchModel = new PermissionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('list',[
            'searchModel'=>$searchModel,
            'dataProvider'=>$dataProvider
        ]);
    }

    /**
     * 创建
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        return $this->save();
    }

    /**
     * 更新
     * @return string|\yii\web\Response
     */
    public function actionUpdate()
    {
        return $this->save(false);
    }

    /**
     * 详情
     * @return string
     */
    public function actionView()
    {
        return $this->render('view',[
            'model' => $this->findModel()
        ]);
    }

    /**
     * 删除
     * @return \yii\web\Response
     * @throws \Exception
     * @throws \Throwable
     */
    public function actionDelete()
    {
        $model = $this->findModel();
        if($model->delete()){
            $this->operateId = 0;
            $this->operateDescribe = '( '. $model->name .' )';
            $this->alert(Yii::t('common','Delete Successfully'), self::ALERT_SUCCESS);
        }else{
            $this->alert(Yii::t('common','Delete Failure'));
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * 获取二级菜单
     * @return string
     */
    public function actionMenu()
    {
        $id = (int) Yii::$app->request->get('id', 0);
        $options = '<option value="">请选择</option>';
        if($id){
            $menu = Menu::childArray($id);
            foreach ($menu as $key => $val){
                $options .= "<option value=\"{$key}\">{$val}</option>";
            }
        }
        return $options;
    }

    /**
     * 保存
     * @param bool $isCreate
     * @return string|\yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    private function save($isNewRecord = true)
    {
        $model = $isNewRecord ? new AuthItem() : $this->findModel();
        $model->type = AuthItem::TYPE_PERMISSION;

        $request = Yii::$app->request;
        if($request->isPost){
            if($model->load($request->post()) && $model->validate()){

                $trans = Yii::$app->db->beginTransaction();
                try{
                    $model->save(false);
                    // 关联菜单关系
                    if($isNewRecord){
                       $authItemChild = new AuthItemChild();
                        $authItemChild->parent = Menu::find()->select('route')->where(['id' => $model->levelTwo])->scalar();
                        $authItemChild->child = $model->name;
                        $authItemChild->save(false);
                    }
                    $trans->commit();
                    $this->alert(Yii::t('common', $isNewRecord ? 'Create Successfully' : 'Update Successfully'), self::ALERT_SUCCESS);
                    $this->operateId = 0;
                    $this->operateDescribe = '( '. $model->name .' )';
                    if($isNewRecord){
                        return $this->redirect('create');
                    }
                }catch (\Exception $e){
                    $trans->rollBack();
                    $this->alert(Yii::t('common', $isNewRecord ? 'Create Failure' : 'Update Failure'));
                }
            }else{
                $this->exception(Yii::t('common', 'Illegal Operation'));
            }
        }
        return $this->render('form',[
            'model' => $model
        ]);
    }

    /**
     * 查询
     * @return  AuthItem the loaded model
     * @throws \yii\web\NotFoundHttpException
     */
    public function findModel()
    {
        $name = Yii::$app->request->get('id');
        if($name){
            if(($model = AuthItem::findOne(['name' => $name, 'type' => AuthItem::TYPE_PERMISSION])) !== null){
                // 菜单没有权限删除和编辑
                $menu = Menu::findOne(['route' => $model->name]);
                if($menu === null){
                    // 关联关系
                    $authItemChild =  AuthItemChild::find()->innerJoinWith(['parentItem' => function(ActiveQuery $query){
                            $query->andWhere(['type' => AuthItem::TYPE_PERMISSION]);
                    }])->where(['child' => $model->name])->one();
                    if($authItemChild){
                        // 权限的父级
                        $menu = Menu::findOne(['route' => $authItemChild->parent]);
                        if($menu){
                            $model->levelOne = $menu->pid;
                            $model->levelTwo = $menu->id;
                            return $model;
                        }
                    }
                }
            }
        }
        $this->exception(Yii::t('common', 'Illegal Request'));
    }
}