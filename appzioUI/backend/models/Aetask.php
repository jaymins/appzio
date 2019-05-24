<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ae_task".
 *
 * @property int $id
 * @property int $play_id
 * @property int $status
 * @property string $context
 * @property string $task
 * @property string $parameters
 * @property string $result
 * @property int $tries
 * @property int $timetolive
 * @property int $launchtime
 */
class Aetask extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ae_task';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['play_id', 'status', 'tries', 'timetolive', 'launchtime'], 'integer'],
            [['context', 'task', 'parameters', 'result', 'tries', 'timetolive', 'launchtime'], 'required'],
            [['task', 'parameters', 'result'], 'string'],
            [['context'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'play_id' => 'Play ID',
            'status' => 'Status',
            'context' => 'Context',
            'task' => 'Task',
            'parameters' => 'Parameters',
            'result' => 'Result',
            'tries' => 'Tries',
            'timetolive' => 'Timetolive',
            'launchtime' => 'Launchtime',
        ];
    }
}
