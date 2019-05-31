<?php

namespace packages\actionDitems\themes\uiKit\Models;

use packages\actionDitems\Models\ItemCategoryModel;
use packages\actionDitems\Models\ItemCategoryRelationModel;
use packages\actionDitems\Models\ItemModel;
use packages\actionDitems\Models\ItemTagRelationModel;
use packages\actionDitems\Models\Model as BootstrapModel;

\Yii::import('ext.PHPExcel.PHPExcel');

class Model extends BootstrapModel
{

    /**
     * Retrieve all items created by given user by type.
     *
     * @param $type string
     * @return array|mixed|null|static[]
     */
    public function getUserItemsByType($type)
    {
        $items = ItemModel::model()
            ->with('reminders')
            ->findAll(array(
                'order' => 't.id DESC',
                'condition' => 'play_id = :playId AND t.type = :type',
                'params' => array(
                    ':playId' => $this->playid,
                    ':type' => $type,
                )
            ));

        return $items;
    }

    /**
     * Get all experts filtered by name.
     *
     * @param string $query
     * @param int $page
     * @return array
     */
    public function getExperts($query = '', $page = 1): array
    {
        $skip = ($page - 1) * 20;

        $experts = ExpertModel::model()->findAll('name LIKE :query OR country LIKE :query ORDER BY name ASC LIMIT 20 OFFSET :skip', array(
            ':query' => '%' . $query . '%',
            ':skip' => $skip
        ));

        return $this->mapExperts($experts);
    }

    /**
     * Get a list of experts filtered by their country, business unit and name.
     *
     * @param string $name
     * @param int $page
     * @param string $country
     * @return array
     */
    public function getExpertsByCountryAndUnit($name = '', $page = 1): array
    {

        $data = \ThirdpartyServices::geoAddressTranslation(
            $this->getSavedVariable('lat'),
            $this->getSavedVariable('lon'),
            $this->appid
        );

        $this->saveVariable('current_country', $data['country']);
        $country = $this->getSavedVariable('current_country');
        $unit = $this->getSavedVariable('business_unit');

        $skip = ($page - 1) * 20;

        $experts = ExpertModel::model()->findAll('business_unit LIKE :business_unit AND country LIKE :country AND name LIKE :name ORDER BY name ASC LIMIT 20 OFFSET :skip', array(
            ':business_unit' => '%' . $unit . '%',
            ':country' => '%' . $country . '%',
            ':name' => '%' . $name . '%',
            ':skip' => $skip
        ));

        return $this->mapExperts($experts);
    }

    /**
     * Get a list of experts filtered by their business_unit and name.
     *
     * @param string $name
     * @param int $page
     * @return array
     */
    public function getExpertsByUnit($name = '', int $page = 1)
    {
        $skip = ($page - 1) * 20;

        $unit = $this->getSavedVariable('business_unit');

        $experts = ExpertModel::model()->findAll('business_unit LIKE :business_unit AND name LIKE :name ORDER BY name ASC LIMIT 20 OFFSET :skip', array(
            ':business_unit' => '%' . $unit . '%',
            ':name' => '%' . $name . '%',
            ':skip' => $skip
        ));

        return $this->mapExperts($experts);
    }

    /**
     * Map experts list to have the composition required by the components.
     *
     * @param $experts
     * @return array
     */
    protected function mapExperts($experts): array
    {
        return array_map(function (ExpertModel $expert) {
            return array(
                'name' => $expert->name,
                'info' => $expert->position . ', ' . $expert->country,
                'business_unit' => $expert->business_unit,
                'contact' => $expert->email,
                'onclick' => new \stdClass()
            );
        }, $experts);
    }

    public function getSubmittedCategoryIds()
    {

        $data = array();
        $allCategories = (empty($this->sessionGet('categories'))) ?
            array() : $this->sessionGet('categories');

        // Merge the currently submitted data
        $submitted_categories = [];

        foreach ($this->submitvariables as $submit_var => $submit_value) {
            if (preg_match('~category~', $submit_var)) {
                $submitted_categories[$submit_var] = $submit_value;
            }
        }

        $allCategories = array_merge($allCategories, $submitted_categories);

        foreach ($allCategories as $variable => $value) {
            if (stristr($variable, 'category|') && !empty($value)) {
                $categoryId = str_replace('category|', '', $variable);
                $model = ItemCategoryModel::model()->findByPk($categoryId);

                $data[] = array(
                    'id' => $model->id,
                    'description' => $value
                );
            }
        }

        return $data;
    }

