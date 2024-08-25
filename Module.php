<?php
namespace panix\mod\scheduler;

use panix\engine\WebModule;
use panix\mod\scheduler\models\SchedulerLog;
use Yii;
use yii\base\BootstrapInterface;
use panix\mod\scheduler\models\SchedulerTask;
use yii\helpers\ArrayHelper;

/**
 * Class Module
 * @package panix\mod\scheduler
 */
class Module extends WebModule
{
    /**
     * Path where task files can be found in the application structure.
     * @var string
     */
    public $taskPath = '@app/cron';

    /**
     * Namespace that tasks use.
     * @var string
     */
    public $taskNameSpace = 'app\cron';


    /**
     * Scans the taskPath for any task files, if any are found it attempts to load them,
     * creates a new instance of each class and appends it to an array, which it returns.
     *
     * @return Task[]
     * @throws \yii\base\ErrorException
     */
    public function getTasks()
    {
        $dir = Yii::getAlias($this->taskPath);

        if (!is_readable($dir)) {
            throw new \yii\base\ErrorException("Task directory ($dir) does not exist");
        }

        $files = array_diff(scandir($dir), ['..', '.']);
        $tasks = [];

        foreach ($files as $fileName) {
            // strip out the file extension to derive the class name
            $className = preg_replace('/\.[^.]*$/', '', $fileName);

            // validate class name
            if (preg_match('/^[a-zA-Z0-9_]*Task$/', $className)) {
                $tasks[] = $this->loadTask($className);
            }
        }

        $this->cleanTasks($tasks);

        return $tasks;
    }

    /**
     * Removes any records of tasks that no longer exist.
     *
     * @param Task[] $tasks
     */
    public function cleanTasks($tasks)
    {
        $currentTasks = ArrayHelper::map($tasks, function ($task) {
            return $task->getName();
        }, 'description');

        foreach (SchedulerTask::find()->indexBy('name')->all() as $name => $task) { /* @var SchedulerTask $task */
            if (!array_key_exists($name, $currentTasks)) {
                SchedulerLog::deleteAll(['scheduler_task_id' => $task->id]);
                $task->delete();
            }
        }
    }

    /**
     * Given the className of a task, it will return a new instance of that task.
     * If the task doesn't exist, null will be returned.
     *
     * @param $className
     * @return null|object
     * @throws \yii\base\InvalidConfigException
     */
    public function loadTask($className)
    {
        $className = implode('\\', [$this->taskNameSpace, $className]);

        try {
            $task = Yii::createObject($className);
            $task->setModel(SchedulerTask::createTaskModel($task));
        } catch (\ReflectionException $e) {
            $task = null;
        }

        return $task;
    }
    public function getAdminMenu()
    {
        return [
            'modules' => [
                'items' => [
                    [
                        'label' => Yii::t('scheduler/default', 'MODULE_NAME'),
                        'url' => ['/admin/scheduler'],
                        'icon' => $this->icon,
                        'visible' => Yii::$app->user->can('/scheduler/admin/default/index') || Yii::$app->user->can('/scheduler/admin/default/*')
                    ],
                ],
            ],
        ];
    }


    public function getInfo()
    {
        return [
            'label' => Yii::t('scheduler/default', 'MODULE_NAME'),
            'author' => 'dev@pixelion.com.ua',
            'version' => '1.0',
            'icon' => $this->icon,
            'description' => Yii::t('scheduler/default', 'MODULE_DESC'),
            'url' => ['/admin/scheduler'],
        ];
    }

}
