<?php

/**
 * Default model for the action. It has access to all data provided by the BootstrapModel.
 *
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMfitness\Models;

use Bootstrap\Models\BootstrapModel;
use packages\actionMcalendar\Models\CalendarHandler;
use CDbCriteria;
use packages\actionMcalendar\Models\CalendarModel;
use Yii;

class Model extends BootstrapModel
{

    use Pr;
    use Leaderboard;
    use Points;
    use Chart;
    use Units;
    use CalendarHandler;

    public $category_id;
    public $points;

    public $seconds_per_week = 604800;
    public $search_term;

    public function storeProgramSettings(array $programs)
    {
        foreach ($programs as $program_id => $program_data) {
            $status = $this->replaceProgram($program_data, $program_id);

            if (isset($status['error'])) {
                $this->validation_errors[] = $status['error'];
            }
        }
        // There was an error
        if ($this->validation_errors) {
            return false;
        }

        // Clear the session's data
        $this->clearTmpStorage();

        return true;
    }

    /**
     * Gets the updated programs from the session
     * Also - makes sure that the values actually differ from the originally stored data
     *
     * @return array
     */
    public function getProgramUpdates()
    {
        $data = [];

        foreach ($this->session_storage as $var => $value) {
            if (!stristr($var, 'sw_program_')) {
                continue;
            }

            $pieces = explode('|', $var);
            $user_program_id = str_replace('sw_program_', '', $pieces[0]);
            $data[$user_program_id][$pieces[1]] = $value;
        }

        if (empty($data)) {
            return [];
        }

        $current_programs_data = ProgramSelectionModel::getAllUserPrograms($this->playid);

        // Fail safe
        if (empty($current_programs_data)) {
            return [];
        }

        $changed_programs = [];

        /** @var ProgramSelectionModel $program */
        foreach ($current_programs_data as $program) {
            if (!isset($data[$program->id]) OR empty($data[$program->id])) {
                continue;
            }

            $entry = $data[$program->id];

            $program_status = $this->programIsChanged($program, $entry);

            if ($program_status['is_changed']) {

                $times = [];
                $entry_data = [];

                foreach ($entry as $field => $value) {
                    if (stristr($field, 'times_')) {
                        $times[$this->getNumber($field) - 1] = $value;
                    } else if ($field == 'current_program_week') {
                        $entry_data[$field] = $this->getNumber($value);
                    } else {
                        $entry_data[$field] = $value;
                    }
                }

                if ($program_status['start_time']) {
                    $entry_data['start_time'] = $program_status['start_time'];

                    // Check if the program has been actually restarted; e.g set to start from a previous date
                    if ($program_status['start_time'] < $program->program_start_date) {
                        $entry_data['is_reverted'] = true;
                    }
                } else {
                    $entry_data['start_time'] = $program->program_start_date;
                }

                // Populate all required values
                if (!isset($entry_data['training_days_per_week'])) {
                    $entry_data['training_days_per_week'] = $program->training_days_per_week;
                }

                if (!isset($entry_data['training_days'])) {
                    $entry_data['training_days'] = $program->training_days;
                }

                if (empty($times)) {
                    $entry_data['times'] = $program->times;
                } else {
                    $entry_data['times'] = $this->getTimesArray($times, $program->times);
                }

                $entry_data['program_id'] = $program->program_id;
                $entry_data['program_type'] = $program->program_type;

                $changed_programs[$program->id] = $entry_data;
            }
        }

        return $changed_programs;
    }

    private function programIsChanged(ProgramSelectionModel $program, $entry_data)
    {
        $is_changed = false;
        $start_time = 0;
        $times = [];

        foreach ($entry_data as $field => $value) {

            if (stristr($field, 'times_')) {
                $times[] = $value;
                continue;
            }

            switch ($field) {
                case 'current_program_week':
                    $program_week = $this->getWeeksOffset($program->program_start_date, time());

                    if ($program_week != $value) {
                        $is_changed = true;
                        $program_week = $this->getNumber($program_week);
                        $selected_week = $this->getNumber($value);

                        // Program's week is changed to lower value.
                        // This essentially means that the program is restarted
                        if ($program_week > $selected_week) {
                            $start_time = $program->program_start_date - (abs(($selected_week - $program_week)) * $this->seconds_per_week);
                        } else {
                            $start_time = $program->program_start_date + (($selected_week - $program_week) * $this->seconds_per_week);
                        }
                    } else if (
                        ($program_week == $value) AND
                        (isset($entry_data['current_program_food_type']) AND ($entry_data['current_program_food_type'] != $program->program->subcategory->name))
                    ) {
                        // Special case where only the Program's Food type is changed, but the actual week number remains the same
                        $start_time = $program->program_start_date;
                    }

                    break;

                case 'training_days_per_week':

                    if ($program->training_days_per_week != $value) {
                        $is_changed = true;
                    }

                    break;

                case 'training_days':

                    $program_days = $program->training_days;

                    if (array_diff(json_decode($value), json_decode($program_days))) {
                        $is_changed = true;
                    }

                    break;

                case 'current_program_food_type':

                    $current_sub_cat = $program->program->subcategory->name;
                    if ($current_sub_cat != $value) {
                        $is_changed = true;
                    }

                    break;
            }

        }

        if ($times AND array_diff($times, json_decode($program->times))) {
            $is_changed = true;
        }

        return [
            'is_changed' => $is_changed,
            'start_time' => $start_time,
        ];
    }

    public function getProgramCategories()
    {

        $criteria = new \CDbCriteria();
        $criteria->order = 'category_order DESC, id DESC';

        $categories = ProgramCategoriesModel::model()->findAllByAttributes(array(), $criteria);

        if (empty($categories)) {
            return [];
        }

        return $categories;
    }

    public function getProgramsByCategory()
    {
        $category_id = $this->getCategoryID();

        if (empty($category_id)) {
            return [];
        }

        $order = $this->getSavedVariable('tmp_program_sorting', 'DESC');

        $criteria = new \CDbCriteria();
        $criteria->order = 'name ' . $order;


        // todo: include filtering by component names
        if($this->getSavedVariable('tmp_program_filter')){
            $keyword = $this->getSavedVariable('tmp_program_filter');

            $criteria->addCondition("(`t`.`name` LIKE '%$keyword%')");
            //OR exercises.programexercises.name LIKE '%$keyword%'

            $programs = ProgramModel::model()->with('exercises')->findAllByAttributes([
                'category_id' => $category_id
            ], $criteria);
        } else {
            $programs = ProgramModel::model()->findAllByAttributes([
                'category_id' => $category_id
            ], $criteria);
        }


        if (empty($programs)) {
            return [];
        }

        return $programs;
    }

    public function getExerciseData($type_id = null, $order = null,$paged=false)
    {
        $criteria = new CDbCriteria();
        if ($type_id) {
            $criteria->addInCondition("category_id",[$type_id]);
        }
        if ($order) {
            $criteria->order = 'name ' . $order;
        }
        
        if($this->search_term){
            $criteria->addSearchCondition('name', $this->search_term);
        }

        // if used with infinite listing component
        if($paged){
            $criteria->limit = 15;

            if(isset($_REQUEST['next_page_id'])){
                $criteria->offset = $_REQUEST['next_page_id'];
            }
        }

        return ExerciseModel::model()->findAll($criteria);
    }
    public function addToCalendar($exercise_id, $date)
    {
        $data =[
            'play_id' => $this->playid,
            'type_id' => 8, // Fitness
            'exercise_id' => $exercise_id,
            'program_id' => null,
            'recipe_id' => null,
            'notes' => '',
            'points' => 8,
            'time' => $date,
            'completion' => 0,
            'is_completed' => 0,
        ];
        CalendarModel::insertEntries([$data]);

    }
    public function getCategoryData()
    {
        $category_id = $this->getCategoryID();

        if (empty($category_id)) {
            return new \StdClass;
        }

        $category_data = ProgramCategoriesModel::model()->findByPk($category_id);

        return $category_data;
    }

    private function getCategoryID()
    {
        if (stristr($this->getMenuId(), 'fitness-category-')) {
            $category_id = str_replace('fitness-category-', '', $this->getMenuId());
            $this->sessionSet('current_fitness_category', $category_id);
            return $category_id;
        } else {
            return $this->sessionGet('current_fitness_category');
        }
    }

    public function storeCheckboxesSelections(string $prefix)
    {
        $var_data = [];

        foreach ($this->submitvariables as $variable => $data) {
            if (!stristr($variable, $prefix)) {
                continue;
            }
            $pieces = explode('-', $variable);

            if (!isset($pieces[1])) {
                continue;
            }

            if ($data) {
                $var_data[$pieces[0]][] = $pieces[1];
            }
        }

        foreach ($var_data as $var => $entries) {
            $this->sessionSet($var, json_encode($entries));
        }

        return true;
    }

    public function getWeeksOffset($from, $to)
    {
        $first = \DateTime::createFromFormat('m/d/Y', date('m/d/Y', $from));
        $second = \DateTime::createFromFormat('m/d/Y', date('m/d/Y', $to));

        $offset = floor($first->diff($second)->days / 7);

        return 'Week ' . ($offset + 1);
    }

    public function getHourSelectorData()
    {
        return '4;4am;5;5am;6;6am;7;7am;8;8am;9;9am;10;10am;11;11am;12;12:00;13;1pm;14;2pm;15;3pm;16;4pm;17;5pm;18;6pm;19;7pm;20;8pm;21;9pm;22;10pm;23;11pm';
    }

    public function getHourSelectorData24h()
    {
        return '4;4;5;5;6;6;7;7;8;8;9;9;10;10;11;11;12;12;13;13;14;14;15;15;16;16;17;17;18;18;19;19;20;20;21;21;22;22;23;23';
    }

    public function getMinuteSelectorData()
    {
        return '0;00;15;15;30;30;45;45';
    }

    public function getLengthSelectorData()
    {
        return '15;15min;30;30min;45;45min;60;1h;75;1h 15min;90min;1h 30min;110;1h 45min;120;2h';
    }
    public function getWeekDays()
    {
        return [
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
            'Sunday',
        ];
    }

    public function clearTmpStorage()
    {
        if (empty($this->session_storage)) {
            return false;
        }

        foreach ($this->session_storage as $key => $value) {
            if (stristr($key, 'sw_program_')) {
                $this->sessionUnset($key);
            }
        }

        return true;
    }

    private function getNumber(string $string)
    {
        return (int)filter_var($string, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Combines the user selected time intervals with the program's times
     *
     * @param array $times
     * @param string $program_times
     * @return string
     */
    private function getTimesArray(array $times, string $program_times)
    {
        $predefined_times = json_decode($program_times, true);

        if ($predefined_times) {
            // Failsafe in case there aren't any times recorded in the database
            $output = array_replace($predefined_times, $times);
        } else {
            $output = array_values($times);
        }

        return json_encode($output);
    }

    public function stopProgram(int $id)
    {

        $prs = ProgramSelectionModel::model()->findByPk($id);

        if(isset($prs->program_id)){
            $this->removeCalendarProgramEntries($prs->program_id, $this->playid);
            $this->removeActiveUserProgram($prs->program_id, $this->playid);
            $this->sessionUnset('program_setting_delete_flag');
        }


    }

}