    public function saveItemAndTagRelation($itemId, $tagIds)
    {
        ItemTagRelationModel::model()->deleteAllByAttributes(array('item_id' => $itemId));
        parent::saveItemAndTagRelation($itemId, $tagIds);
    }

    public function saveItemAndCategoryRelation($itemId, $categories)
    {

        ItemCategoryRelationModel::model()->deleteAllByAttributes(array('item_id' => $itemId));
        $data = array_map(function ($category) use ($itemId) {
            return array(
                'item_id' => $itemId,
                'category_id' => $category['id'],
                'description' => $category['description']
            );
        }, $categories);

        if (empty($data)) {
            return;
        }

        $builder = \Yii::app()->db->schema->commandBuilder;
        $command = $builder->createMultipleInsertCommand('ae_ext_items_category_item', $data);
        $command->execute();
    }

    /**
     * Get all DHL categories.
     * For those who have been assigned to the item, get the description.
     *
     * @param $itemId
     * @return array|mixed|null|static[]
     */
    public function getItemCategoryInformation($itemId): array
    {
        $categories = ItemCategoryModel::model()
            ->with('category_relations')
            ->findAll('app_id = :appId AND item_id = :itemId', array(
                ':appId' => $this->appid,
                ':itemId' => $itemId
            ));

        return $categories;
    }

    public function validateInput()
    {
        $requiredVariables = array(
            'name' => 'empty',
            'date_added' => 'empty'
        );

        $submittedVariables = $this->getAllSubmittedVariablesByName();

        // Make sure that we also validate data, submitted in the first tab
        if ($this->getSavedVariable('temp_preset')) {
            $data_tab_1 = json_decode($this->getSavedVariable('temp_preset'), true);
            $submittedVariables = array_merge($data_tab_1, $submittedVariables);
        }

        foreach ($submittedVariables as $key => $value) {

            foreach ($requiredVariables as $var => $validation_type) {

                $validations = explode('|', $validation_type);

                foreach ($validations as $validation) {

                    if ($key != $var) {
                        continue;
                    }

                    if ($validation == 'empty' AND empty($value)) {
                        $this->validation_errors[$key] = '{#the_' . $key . '_field_is_required#}';
                    } else if ($value AND preg_match('~length~', $validation)) {
                        $rq_chars = str_replace('length:', '', $validation);
                        if (strlen($value) > $rq_chars)
                            $this->validation_errors[$key] = '{#the_' . $key . '_field_should_contain_' . $rq_chars . '_characters_max#}';
                    }
                }
            }
        }

        if (!strstr($this->getMenuId(), 'edit_')) {
            $slug = $this->playid . '-' . $this->getSubmittedVariableByName('name') . '-' . date('d-M-Y-H:m');

            $itemExists = ItemModel::model()->find('slug = :slug', array('slug' => $slug));
            if (!empty($itemExists)) {
                $this->validation_errors['duplicate'] = 'You just created a visit for the same team name. Please check and update the existing visit or enter another team name.';
            }
        }

    }

    /* this function treats variable as a json list where it adds a value
    note that if variable includes a string, it will overwrite it */
    public function addToVariable($variable, $value)
    {
        $var = $this->getSavedVariable($variable);

        if ($var) {
            $var = json_decode($var, true);
            if (is_array($var) AND !empty($var)) {
                if (in_array($value, $var)) {
                    return false;
                } else {
                    array_push($var, $value);
                }
            }
        }

        if (!is_array($var) OR empty($var)) {
            $var = array();
            array_push($var, $value);
        }

        $var = json_encode($var);
        $this->saveVariable($variable, $var);
    }

    public function removeFromVariable($variable, $value)
    {

        $var = $this->getSavedVariable($variable);

        if ($var) {
            $var = json_decode($var, true);
            if (is_array($var) AND !empty($var)) {
                $remove = false;

                foreach ($var as $key => $search) {
                    if ($value == $search) {
                        $remove = $key;
                    }
                }
                if ($remove !== false) {
                    unset($var[$remove]);
                }
            }
        }

        if (!is_array($var) OR empty($var)) {
            $var = array();
        }

        $var = json_encode($var);
        $this->saveVariable($variable, $var);
    }

