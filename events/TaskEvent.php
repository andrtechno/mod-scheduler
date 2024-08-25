<?php

namespace panix\mod\scheduler\events;

use panix\mod\scheduler\Task;
use yii\base\Event;

class TaskEvent extends Event
{
    /**
     * @var Task
     */
    public $task;

    /**
     * @var \Exception
     */
    public $exception;

    /**
     * @var bool
     */
    public $success;

    /**
     * @var string
     */
    public $output;

    public $cancel = false;
}
