<?php

namespace packages\actionMcalendar\Models;

use packages\actionMfitness\Models\ProgramExerciseModel;
use packages\actionMfitness\Models\ProgramModel;
use packages\actionMfitness\Models\ProgramRecipeModel;
use packages\actionMfitness\Models\ProgramSelectionModel;

trait CalendarHandler
{

    public $number_of_weeks = 8;
    public $weeks_offset = 0;

    public function removeCalendarProgramEntries(int $program_id, int $play_id, $stamp = null)
    {
        $criteria = new \CDbCriteria;
        $criteria->condition = 'play_id = :play_id AND program_id = :program_id AND time > :stamp';
        $criteria->params = [
            ':play_id' => $play_id,
            ':program_id' => $program_id,
            ':stamp' => ($stamp ? $stamp : time()),
        ];

        CalendarModel::model()->deleteAll($criteria);

        return true;
    }

    /**
     * @param array $ids
     * @return bool
     */
    public function removeCalendarProgramEntriesByIDs(array $ids)
    {
        if (empty($ids)) {
            return false;
        }

        $ids_string = implode(',', $ids);
        $sql = "DELETE FROM ae_ext_calendar_entry WHERE id IN ({$ids_string});";

        \Yii::app()->db->createCommand($sql)->query();

        return true;
    }

    public function getEntriesToDelete(int $program_id, int $play_id, $stamp = null)
    {
        $criteria = new \CDbCriteria;
        $criteria->condition = 'play_id = :play_id AND program_id = :program_id AND time > :stamp';
        $criteria->params = [
            ':play_id' => $play_id,
            ':program_id' => $program_id,
            ':stamp' => ($stamp ? $stamp : time()),
        ];

        $entries = CalendarModel::model()->findAll($criteria);

        if (empty($entries)) {
            return [];
        }

        $data = \CHtml::listData($entries, 'id', 'time');
        return array_keys($data);
    }

    public function removeActiveUserProgram(int $program_id, int $play_id)
    {
        $criteria = new \CDbCriteria;
        $criteria->condition = 'play_id = :play_id AND program_id = :program_id';
        $criteria->params = [
            ':play_id' => $play_id,
            ':program_id' => $program_id,
        ];

        ProgramSelectionModel::model()->deleteAll($criteria);

        return true;
    }

    public function getTrainingDaysPerWeek(){
        $days = @json_decode($this->getSavedVariable('program_training_days'),true);

        if(is_array($days)){
            $num_of_days = count($days);
        } else {
            $num_of_days = 1;
        }

        return $num_of_days;

    }

    /**
     * Add an individual program in the calendar
     * The method is using different data depending on what is the program's type
     *
     * @param int $program_id
     * @return bool
     */
    public function addProgramToCalendar(int $program_id)
    {

        date_default_timezone_set('UTC');

        $program = ProgramModel::getProgramDetails($program_id);
        $type = $program->program_type;
        $result = '';


        if ($type === 'fitness') {
            $result = $this->generateCalendarEntries($program, [
                'start_date' => $this->getSavedVariable('program_start_date'),
                'training_days_per_week' => $this->getTrainingDaysPerWeek(),
                'training_days' => $this->getSavedVariable('program_training_days'),
                'times' => [
                    'training_time' => $this->getSavedVariable('program_training_time',0),
                    'training_time_1' => $this->getSavedVariable('program_training_time_1',0),
                    'training_time_2' => $this->getSavedVariable('program_training_time_2',0),
                    'training_time_3' => $this->getSavedVariable('program_training_time_3',0),
                ]
            ]);
        } else if ($type === 'food') {
            $result = $this->generateCalendarFoodEntries($program, [
                'start_date' => $this->getSavedVariable('program_start_date'),
                'times' => [
                    'meal1_time' => $this->getSavedVariable('program_meal1_time',0),
                    'meal2_time' => $this->getSavedVariable('program_meal2_time',0),
                    'meal3_time' => $this->getSavedVariable('program_meal3_time',0),
                    'meal4_time' => $this->getSavedVariable('program_meal4_time',0),
                ]
            ]);
        }

        return $result;
    }

