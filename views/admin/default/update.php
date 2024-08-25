<?php
/**
 * Update Task View
 *
 * @var yii\web\View $this
 * @var panix\mod\scheduler\models\SchedulerTask $model
 */

use yii\helpers\Html;
use panix\mod\scheduler\models\SchedulerTask;
use yii\bootstrap4\Tabs;
use yii\bootstrap4\ActiveForm;
use panix\engine\grid\GridView;
use panix\mod\scheduler\models\SchedulerLog;

$this->title = $model->__toString();
$this->params['breadcrumbs'][] = ['label' => SchedulerTask::label(2), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->__toString();
?>
<div class="task-update">
    <div class="row">
        <div class="col-lg-6">

            <div class="card">
                <div class="card-header">
                    <h5><?= $this->title ?></h5>
                </div>
                <div class="card-body">




                    <?php $form = ActiveForm::begin([
                        'id' => $model->formName(),
                        'layout' => 'horizontal',
                        'enableClientValidation' => false,
                    ]); ?>

                    <?= $form->field($model, 'name', ['inputOptions' => ['disabled' => 'disabled']]) ?>
                    <?= $form->field($model, 'description', ['inputOptions' => ['disabled' => 'disabled']]) ?>
                    <?= $form->field($model, 'schedule', ['inputOptions' => ['disabled' => 'disabled']]) ?>
                    <?= $form->field($model, 'status', ['inputOptions' => ['disabled' => 'disabled']]) ?>

                    <?php if ($model->status_id == SchedulerTask::STATUS_RUNNING): ?>
                        <?= $form->field($model, 'started_at', ['inputOptions' => ['disabled' => 'disabled']]) ?>
                    <?php endif ?>

                    <?= $form->field($model, 'last_run', ['inputOptions' => ['disabled' => 'disabled']]) ?>
                    <?= $form->field($model, 'next_run', ['inputOptions' => ['disabled' => 'disabled']]) ?>

                    <?= $form->field($model, 'active')->dropdownList([1 => 'Yes', 0 => 'No']);
                    ?>

                    <?= Html::submitButton('<span class="glyphicon glyphicon-check"></span> ' . ($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save')), [
                        'id' => 'save-' . $model->formName(),
                        'class' => 'btn btn-primary'
                    ]); ?>

                    <?php ActiveForm::end(); ?>


                </div>
            </div>
        </div>
        <div class="col-lg-6">


            <div class="table-responsive">
                <?php \yii\widgets\Pjax::begin(['id' => 'logs']); ?>
                <?= GridView::widget([
                    'layout' => '{summary}{pager}{items}{pager}',
                    'layoutOptions' => ['title' => 'Logs'],
                    'dataProvider' => $logDataProvider,
                    'pager' => [
                        'class' => yii\widgets\LinkPager::className(),
                        'firstPageLabel' => Yii::t('app', 'First'),
                        'lastPageLabel' => Yii::t('app', 'Last'),
                    ],
                    'columns' => [
                        [
                            'attribute' => 'started_at',
                            'format' => 'raw',
                            'value' => function (SchedulerLog $model) {
                                return Html::a(Yii::$app->getFormatter()->asDatetime($model->started_at), ['view-log', 'id' => $model->id], ['data-pjax' => 0]);
                            }
                        ],
                        'ended_at:datetime',
                        [
                            'label' => 'Duration',
                            'value' => function (SchedulerLog $model) {
                                return $model->getDuration();
                            }
                        ],
                        [
                            'label' => 'Result',
                            'format' => 'raw',
                            'contentOptions' => ['class' => 'text-center'],
                            'value' => function (SchedulerLog $model) {

                                return Html::tag('span', '', [
                                    'class' => $model->error == 0 ? 'text-success icon-check' : 'text-danger icon-warning'
                                ]);
                            }
                        ],
                    ],
                ]); ?>
                <?php \yii\widgets\Pjax::end(); ?>
            </div>

        </div>
    </div>

</div>
