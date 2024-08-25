# yii2-scheduler

[![Latest Stable Version](https://poser.pugx.org/panix/mod-scheduler/v/stable)](https://packagist.org/packages/panix/mod-scheduler)
[![Total Downloads](https://poser.pugx.org/panix/mod-scheduler/downloads)](https://packagist.org/packages/panix/mod-scheduler)
[![Monthly Downloads](https://poser.pugx.org/panix/mod-scheduler/d/monthly)](https://packagist.org/packages/panix/mod-scheduler)
[![Daily Downloads](https://poser.pugx.org/panix/mod-scheduler/d/daily)](https://packagist.org/packages/panix/mod-scheduler)
[![Latest Unstable Version](https://poser.pugx.org/panix/mod-scheduler/v/unstable)](https://packagist.org/packages/panix/mod-scheduler)
[![License](https://poser.pugx.org/panix/mod-scheduler/license)](https://packagist.org/packages/panix/mod-scheduler)


A scheduled task manager for yii2

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Install using the following command.

~~~bash
$ composer require panix/mod-scheduler
~~~

Now that the  package has been installed you need to configure the module in your application

The `config/console.php` file should be updated to reflect the changes below
~~~php
    'bootstrap' => ['log', 'scheduler'],
    'modules' => [
        'scheduler' => ['class' => 'panix\mod\scheduler\Module'],
    ],
    'components' => [
        'errorHandler' => [
            'class' => 'panix\mod\scheduler\ErrorHandler'
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\EmailTarget',
                    'mailer' =>'mailer',
                    'levels' => ['error', 'warning'],
                    'message' => [
                        'to' => ['admin@example.com'],
                        'from' => [$params['adminEmail']],
                        'subject' => 'Scheduler Error - ####SERVERNAME####'
                    ],
                    'except' => [
                    ],
                ],
            ],
        ],
    ]
~~~

also add this to the top of your `config/console.php` file
~~~php
\yii\base\Event::on(
    \panix\mod\scheduler\console\SchedulerController::className(),
    \panix\mod\scheduler\events\SchedulerEvent::EVENT_AFTER_RUN,
    function ($event) {
        if (!$event->success) {
            foreach($event->exceptions as $exception) {
                throw $exception;
            }
        }
    }
);
~~~

To implement the GUI for scheduler also add the following to your `config/web.php`
~~~php
    'bootstrap' => ['log', 'scheduler'],
    'modules' => [
        'scheduler' => ['class' => 'panix\mod\scheduler\Module'],
    ],
~~~

After the configuration files have been updated, a `tasks` directory will need to be created in the root of your project.


Run the database migrations, which will create the necessary tables for `scheduler`
~~~bash
php yii migrate up --migrationPath=vendor/webtoolsnz/yii2-scheduler/src/migrations
~~~

Add a controller
~~~php
<?php

namespace app\modules\admin\controllers;

use yii\web\Controller;

/**
 * Class SchedulerController
 * @package app\modules\admin\controllers
 */
class SchedulerController extends Controller
{
    public function actions()
    {
        return [
            'index' => [
                'class' => 'panix\mod\scheduler\actions\IndexAction',
                'view' => '@scheduler/views/index',
            ],
            'update' => [
                'class' => 'panix\mod\scheduler\actions\UpdateAction',
                'view' => '@scheduler/views/update',
            ],
            'view-log' => [
                'class' => 'panix\mod\scheduler\actions\ViewLogAction',
                'view' => '@scheduler/views/view-log',
            ],
        ];
    }
}
~~~

## Example Task

You can now create your first task using scheduler, create the file `AlphabetTask.php` inside the `tasks` directory in your project root.

Paste the below code into your task:
~~~php
<?php
namespace app\tasks;

/**
 * Class AlphabetTask
 * @package app\tasks
 */
class AlphabetTask extends \panix\mod\scheduler\Task
{
    public $description = 'Prints the alphabet';
    public $schedule = '0 * * * *';
    public function run()
    {
        foreach (range('A', 'Z') as $letter) {
            echo $letter;
        }
    }
}
~~~

The above code defines a simple task that runs at the start of every hour, and prints the alphabet.

The `$schedule` property of this class defines how often the task will run, these are simply [Cron Expression](http://en.wikipedia.org/wiki/Cron#Examples)


### Running the tasks

Scheduler provides an intuitive CLI for executing tasks, below are some examples

```bash
 # list all tasks and their status
 $ php yii scheduler

 # run the task if due
 $ php yii scheduler/run --taskName=AlphabetTask

 # force the task to run regardless of schedule
 $ php yii scheduler/run --taskName=AlphabetTask --force

 # run all tasks
 $ php yii scheduler/run-all

 # force all tasks to run
 $ php yii scheduler/run-all --force
```

In order to have your tasks run automatically simply setup a crontab like so

```bash
*/5 * * * * admin php /path/to/my/app/yii scheduler/run-all > /dev/null &
```

### Events & Errors

Events are thrown before and running individual tasks as well as at a global level for multiple tasks

Task Level

```php
Event::on(AlphabetTask::className(), AlphabetTask::EVENT_BEFORE_RUN, function ($event) {
    Yii::trace($event->task->className . ' is about to run');
});
Event::on(AlphabetTask::className(), AlphabetTask::EVENT_AFTER_RUN, function ($event) {
    Yii::trace($event->task->className . ' just ran '.($event->success ? 'successfully' : 'and failed'));
});
```

or at the global level, to throw errors in `/yii`

```php
$application->on(\panix\mod\scheduler\events\SchedulerEvent::EVENT_AFTER_RUN, function ($event) {
    if (!$event->success) {
        foreach($event->exceptions as $exception) {
            throw $exception;
        }
    }
});
```

You could throw the exceptions at the task level, however this will prevent further tasks from running.

## License

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.
