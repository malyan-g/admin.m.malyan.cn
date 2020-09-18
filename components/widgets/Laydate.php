<?php
/**
 * Created by PhpStorm.
 * User: M
 * Date: 17/4/7
 * Time: 下午3:27
 */

namespace app\components\widgets;

use yii\base\Widget;
use yii\helpers\Html;

/**
 * Class Laydate
 * @package app\widgets
 * <?=  Laydate::widget(['form' => $form, 'model' => $searchModel, 'label' => '结束时间', 'startDate' => 'payStartDate', 'endDate' => 'payEndDate']) ?>
 */
class Laydate extends Widget
{
    /**
     * @var $form \yii\widgets\ActiveForm 
     */
    public $form;

    /**
     * @var $model
     */
    public $model;

    /**
     * @var $label
     */
    public $label = '时间';

    /**
     * @var $startDate
     */
    public $startDate = 'startDate';

    /**
     * @var $endDate
     */
    public $endDate = 'endDate';

    /**
     * @var $searchType
     */
    public $searchType = true;

    /**
     * @var string
     */
    public $date = 'date';

    /**
     * @var string
     */
    public $format = 'YYYY-MM-DD';

    /**
     * @var string
     */
    public $formFormat = 'Y-m-d H:i:s';

    /**
     * @inheritdoc
     */
    public function run()
    {
        if($this->searchType){
            $this->generateJs($this->startDate, $this->endDate);
            return $this->generateHtml($this->startDate, $this->endDate);
        }else{
            $this->generateDateJs();
            return $this->generateDateHtml();
        }
    }

    /**
     * 注册js
     * @param $startDate
     * @param $endDate
     */
    private function generateJs($startDate, $endDate)
    {
        $startDataId = Html::getInputId($this->model, $startDate);
        $endDataId = Html::getInputId($this->model, $endDate);
        $date = date('Y-m-d');
        $startMax = $this->model->$endDate ? $this->model->$endDate : $date;
        $endMin = $this->model->$startDate ? $this->model->$startDate : '2000-01-01';
        $js = <<<EOD
            var $startDate = {
                elem : '#{$startDataId}',
                min: '2000-01-01',
                max: '{$startMax}',
                format : '$this->format',
                istoday: false,
                issure: false,
                choose: function(datas){
                    $endDate.min = datas; //开始日选好后，重置结束日的最小日期
                    $endDate.start = datas; //将结束日的初始值设定为开始日
                }
            };
            
            var $endDate = {
                elem : '#{$endDataId}',
                min: '{$endMin}',
                max: '$date',
                format : '$this->format',
                istoday: false,
                issure: false,
                choose: function(datas){
                    $startDate.max = datas; //结束日选好后，重置开始日的最大日期
                }
            };
            
            laydate($startDate);
            laydate($endDate);
EOD;
        $this->view->registerJsFile('@web/js/laydate/laydate.js');
        $this->view->registerJs($js);
    }

    /**
     * 生成html
     * @param $startDate
     * @param $endDate
     * @return string
     */
    private function generateHtml($startDate, $endDate)
    {
        return $this->form->field($this->model, $startDate, [
            'template' => '{label}{input}<span class="glyphicon glyphicon-calendar form-control-feedback" aria-hidden="true"></span>',
            'options' => [
                'class' => 'form-group has-feedback'
            ],
            'inputOptions' => [
                'class' => 'form-control',
                'autocomplete' => 'off',
                'placeholder' => $this->model->attributeLabels()[$startDate],
                'value' => $this->model->$startDate
            ]
        ])->label($this->label) .
        '<div class="form-group mr-10" style="color: grey">&nbsp;-&nbsp;</div>' .
        $this->form->field($this->model, $endDate, [
            'template' => '{label}{input}<span class="glyphicon glyphicon-calendar form-control-feedback" aria-hidden="true"></span>',
            'options' => [
                'class' => 'form-group has-feedback rt-mrt',
            ],
            'inputOptions' => [
                'class' => 'form-control',
                'autocomplete' => 'off',
                'placeholder' => $this->model->attributeLabels()[$endDate],
                'value' => $this->model->$endDate
            ]
        ])->label('');
    }

    /**
     * @inheritdoc
     */
    private function generateDateJs()
    {
        $dataId = Html::getInputId($this->model, $this->date);
        $showTime = $this->format == 'YYYY' ? 'true' : 'false';
        $js = <<<EOD
            var $this->date = {
                elem : '#{$dataId}',
                type : 'datetime',
                min: '2000',
                max: '2039',
                format: '$this->format',
                istoday: false,
                issure: $showTime,
                istime: $showTime
            };
            
            laydate($this->date);
EOD;
        $this->view->registerJsFile('@web/js/laydate/laydate.js');
        $this->view->registerJs($js);
    }

    /**
     * @inheritdoc
     */
    private function generateDateHtml()
    {
        $date = $this->date;
        return $this->form->field($this->model, $date, [
            'template' => '{label}{input}<span class="glyphicon glyphicon-calendar form-control-feedback" aria-hidden="true"></span>',
            'options' => [
                'class' => 'form-group has-feedback'
            ],
            'inputOptions' => [
                'class' => 'form-control',
                'placeholder' => '选择时间',
                'value' => $this->model->$date ? date($this->formFormat, $this->model->$date) : ''
            ]
        ])->label(false);
    }
}
