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
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = \panix\mod\scheduler\models\SchedulerTask::label(2);
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="scheduler-index">

    <h1><?= $this->title ?></h1>

    <div class="table-responsive">
        <?php Pjax::begin(); ?>
        <?= GridView::widget([
            'layout' => '{summary}{pager}{items}{pager}',
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
                    'value' => function ($t) {
                        return Html::a($t->name, ['update', 'id' => $t->id]);
                    }
                ],

                'description',

                [
                    'attribute' => 'schedule',
                    'format' => 'raw',
                    'value' => function ($t) {
                        // return $t->schedule;
                        return $t->schedule.'<br>'.\panix\mod\scheduler\components\translate\CronTranslator::translate($t->schedule, Yii::$app->language, true);
                    }
                ],
                'status',
                'started_at',
                'last_run',
                'next_run',
            ],
        ]); ?>
        <?php Pjax::end(); ?>
    </div>
</div>
