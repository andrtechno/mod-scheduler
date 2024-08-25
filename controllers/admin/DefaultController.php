<?php

namespace panix\mod\scheduler\controllers\admin;

use panix\engine\controllers\AdminController;
use panix\mod\scheduler\models\SchedulerLog;
use panix\mod\scheduler\models\SchedulerTask;
use Yii;

/**
 * DefaultController Controller
 *
 */
class DefaultController extends AdminController
{

    /**
     *
     * @return string
     */
    public function actionIndex()
    {
        $this->pageName = 'Scheduler';

        $model  = new SchedulerTask();
        $dataProvider = $model->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }
    public function actionUpdate($id)
    {
        $model = SchedulerTask::findOne($id);
        $request = Yii::$app->getRequest();

        if (!$model) {
            throw new \yii\web\HttpException(404, 'The requested page does not exist.');
        }

        if ($model->load($request->post())) {
            $model->save();
        }

        $logModel = new SchedulerLog();
        $logModel->scheduler_task_id = $model->id;
        $logDataProvider = $logModel->search(Yii::$app->request->queryParams);

        return $this->render('update', [
            'model' => $model,
            'logModel' => $logModel,
            'logDataProvider' => $logDataProvider,
        ]);
    }


    public function actionViewLog($id)
    {
        $model = SchedulerLog::findOne($id);

        if (!$model) {
            throw new \yii\web\HttpException(404, 'The requested page does not exist.');
        }

        return $this->render('view-log', [
            'model' => $model,
        ]);
    }
}
