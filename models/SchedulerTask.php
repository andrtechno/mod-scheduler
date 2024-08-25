<?php

namespace panix\mod\scheduler\models;

use Yii;
use yii\helpers\Inflector;

/**
 * This is the model class for table "scheduler_task".
 */
class SchedulerTask extends \panix\mod\scheduler\models\base\SchedulerTask
{
    const STATUS_INACTIVE = 0;
    const STATUS_PENDING = 10;
    const STATUS_DUE = 20;
    const STATUS_RUNNING = 30;
    const STATUS_OVERDUE = 40;
    const STATUS_ERROR = 50;

    /**
     * @var array
     */
    private static $_statuses = [
        self::STATUS_INACTIVE => 'STATUS_INACTIVE',
        self::STATUS_PENDING => 'STATUS_PENDING',
        self::STATUS_DUE => 'STATUS_DUE',
        self::STATUS_RUNNING => 'STATUS_RUNNING',
        self::STATUS_OVERDUE => 'STATUS_OVERDUE',
        self::STATUS_ERROR => 'STATUS_ERROR',
    ];

    /**
     * Return Taskname
     * @return string
     */
    public function __toString()
    {
        return Inflector::camel2words($this->name);
    }

    /**
     * @param $task
     * @return array|null|SchedulerTask|\yii\db\ActiveRecord
     */
    public static function createTaskModel($task)
    {
        $model = SchedulerTask::find()
            ->where(['name' => $task->getName()])
            ->one();

        if (!$model) {
            $model = new SchedulerTask();
            $model->name = $task->getName();
            $model->active = $task->active;
            $model->next_run = $task->getNextRunDate();
            $model->last_run = NULL;
            $model->status_id = self::STATUS_PENDING;
        }

        $model->description = $task->description;
        $model->schedule = $task->schedule;
        $model->save(false);

        return $model;
    }

    /**
     * @return string|null
     */
    public function getStatus()
    {
        return isset(self::$_statuses[$this->status_id]) ? self::$_statuses[$this->status_id] : null;
    }


    /**
     * Update the status of the task based on various factors.
     */
    public function updateStatus()
    {
        $status = $this->status_id;
        $isDue = in_array(
            $status,
            [
                self::STATUS_PENDING,
                self::STATUS_DUE,
                self::STATUS_OVERDUE,
            ]
        ) && strtotime($this->next_run) <= time();

        if ($isDue && $this->started_at == null) {
            $status = self::STATUS_DUE;
        } elseif ($this->started_at !== null) {
            $status = self::STATUS_RUNNING;
        } elseif ($this->status_id == self::STATUS_ERROR) {
            $status = $this->status_id;
        } elseif (!$isDue) {
            $status = self::STATUS_PENDING;
        }

        if (!$this->active) {
            $status = self::STATUS_INACTIVE;
        }

        $this->status_id = $status;
    }

    public function beforeSave($insert)
    {
        $this->updateStatus();
        return parent::beforeSave($insert);
    }
}