    public function replaceProgram(array $program_data, int $program_selection_id)
    {

        date_default_timezone_set('UTC');

        $times = json_decode($program_data['times']);
        $clear_time = time();

        $start_date = $program_data['start_time'];

        if (isset($program_data['is_reverted'])) {
            $clear_time = $program_data['start_time'];
        }

        // Generate dates for X number of weeks
        // Note that the calculation is always done using +1 week
        // in order to include the current week
        if (isset($program_data['current_program_week'])) {
            $this->weeks_offset = $program_data['current_program_week'] - 1;
            $this->number_of_weeks = ($this->number_of_weeks - $program_data['current_program_week']) + 1;
        }

        if (isset($program_data['current_program_food_type']) AND $program_data['current_program_food_type']) {
            $program = $this->getRandomProgram(
                $program_data['current_program_food_type'],
                'food'
            );

            if (empty($program)) {
                return [
                    'error' => 'Program of type "' . $program_data['current_program_food_type'] . '" doesn\'t exist'
                ];
            }
        } else {
            $program = ProgramModel::getProgramDetails($program_data['program_id']);
        }

        // This would only store the obsolete/older entries in the memory.
        // The actual delete operation would only happen if no validation is triggered
        $calendar_entries_to_delete = $this->getEntriesToDelete($program_data['program_id'], $this->playid, $clear_time);

        if ($program_data['program_type'] == 'fitness' OR $program_data['program_type'] == 'challenge') {
            $result = $this->generateCalendarEntries($program, [
                'start_date' => $start_date,
                'training_days_per_week' => $program_data['training_days_per_week'],
                'training_days' => $program_data['training_days'],
                'times' => [
                    'training_time' => isset($times[0]) ? $times[0] : '',
                    'training_time_1' => isset($times[1]) ? $times[1] : '',
                    'training_time_2' => isset($times[2]) ? $times[2] : '',
                    'training_time_3' => isset($times[3]) ? $times[3] : '',
                ]
            ]);
        } else {
            $result = $this->generateCalendarFoodEntries($program, [
                'start_date' => $start_date,
                'times' => [
                    'meal1_time' => isset($times[0]) ? $times[0] : '',
                    'meal2_time' => isset($times[1]) ? $times[1] : '',
                    'meal3_time' => isset($times[2]) ? $times[2] : '',
                    'meal4_time' => isset($times[3]) ? $times[3] : '',
                ]
            ]);
        }

        if ($result === true) {
            ProgramSelectionModel::deleteUserProgram($program_selection_id,$this->playid);
            $this->removeCalendarProgramEntriesByIDs($calendar_entries_to_delete);
        }

        return $result;
    }

    public function addCalendarEntries()
    {
        date_default_timezone_set('UTC');

        $program = $this->getRandomProgram(
            $this->getSavedVariable('goal'),
            'fitness'
        );

        if (empty($program)) {
            return false;
        }

        $this->generateCalendarEntries($program, [
            'start_date' => $this->getSavedVariable('fitness_start_date'),
            'training_days_per_week' => $this->getTrainingDaysPerWeek(),
            'training_days' => $this->getSavedVariable('training_days'),
            'times' => [
                'training_time' => $this->getSavedVariable('training_time'),
            ]
        ]);

        return true;
    }

    public function addFoodCalendarEntries()
    {
        date_default_timezone_set('UTC');


        if($this->getSavedVariable('selected_food_program')){
            $id = $this->getSavedVariable('selected_food_program');
            $program = ProgramModel::model()->findByPk($id);
        } else {
            $program = $this->getRandomProgram(
                $this->getSavedVariable('food_type'),
                'food'
            );
        }

        if (empty($program)) {
            return false;
        }

        $times = [];
        $meals = $this->getSavedVariable('food_program_meals') + 1;

        for ($i = 1; $i < $meals; $i++) {
            $name = 'meal' . $i . '_time';
            $times[$name] = $this->getSavedVariable($name);
        }

        $this->generateCalendarFoodEntries($program, [
            'start_date' => $this->getSavedVariable('food_start_date'),
            'times' => $times
        ]);

        return true;
    }

