<?php

use yii\db\Schema;
use yii\db\Migration;
use panix\mod\scheduler\models\SchedulerLog;
use panix\mod\scheduler\models\SchedulerTask;

class m150510_090513_Scheduler extends Migration
{
    public function safeUp()
    {
        $this->createTable(SchedulerLog::tableName(), [
            'id'=> Schema::TYPE_PK.'',
            'scheduler_task_id'=> Schema::TYPE_INTEGER.'(11) NOT NULL',
            'started_at'=> Schema::TYPE_TIMESTAMP.' NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'ended_at'=> Schema::TYPE_TIMESTAMP.' NULL DEFAULT NULL',
            'output'=> Schema::TYPE_TEXT.' NOT NULL',
            'error'=> Schema::TYPE_BOOLEAN.'(1) NOT NULL DEFAULT "0"',
        ], 'ENGINE=InnoDB');

        $this->createIndex('id_UNIQUE', SchedulerLog::tableName(),'id',1);
        $this->createIndex('fk_table1_scheduler_task_idx', SchedulerLog::tableName(),'scheduler_task_id',0);

        $this->createTable(SchedulerTask::tableName(), [
            'id'=> Schema::TYPE_PK.'',
            'name'=> Schema::TYPE_STRING.'(45) NOT NULL',
            'schedule'=> Schema::TYPE_STRING.'(45) NOT NULL',
            'description'=> Schema::TYPE_TEXT.' NOT NULL',
            'status_id'=> Schema::TYPE_INTEGER.'(11) NOT NULL',
            'started_at'=> Schema::TYPE_TIMESTAMP.' NULL DEFAULT NULL',
            'last_run'=> Schema::TYPE_TIMESTAMP.' NULL DEFAULT NULL',
            'next_run'=> Schema::TYPE_TIMESTAMP.' NULL DEFAULT NULL',
            'active'=> Schema::TYPE_BOOLEAN.'(1) NOT NULL DEFAULT "0"',
        ], 'ENGINE=InnoDB');

        $this->createIndex('id_UNIQUE', SchedulerTask::tableName(),'id',1);
        $this->createIndex('name_UNIQUE', SchedulerTask::tableName(),'name',1);
        $this->addForeignKey('fk_scheduler_log_scheduler_task_id', SchedulerLog::tableName(), 'scheduler_task_id', SchedulerTask::tableName(), 'id');
    }

    public function safeDown()
    {
        $this->delete(SchedulerLog::tableName());
        $this->delete(SchedulerTask::tableName());

        $this->dropForeignKey('fk_scheduler_log_scheduler_task_id', SchedulerLog::tableName());
        $this->dropTable(SchedulerLog::tableName());
        $this->dropTable(SchedulerTask::tableName());
    }
}