    /* this function treats variable as a json list where it adds a value
   note that if variable includes a string, it will overwrite it */
    public function addToVariableCounting($variable, $value)
    {

        $var = $this->getSavedVariable($variable);

        if ($var) {
            $var = json_decode($var, true);
            if (is_array($var) AND !empty($var)) {
                $found = 0;
                foreach ($var as $key => $item) {
                    $itemParts = explode('|', $item);
                    if ($itemParts[0] == $value) {
                        $itemParts[1]++;
                        $found = 1;
                    }

                    $var[$key] = implode('|', $itemParts);
                }
                if (!$found) {
                    array_push($var, $value . '|1');
                }
            }
        }

        if (!is_array($var) OR empty($var)) {
            $var = array();
            array_push($var, $value . '|1');
        }

        $var = json_encode($var);
        $this->saveVariable($variable, $var);

    }

    public function getFirstWeeklyOccurrence($event)
    {

        $map = $this->numberToWeekDays();
        $interval = $event->reminders[0]->pattern->separation_count;
        $days = explode(',', $event->reminders[0]->pattern->day_of_week);

        $byday = [];

        foreach ($days as $day) {
            $byday[] = $map[$day];
        }

        if (empty($interval))
            $interval = 1;

        $rrule = new \RRule([
            'FREQ' => 'WEEKLY',
            'INTERVAL' => $interval,
            'BYDAY' => implode(', ', $byday),
            'DTSTART' => date('Y-m-d, g:i a', $event->reminders[0]->date),
            'UNTIL' => date('Y-m-d, g:i a', $event->reminders[0]->end_date),
        ]);

        foreach ($rrule as $occurrence) {

            $occurrence_stamp = strtotime($occurrence->format('F j, Y, g:i a'));

            // The Occurence period has already passed
            if ($occurrence_stamp < time()) {
                continue;
            }

            return 'Upcoming in ' . $occurrence->format('l, dS \of F');
        }

        return '{#the_event_wouldn\'t_occur_again#}';
    }

    public function getFirstMontlyOccurrence($event)
    {

        $interval = $event->reminders[0]->pattern->separation_count;
        $day = $event->reminders[0]->pattern->day_of_month;

        if (empty($interval))
            $interval = 1;

        $rrule = new \RRule([
            'FREQ' => 'MONTHLY',
            'INTERVAL' => $interval,
            'BYMONTHDAY' => $day,
            'DTSTART' => date('Y-m-d, g:i a', $event->reminders[0]->date),
            'UNTIL' => date('Y-m-d, g:i a', $event->reminders[0]->end_date),
        ]);

        foreach ($rrule as $occurrence) {

            $occurrence_stamp = strtotime($occurrence->format('F j, Y, g:i a'));

            // The Occurence period has already passed
            if ($occurrence_stamp < time()) {
                continue;
            }

            return 'Upcoming in ' . $occurrence->format('l, dS \of F');
        }

        return '{#the_event_wouldn\'t_occur_again#}';
    }

    public function getEventPattern($event_reminder)
    {
        if (isset($event_reminder->pattern->recurring_type)) {
            return $event_reminder->pattern->recurring_type;
        }

        return false;
    }

    public function getRecurringDays($days_data)
    {
        $days = explode(',', $days_data);

        if (count($days) == 1) {
            return jddayofweek($days[0] - 1, 1);
        }

        // Fix the order of the days
        sort($days);

        $output = '';
        $second_to_last = array_slice($days, -2, 1);

        foreach ($days as $i => $day) {
            $output .= jddayofweek($day - 1, 1);

            if ($day == $second_to_last[0]) {
                $output .= ' and ';
            } else if (($i + 1) < count($days)) {
                $output .= ', ';
            }

        }

        return $output;
    }

    public function getRecurringMonths($day)
    {
        $date = date('m-Y');
        $date_obj = date_create($day . '-' . $date);
        return 'the ' . $date_obj->format('dS');
    }