    /**
     * Get a Random program based on certain criteria
     *
     * @param $subcategory_name
     * @param $type
     * @return null|ProgramModel
     */
    public function getRandomProgram(string $subcategory_name, string $type)
    {
        $relation = [
            'category' => [
                'alias' => 'category'
            ],
            'subcategory' => [
                'alias' => 'subcategory'
            ],
            'exercises',
            'recipes'
        ];

        if ($type === 'fitness') {
            $condition = "subcategory.name = :subcat_name AND program.is_challenge <> 1 AND category.name LIKE 'Training'";
        } else {
            $condition = "subcategory.name = :subcat_name AND program.program_type LIKE 'food'";
        }

        $criteria = new \CDbCriteria;
        $criteria->alias = 'program';
        $criteria->with = $relation;
        $criteria->condition = $condition;
        $criteria->params = [
            ':subcat_name' => $subcategory_name
        ];
        $criteria->limit = 1;
        $criteria->order = 'RAND()';

        $program = ProgramModel::model()->find($criteria);

        return $program;
    }

    /**
     * Add entries to the Calendar table
     *
     * @param ProgramModel $program
     * @param array $settings
     * @return bool|array
     */
    public function generateCalendarEntries(ProgramModel $program, array $settings)
    {
        date_default_timezone_set('UTC');

        // Programs with no exercises are not accepted
        if (!isset($program->exercises) OR empty($program->exercises)) {
            return [
                'error' => '{#missing_exercises#}'
            ];
        }

        $times = [];

        if (!$program->exercises_per_day OR $program->exercises_per_day == 1) {
            $times[] = $settings['times']['training_time'];
        } else {
            for ($i = 1; $i < $program->exercises_per_day + 1; $i++) {
                $times[] = $settings['times']['training_time_' . $i];
            }
        }

        $start_date = ($settings['start_date'] ? $settings['start_date'] : time());
        $training_days_per_week = $settings['training_days_per_week'];
        $training_days = $settings['training_days'];

        if(empty($trainig_days_per_week) && is_array($training_days)) {
            $training_days_per_week = count($training_days);
        }
        
        $params = [
            'FREQ' => 'WEEKLY',
            'INTERVAL' => 1,
            'BYDAY' => $this->getCalendarDays($training_days),
            'DTSTART' => gmdate('Y-m-d, g:i a', $start_date),
            'COUNT' => $training_days_per_week * $this->number_of_weeks,
        ];

        if ($times) {
            $params = array_merge($params, $this->getTimeParams($times));
        }

        $entries = new \RRule($params);

        $dates = [];
        $current_program_id = $program->id;

        foreach ($entries as $i => $occurrence) {
            if (count($times) == 1) {
                $dates[] = strtotime($occurrence->format('F j, Y, g:i a'));
            } else {
                $date_start_stamp = strtotime($occurrence->format('F j, Y, g:i a'));
                $dates[] = $date_start_stamp;

                // Beginning of the day
                $base_time = date('F j, Y', $date_start_stamp);

                foreach ($times as $j => $time) {
                    if (!$j)
                        continue;

                    $dates[] = strtotime($base_time . ' ' . $time);
                }
            }
        }

        // Make sure that no duplicates per day are allowed
        if ($error = $this->validateGeneratedEntries($current_program_id, $dates,$program)) {
            return $error;
        }

        if ($program->program_sub_type == 'non_weekly_based') {
            $data = $this->getNonWeeklyCalendarEntries($program, $dates, $current_program_id);
        } else {
            $data = $this->getWeeklyCalendarEntries($program, $dates, $current_program_id);
        }

        // It is possible that there are no exercise generated for a certain period of time
        if (empty($data)) {
            return [
                'error' => '{#missing_exercises_for_this_perior#}'
            ];
        }

        CalendarModel::insertEntries($data);

        ProgramSelectionModel::addUserProgram($program, $this->playid, [
            'program_start_date' => $settings['start_date'],
            'training_days_per_week' => $settings['training_days_per_week'],
            'training_days' => $settings['training_days'],
            'times' => (is_array($times) ? json_encode($times) : false)
        ]);

        return true;
    }

