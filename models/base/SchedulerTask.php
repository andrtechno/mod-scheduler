<?php

namespace panix\mod\scheduler\models\base;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the base-model class for table "scheduler_task".
 *
 * @property integer $id
 * @property string $name
 * @property string $schedule
 * @property string $description
 * @property integer $status_id
 * @property string $started_at
 * @property string $last_run
 * @property string $next_run
 * @property integer $active
 *
 * @property \panix\mod\scheduler\models\SchedulerLog[] $schedulerLogs
 */
class SchedulerTask extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%scheduler_task}}';
    }


    /**
     *
     */
    public static function label($n = 1)
    {
        return Yii::t('app', '{n, plural, =1{Scheduler Task} other{Scheduler Tasks}}', ['n' => $n]);
    }

    /**
     *
     */
    public function __toString()
    {
        return (string) $this->id;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'schedule', 'description', 'status_id'], 'required'],
            [['description'], 'string'],
            [['status_id', 'active'], 'integer'],
            [['started_at', 'last_run', 'next_run'], 'safe'],
            [['name', 'schedule'], 'string', 'max' => 45],
            [['name'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('scheduler/default', 'ID'),
            'name' => Yii::t('scheduler/default', 'Name'),
            'schedule' => Yii::t('scheduler/default', 'Schedule'),
            'description' => Yii::t('scheduler/default', 'Description'),
            'status_id' => Yii::t('scheduler/default', 'Status ID'),
            'started_at' => Yii::t('scheduler/default', 'Started At'),
            'last_run' => Yii::t('scheduler/default', 'Last Run'),
            'next_run' => Yii::t('scheduler/default', 'Next Run'),
            'active' => Yii::t('scheduler/default', 'Active'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSchedulerLogs()
    {
        return $this->hasMany(\panix\mod\scheduler\models\SchedulerLog::className(), ['scheduled_task_id' => 'id']);
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params = null)
    {
        $formName = $this->formName();
        $params = !$params ? Yii::$app->request->get($formName, array()) : $params;
        $query = self::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder'=>['id'=>SORT_DESC]],
        ]);

        $this->load($params, $formName);

        $query->andFilterWhere([
            'id' => $this->id,
            'status_id' => $this->status_id,
            'active' => $this->active,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'schedule', $this->schedule])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'started_at', $this->started_at])
            ->andFilterWhere(['like', 'last_run', $this->last_run])
            ->andFilterWhere(['like', 'next_run', $this->next_run]);

        return $dataProvider;
    }
}

