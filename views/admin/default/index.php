<?php
/**
 * Index View for scheduled tasks
 *
 * @var \yii\web\View $this
 * @var \yii\data\ActiveDataProvider $dataProvider
 * @var \panix\mod\scheduler\models\SchedulerTask $model
 */

use yii\helpers\Html;
use yii\helpers\Url;
use panix\engine\grid\GridView;
use yii\widgets\Pjax;
use panix\mod\scheduler\models\SchedulerTask;

$this->title = SchedulerTask::label(2);
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="scheduler-index">

    <h1><?= $this->title ?></h1>

    <div class="table-responsive">
        <?php Pjax::begin(); ?>
        <?= GridView::widget([
            'layout' => '{summary}{pager}{items}{pager}',
            'layoutOptions' => ['title' => $this->context->pageName],
            'dataProvider' => $dataProvider,
            'pager' => [
                'class' => yii\widgets\LinkPager::className(),
                'firstPageLabel' => Yii::t('app', 'First'),
                'lastPageLabel' => Yii::t('app', 'Last'),
            ],
            'columns' => [
                [
                    'attribute' => 'name',
                    'format' => 'raw',
                    'value' => function (SchedulerTask $model) {
                        return Html::a($model->name, ['update', 'id' => $model->id]);
                    }
                ],
                'description',
                [
                    'attribute' => 'schedule',
                    'format' => 'raw',
                    'contentOptions'=>['class'=>'text-center'],
                    'value' => function (SchedulerTask $model) {
                        return $model->schedule . '<br>' . \panix\mod\scheduler\components\translate\CronTranslator::translate($model->schedule, Yii::$app->language, true);
                    }
                ],
                [
                    'attribute' => 'status',
                    'format' => 'raw',
                    'contentOptions'=>['class'=>'text-center'],
                    'value' => function (SchedulerTask $model) {
                        return Yii::t('scheduler/default', $model->status);
                    }
                ],
                [
                    'attribute' => 'started_at',
                    'format' => 'raw',
                    'contentOptions'=>['class'=>'text-center'],
                    'value' => function (SchedulerTask $model) {
                        return $model->started_at;
                    }
                ],
                [
                    'attribute' => 'last_run',
                    'format' => 'raw',
                    'contentOptions'=>['class'=>'text-center'],
                    'value' => function (SchedulerTask $model) {
                        return $model->last_run;
                    }
                ],
                [
                    'attribute' => 'next_run',
                    'format' => 'raw',
                    'contentOptions'=>['class'=>'text-center'],
                    'value' => function (SchedulerTask $model) {
                        return $model->next_run;
                    }
                ],

            ],
        ]); ?>
        <?php Pjax::end(); ?>
    </div>
</div>