    public function generateCalendarFoodEntries(ProgramModel $program, array $settings)
    {
        date_default_timezone_set('UTC');

        // Programs with no recipes are not accepted
        if (!isset($program->recipes) OR empty($program->recipes)) {
            return [
                'error' => '{#missing_recipes#}'
            ];
        }

        $times = array_filter(
            array_values($settings['times'])
        );

        $current_program_id = $program->id;
        $type_id = $program->category_id;
        $data = [];
        $dates = [];

        $start_date = date('Y-m-d', $settings['start_date']);
        $end_date = date('Y-m-d', strtotime( '+8 weeks',$settings['start_date']));

        $dates_range = new \DatePeriod(
            new \DateTime($start_date),
            new \DateInterval('P1D'),
            new \DateTime($end_date)
        );

        foreach ($dates_range as $day) {
            $current_day = $day->format('Y-m-d');
            foreach ($times as $time) {
                $dates[] = strtotime($current_day . ' ' . $time);
            }
        }

        // Make sure that no duplicates per day are allowed
        if ($error = $this->validateGeneratedEntries($current_program_id, $dates,$program)) {
            return $error;
        }

        // Group the generated dates by weeks
        $date_groups = $this->getGroupedDates($dates);
        $week_index = $this->weeks_offset;

        foreach ($date_groups as $group) {
            $week_index++;

            $recipes_per_week = $this->getRecipesByWeek($program->recipes, $week_index);

            if (empty($recipes_per_week)) {
                continue;
            }

            $max = $program->exercises_per_day*7;

            /* this will repeat values if there are not enough */
            if(count($recipes_per_week) < $max){
                $originals = $recipes_per_week;

                while (count($recipes_per_week) < $max){
                    $recipes_per_week = array_merge($originals,$recipes_per_week);
                }
            }

            /** @var ProgramRecipeModel $recipe */
            foreach ($recipes_per_week as $i => $recipe) {
                // Check if there is a "slot" for this recipe
                if (isset($group[$i])) {
                    $data[] = [
                        'play_id' => $this->playid,
                        'type_id' => $type_id,
                        'exercise_id' => null,
                        'program_id' => $current_program_id,
                        'recipe_id' => $recipe->recipe_id,
                        'notes' => null,
                        'points' => 8,
                        'time' => $group[$i],
                        'completion' => 0,
                        'is_completed' => 0,
                    ];
                }
            }
        }

        CalendarModel::insertEntries($data);

        ProgramSelectionModel::addUserProgram($program, $this->playid, [
            'program_start_date' => $settings['start_date'],
            'times' => (is_array($times) ? json_encode($times) : false)
        ]);

        return true;
    }

    private function getNonWeeklyCalendarEntries(ProgramModel $program, array $dates, int $current_program_id)
    {
        $type_id = $program->category_id;
        $data = [];
        $index = 0;

        foreach ($dates as $date) {

            if (!isset($program->exercises[$index])) {
                $index = 0;
            }

            $exercise_id = $program->exercises[$index]->exercise_id;
            $data[] = [
                'play_id' => $this->playid,
                'type_id' => $type_id,
                'exercise_id' => $exercise_id,
                'program_id' => $current_program_id,
                'recipe_id' => null,
                'notes' => null,
                'points' => 8,
                'time' => $date,
                'completion' => 0,
                'is_completed' => 0,
            ];

            $index++;
        }

        return $data;
    }