    public function getRecurringSeparation($separation_count)
    {

        switch ($separation_count) {
            case '1':
                return '{#every#}';
                break;

            case '2':
                return '{#every_second#}';

            case '3':
                return '{#every_third#}';
                break;

            case '4':
                return '{#every_forth#}';
                break;

            case '5':
                return '{#every_fifth#}';
                break;

            case '6':
                return '{#every_sixth#}';
                break;
        }

        return '{#every#}';
    }

    public function getSortedReminders()
    {
        $reminders = $this->getAllReminders();

        $output = [];

        foreach ($reminders as $reminder) {
            $item = $reminder->item;
            $type = $item->type;

            $date = $reminder->date;

            if ($type == 'routine' AND !empty($reminder->pattern)) {
                $date = $this->getNextValidDate($reminder);
            }

            $reminder->next_date = $date;

            $output[] = $reminder;
        }

        usort($output, array($this, 'nextDateSort'));

        return $output;
    }

    public function nextDateSort($a, $b)
    {
        if ($a->next_date == $b->next_date) {
            return 0;
        }

        return ($a->next_date > $b->next_date) ? 1 : -1;
    }

    public function getNextValidDate($reminder)
    {

        $interval = $reminder->pattern->separation_count;
        if (empty($interval))
            $interval = 1;

        // Monthly event
        if ($day_of_month = $reminder->pattern->day_of_month) {

            $rrule = new \RRule([
                'FREQ' => 'MONTHLY',
                'INTERVAL' => $interval,
                'BYMONTHDAY' => $day_of_month,
                'DTSTART' => date('Y-m-d, g:i a', $reminder->date),
                'UNTIL' => date('Y-m-d, g:i a', $reminder->end_date),
            ]);

            foreach ($rrule as $occurrence) {

                $occurrence_stamp = strtotime($occurrence->format('F j, Y, g:i a'));

                // The Occurence period has already passed
                if ($occurrence_stamp < time()) {
                    continue;
                }

                return $occurrence_stamp;
            }
        } else if ($day_of_week = $reminder->pattern->day_of_week) {

            $map = $this->numberToWeekDays();
            $days = explode(',', $day_of_week);

            $byday = [];

            foreach ($days as $day) {
                $byday[] = $map[$day];
            }

            $rrule = new \RRule([
                'FREQ' => 'WEEKLY',
                'INTERVAL' => $interval,
                'BYDAY' => implode(', ', $byday),
                'DTSTART' => date('Y-m-d, g:i a', $reminder->date),
                'UNTIL' => date('Y-m-d, g:i a', $reminder->end_date),
            ]);

            foreach ($rrule as $occurrence) {

                $occurrence_stamp = strtotime($occurrence->format('F j, Y, g:i a'));

                // The Occurence period has already passed
                if ($occurrence_stamp < time()) {
                    continue;
                }

                return $occurrence_stamp;
            }

        }

        // The routine wouldn't re-occur for some reason
        return strtotime('+ 1 year');
    }

    public function getSubmittedImages()
    {
        $index = 0;
        $images = [];

        while ($index <= 10) {
            $image = $this->getSavedVariable('visit_pic_' . $index);

            if ($image) {
                $images[] = $image;
            }

            $index++;
        }

        return $images;
    }

    /**
     * Clear the temporary item data after storing it successfully to the DB
     */
    public function clearTemporaryData()
    {
        $this->deleteVariable('visit_pic_1');
        $this->deleteVariable('visit_pic_2');
        $this->deleteVariable('visit_pic_3');
        $this->deleteVariable('temp_preset');

        // $this->sessionUnset('itemId');
        $this->sessionUnset('tags');
        $this->sessionUnset('categories');

        return true;
    }

    public function getFormattedTags($tags, $get_from_session = false)
    {
        if ($get_from_session) {
            return (array)$this->sessionGet('tags');
        }

        if (empty($tags)) {
            return [];
        }

        $output = [];

        foreach ($tags as $tag) {
            $output[] = $tag['title'];
        }

        $this->sessionSet('tags', $output);

        return $output;
    }

