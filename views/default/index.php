<?php

use ignatenkovnikita\queuemanager\models\QueueManager;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel ignatenkovnikita\queuemanager\models\search\QueueManagerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Queue Managers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="queue-manager-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


    <?php
    \yii\widgets\Pjax::begin();

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function ($model) {
            if ($model->status == QueueManager::STATUS_DONE) {
                return ['class' => 'success'];
            }
            if ($model->status == QueueManager::STATUS_RESERVED) {
                return ['class' => 'warning'];
            }
            if ($model->status == QueueManager::STATUS_WAITING) {
                return ['class' => 'info'];
            }
            if ($model->status == QueueManager::STATUS_ERROR) {
                return ['class' => 'danger'];
            }
        },
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute' => 'status',
                'value' => function ($data) {
                    return QueueManager::getStatuses($data->status);
                },
                'filter' => QueueManager::getStatuses()
            ],
            'name',
            'sender',
//            'ttr',
//            'delay',
            // 'priority',
            // 'status',
            // 'class',
            // 'properties:ntext',
            // 'data:ntext',
            // 'result_id',
            // 'result:ntext',
            'created_at:datetime',
            // 'updated_at',
            'start_execute:datetime',
            'end_execute:datetime',
            [
                'label' => Yii::t('backend', 'Time Execute'),
                'value' => function (\ignatenkovnikita\queuemanager\models\QueueManager $data) {
                    return Yii::$app->formatter->asTimestamp($data->end_execute - $data->start_execute);
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {repeat} {delete}',
                'buttons' => [
                    'repeat' => function ($url, $model) {
                        $customurl = Yii::$app->getUrlManager()->createUrl(['default/repeat', 'id' => $model['id']]); //$model->id для AR
//                        return \yii\helpers\Html::a('<span class="glyphicon glyphicon-repeat"></span>', $customurl,
//                            ['title' => Yii::t('queuemanager', 'Repeat'), 'data-pjax' => '0']);
                        return Html::a('<span class="glyphicon glyphicon-repeat"></span>', $url, [
                            'title' => Yii::t('queuemanager', 'Repeat'),
                            'data-confirm' => Yii::t('queuemanager', 'Are you sure to repeat this item?'),
                            'data-method' => 'post',
                        ]);
                    }


                ]
            ],
        ],
    ]);
    \yii\widgets\Pjax::end();
    ?>

</div>

<?php

$this->registerJs(' 
    setInterval(function(){  
         $.pjax.reload({container:"#p0"});
    }, 3000);', \yii\web\VIEW::POS_HEAD);
?>