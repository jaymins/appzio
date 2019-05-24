<?php

namespace packages\actionMfitness\Models;

use CActiveRecord;

class ProgramSelectionModel extends CActiveRecord
{

    public $id;
    public $play_id;
    public $program_id;
    public $program_type;
    public $start_time;
    public $program_start_date;
    public $training_days_per_week;
    public $training_days;
    public $times;
    public $is_completed;

    public function tableName()
    {
        return 'ae_ext_fit_program_selection';
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function relations()
    {
        return array(
            'program' => array(self::BELONGS_TO, 'packages\actionMfitness\Models\ProgramModel', 'program_id'),
        );
    }

    public static function getAllUserBusyTimes(int $play_id){
        $programs = self::getAllUserPrograms($play_id,true);
        $output = [];

        foreach ($programs as $program) {
            $times = json_decode($program->times,true);
            foreach ($times as $time){
                $name = isset($program->program->name) ? $program->program->name : '{#unknown_activity#}';
                $output[$time] = $name;
            }
        }

        return $output;
    }

    public static function getAllUserPrograms(int $play_id, $include_related_data = true)
    {
        $relation = [
            'program' => [
                'alias' => 'program',
                'with' => [
                    'category', 'subcategory'
                ]
            ],
        ];

        $criteria = new \CDbCriteria;
        $criteria->alias = 'ps';

        if ($include_related_data) {
            $criteria->with = $relation;
        }

        $criteria->order = 'ps.program_type ASC';
        $criteria->condition = 'ps.play_id = :play_id';
        $criteria->params = [
            ':play_id' => $play_id,
        ];

        $programs = self::model()->findAll($criteria);

        if (empty($programs)) {
            return [];
        }

        return $programs;
    }

    public static function getUserProgram(int $play_id, int $category_id, array $program_types = [])
    {
        $relation = [
            'program' => [
                'alias' => 'program'
            ],
        ];

        $criteria = new \CDbCriteria;
        $criteria->alias = 'ps';
        $criteria->with = $relation;
        $criteria->condition = 'ps.play_id = :play_id AND program.category_id = :category_id';
        $criteria->params = [
            ':play_id' => $play_id,
            ':category_id' => $category_id,
        ];

        if (!empty($program_types)) {
            $criteria->addInCondition('ps.program_type', $program_types);
        }

        $program = self::model()->find($criteria);

        if (empty($program)) {
            return false;
        }

        return $program->program_id;
    }

    public static function addUserProgram(ProgramModel $program, int $play_id, array $params = [])
    {
        $model = new self();
        $model->play_id = $play_id;
        $model->program_id = $program->id;
        $model->program_type = ($program->is_challenge ? 'challenge' : $program->program_type);
        $model->start_time = time();

        if (!empty($params)) {
            foreach ($params as $param => $value) {
                $model->{$param} = $value;
            }
        }

        $model->save();
        return true;
    }

    public static function deleteUserProgram(int $program_selection_id,int $playid)
    {

        @ProgramSelectionModel::model()->deleteAllByAttributes([
            'id' => $program_selection_id,'play_id' => $playid
        ]);

        return true;
    }

    public static function deleteAllUserPrograms(int $play_id)
    {
        @ProgramSelectionModel::model()->deleteAllByAttributes([
            'play_id' => $play_id
        ]);

        return true;
    }

}