    /*
     * Get all submitted tags
     * This method would combine:
     * a) the user's input from the "Type tag" field
     * b) the current state of the "tags" session variable
     */
    public function getSubmittedTags()
    {
        $data = $this->getSubmittedVariableByName('item_tags');
        $tags = explode(', ', $data);

        $submitted_tags = array_filter($tags, [$this, '_remove_empty_internal']);
        $saved_tags = $this->sessionGet('tags');

        if (empty($saved_tags) OR !is_array($saved_tags)) {
            $saved_tags = [];
        }

        $all_tags = array_unique(array_merge($saved_tags, $submitted_tags));

        foreach ($all_tags as $tag_item) {
            $this->addToVariable('user_tags', $tag_item);
        }

        return $all_tags;
    }

    public function removeTag()
    {
        $tags = [];

        if ($this->sessionGet('tags')) {
            $tags = $this->sessionGet('tags');
        }

        $tagToDelete = $this->getMenuId();

        $tags = array_filter($tags, function ($tag) use ($tagToDelete) {
            return $tag !== $tagToDelete ? 1 : 0;
        });

        $this->sessionSet('tags', $tags);
    }

    public function getVisits(int $limit = 20, int $page = null, bool $with_categories = false)
    {

        $range_from = $this->getSavedVariable('filter_time_from');
        $range_to = $this->getSavedVariable('filter_time_to');
        $countries = $this->getSavedVariable('filter_select_country');

        $condition = 'item.type = :type';
        $params = [
            ':type' => 'visit'
        ];

        if ($range_from AND $range_to) {
            $condition .= ' AND (date_added >= :range_from AND date_added < :range_to)';
            $params[':range_from'] = $range_from;
            $params[':range_to'] = $range_to;
        }

        $q = new \CDbCriteria;
        $q->alias = 'item';
        $q->condition = $condition;
        $q->params = $params;
        $q->limit = $limit;
        $q->order = 'id DESC';

        if ($with_categories) {
            $q->with = [
                'category_relations' => [
                    'with' => 'category'
                ]
            ];
        }

        $countries_array = @json_decode($countries, true);
        if (!empty($countries_array)) {
            $q->addInCondition('country', $countries_array);
        }

        if ($page) {
            if ($page == 1) {
                $offset = '0';
            } else {
                $offset = ($page - 1) * $limit;
            }
        }

        if (isset($offset)) {
            $q->offset = $offset;
        }

        $items = ItemModel::model()->findAll($q);

        if (empty($items)) {
            return [];
        }

        return $items;
    }

    public function getTopVisits(string $field)
    {

        $month = $this->getSavedVariable('filter_month', date('F'));
        $year = $this->getSavedVariable('filter_year', date('Y'));

        $stamp = strtotime($month . ' ' . $year . ' GMT');

        $sql = "SELECT {$field}, count({$field}) as frequency
                from ae_ext_items
                WHERE type = 'visit' AND
                (date_added >= :stamp AND date_added < UNIX_TIMESTAMP(TIMESTAMPADD(MONTH,1,:date)))                 
                GROUP BY {$field}
                ORDER BY frequency DESC
                LIMIT 5";

        $visits = \Yii::app()->db->createCommand($sql)
            ->bindValues([
                ':stamp' => $stamp,
                ':date' => date('Y-m-d', $stamp),
            ])
            ->queryAll();

        return $visits;
    }