    private function getWeeklyCalendarEntries(ProgramModel $program, array $dates, int $current_program_id)
    {
        $date_groups = $this->getGroupedDates($dates);
        $type_id = $program->category_id;
        $data = [];

        $week_index = $this->weeks_offset;
        foreach ($date_groups as $group) {
            $week_index++;

            $exercises_per_week = $this->getExercisesByWeek($program->exercises, $week_index);

            if (empty($exercises_per_week)) {
                continue;
            }

            /** @var ProgramExerciseModel $exercise */
            foreach ($exercises_per_week as $i => $exercise) {
                // Check if there is a "slot" for this exercise
                if (isset($group[$i])) {
                    $data[] = [
                        'play_id' => $this->playid,
                        'type_id' => $type_id,
                        'exercise_id' => $exercise->exercise_id,
                        'program_id' => $current_program_id,
                        'recipe_id' => null,
                        'notes' => null,
                        'points' => 8,
                        'time' => $group[$i],
                        'completion' => 0,
                        'is_completed' => 0,
                    ];
                }
            }
        }

        return $data;
    }

    private function getGroupedDates(array $dates)
    {
        $date_groups = [];
        foreach ($dates as $stamp) {
            $date_groups[date('W', $stamp)][] = $stamp;
            // $date_groups[date('W', $stamp)][] = date('l, F j, Y, g:i a', $stamp);
        }

        return $date_groups;
    }

    private function getExercisesByWeek(array $exercises, int $week_num)
    {
        $output = [];

        /** @var ProgramExerciseModel $exercise */
        foreach ($exercises as $exercise) {
            if ($exercise->week == $week_num) {
                $output[] = $exercise;
            }
        }

        return $output;
    }

    private function getRecipesByWeek(array $recipes, int $week_num)
    {
        $output = [];

        /** @var ProgramRecipeModel $recipe */
        foreach ($recipes as $recipe) {
            if ($recipe->week == $week_num) {
                $output[] = $recipe;
            }
        }

        return $output;
    }

    /**
     * Validates the calendar population
     *
     * @param int $program_id
     * @param array $dates
     * @return array|bool
     */
    private function validateGeneratedEntries(int $program_id, array $dates, ProgramModel $program)
    {
        $db_entries = CalendarModel::getCurrentUserCalendarData($this->playid, $program_id);

        if ($matches = array_intersect($dates, array_keys($db_entries))) {
            $match = $db_entries[array_values($matches)[0]];

            if (isset($match->exercise) AND isset($program->subcategory->type) AND $match->exercise AND $program->subcategory->type != 'fitness') {
                $message = '{#you_are_already_doing#} ' . $match->exercise->name . ' {#at_this_time#}. {#this_is_bad_time_management#}';
            } else if (isset($match->recipe) AND isset($program->subcategory->type) AND $match->recipe AND $program->subcategory->type != 'food') {
                $message = '{#you_would_be_having#} ' . $match->recipe->name . ' {#at_this_time#}. {#this_is_bad_time_management#}';
            }

            if(isset($message)){
                return [
                    'error' => $message
                ];
            }
        }

        return false;
    }

    private function _sort_times($a, $b)
    {
        $a = strtotime($a);
        $b = strtotime($b);
        return $a - $b;
    }

    private function getTimeParams(array $times)
    {
        $hours = [];
        $minutes = [];

        usort($times, [$this, '_sort_times']);

        // TODO: This is currently a workaround
        // Please consider using a different approach/library
        foreach ([$times[0]] as $time_entry) {
            $time = explode(':', $time_entry);

            $hours[] = $time[0];

            if (isset($time[1]) AND $time[1] == '00') {
                $minutes[] = '0';
            } elseif (isset($time[1])) {
                $minutes[] = $time[1];
            } else {
                $minutes[] = 0;
            }
        }

        return [
            'BYHOUR' => implode(',', $hours),
            'BYMINUTE' => implode(',', $minutes),
        ];
    }

    private function getCalendarDays(string $training_days)
    {
        $stored_days = @json_decode($training_days, true);

        if (empty($stored_days)) {
            return [];
        }

        $selected_days = [];

        $map = array(
            'MO' => 'Monday',
            'TU' => 'Tuesday',
            'WE' => 'Wednesday',
            'TH' => 'Thursday',
            'FR' => 'Friday',
            'SA' => 'Saturday',
            'SU' => 'Sunday',
        );

        foreach ($stored_days as $day) {
            if (in_array($day, $map)) {
                $selected_days[] = array_flip($map)[$day];
            }
        }

        return $selected_days;
    }

}