    public function exportVisits()
    {
        $category_map = [
            'Discipline' => 1,
            'Team Engagement' => 2,
            'Performance Review' => 3,
            'Continuous Improvement' => 4,
            'Next Steps' => 5,
        ];

        try {

            $objPHPExcel = new \PHPExcel();

            // Set document properties
            $objPHPExcel
                ->getProperties()
                ->setCreator('Appzio')
                ->setLastModifiedBy("Appzio")
                ->setTitle("DHL Visits Export")
                ->setSubject("DHL Visits Export")
                ->setDescription("DHL Visits Export")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("DHL Visits Export");

            $sheets = [
                'Visits',
            ];

            $visits = $this->getVisits(1000, 1, true);

            $count = 2;

            foreach ($sheets as $i => $title) {

                if ($i) {
                    $objPHPExcel->createSheet();
                }

                $objPHPExcel->getActiveSheet()
                    ->setCellValue('A1', 'user mail address')
                    ->setCellValue('B1', 'user division')
                    ->setCellValue('C1', 'country')
                    ->setCellValue('D1', 'team name')
                    ->setCellValue('E1', 'date')
                    ->setCellValue('F1', 'notes (discipline)')
                    ->setCellValue('G1', 'notes (team)')
                    ->setCellValue('H1', 'notes (performance)')
                    ->setCellValue('I1', 'notes (improvements)')
                    ->setCellValue('J1', 'notes (next steps)');

                foreach ($visits as $visit) {

                    // Get the play variables data
                    $visit->extra_data = \AeplayVariable::getArrayOfPlayvariables($visit->play_id);

                    $email = 'N/A';
                    if ($visit->extra_data['email']) {
                        $email = $visit->extra_data['email'];
                    }

                    $unit = 'N/A';
                    if (isset($visit->extra_data['business_unit']) AND $visit->extra_data['business_unit']) {
                        $unit = $visit->extra_data['business_unit'];
                    }

                    $objPHPExcel->getActiveSheet()
                        ->setCellValueByColumnAndRow(0, $count, $email);

                    $objPHPExcel->getActiveSheet()
                        ->setCellValueByColumnAndRow(1, $count, $unit);

                    $objPHPExcel->getActiveSheet()
                        ->setCellValueByColumnAndRow(2, $count, $visit->country);

                    $objPHPExcel->getActiveSheet()
                        ->setCellValueByColumnAndRow(3, $count, $visit->name);

                    $objPHPExcel->getActiveSheet()
                        ->setCellValueByColumnAndRow(4, $count, date('d M Y', $visit->date_added));

                    if ($visit->category_relations) {
                        foreach ($visit->category_relations as $relation) {
                            $category = $relation->category;
                            $index = $category_map[$category['name']];

                            $objPHPExcel->getActiveSheet()
                                ->setCellValueByColumnAndRow($index + 4, $count, $relation->description);
                        }
                    }

                    $count++;
                }

                $objPHPExcel->getActiveSheet()->setTitle($title);
            }

            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);

            $exports_path = 'documents/games/' . $this->appid . '/exports/';
            $path = $_SERVER['DOCUMENT_ROOT'] . dirname($_SERVER['PHP_SELF']) . $exports_path;

            if (!is_dir($path)) {
                mkdir($path, 0777);
            }

            $filename = 'visits-' . time() . '.xlsx';

            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save($path . $filename);

            \Aenotification::addUserEmail(
                $this->playid,
                '!MPROVE - Your Visits Export',
                $this->getMailBody(),
                $this->appid,
                $this->getSavedVariable('email'),
                array(
                    'file1' => $path . $filename,
                )
            );

            return true;

        } catch (\Exception $e) {

        }

        return false;
    }

    private function getMailBody()
    {
        $name = $this->getSavedVariable('firstname');

        $body = "Dear {$name},<br><br>";
        $body .= "Please find your visits export attached to this email.<br><br>";
        $body .= "Kind regards<br>";
        $body .= "Your Continuous Improvement / First Choice team<br><br>";
        $body .= "Mail: Dgf-firstchoice@dhl.com";

        return $body;
    }

    public function _remove_empty_internal($value)
    {
        return !empty($value) AND strlen($value) > 1 AND $value !== 0;
    }

    /**
     * Sets the current user's country as a default tag
     *
     * @return bool
     */
    public function setDefaultTag()
    {
        if (empty($this->getSavedVariable('current_country'))) {
            return false;
        }

        $this->sessionSet('tags', [
            $this->getSavedVariable('current_country')
        ]);

        return true;
    }

    /**
     * Sets all images, saved into the DB as session variables
     *
     * @param ItemModel $item
     * @return bool
     */
    public function setImageSessionVariables(ItemModel $item)
    {
        $images_data = json_decode($item->images, true);

        if (empty($images_data)) {
            return false;
        }

        foreach ($images_data as $i => $image) {
            $this->saveVariable('visit_pic_' . ($i+1), $image);
            // $this->sessionSet('visit_pic_' . ($i+1), $image);
        }

        return true;
    }

    public function setupCategories(int $item_id)
    {        
        /*if ($this->sessionGet('categories')) {
            return false;
        }*/

        $sessionCategories = [];
        $categories = $this->getItemCategoryInformation($item_id);

        foreach ($categories as $category) {
            $sessionCategories['category|' . $category->id] = $category->category_relations[0]->description ?? '{#no_notes_added#}';
        }

        $this->sessionSet('categories', $sessionCategories);

        return true;
    }

}