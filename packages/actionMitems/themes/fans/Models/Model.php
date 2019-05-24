<?php

namespace packages\actionMitems\themes\fans\Models;

use packages\actionMitems\Models\ItemCategoryModel;
use packages\actionMitems\Models\ItemCategoryRelationModel;
use packages\actionMitems\Models\ItemLikeModel;
use packages\actionMitems\Models\ItemTagRelationModel;
use packages\actionMitems\Models\Model as BootstrapModel;
use packages\actionMitems\Models\ItemModel;

class Model extends BootstrapModel
{


    public function getEvent($eventid)
    {
        return ItemModel::model()->with('places')->findByPk($eventid);

    }


    public function getEvents( $query = 'all' )
    {

        $params = array(
            'type' => 'match-event'
        );

        if ( $query == 'my-events' ) {
            $params['play_id'] = $this->playid;
        }

        $output = ItemModel::model()->with('places')->findAllByAttributes($params);

        if(!$output){
            return array();
        }

        return $output;
    }

    public function getMyEvents()
    {

        $mylikes = ItemLikeModel::model()->findAllByAttributes(array('play_id' => $this->playid,'status' => 'going'));
        $output = array();

        foreach($mylikes as $like){
            $output[] = ItemModel::model()->with('places')->findByPk($like->item_id);
        }

        return $output;
    }


    public function going($eventid)
    {
        $obj = new ItemLikeModel();
        $obj->play_id = $this->playid;
        $obj->item_id = $eventid;
        $obj->status = 'going';
        $obj->insert();

    }


    public function getParticipants($id)
    {

        $output = array();
        
        $participants = ItemLikeModel::model()->findAllByAttributes(array('item_id' => $id));

        foreach($participants as $participant){
            $output[$participant->play_id] = \AeplayVariable::getArrayOfPlayvariables($participant->play_id);
        }

        return $output;
    }





    public function getCharacters(){
        $characters = $this->getConfigParam('characters');
        $characters = explode(';', $characters);

        $count = 0;

        foreach ($characters as $character){
            if($count == 1 AND isset($title)){
                $output[] = array('title' => $title,'content' => $character);
                unset($title);
                continue;
            }

            $title = $character;
            $count = 1;
        }

        return $output;
    }

    public function getDashboard(){
        $values = $this->getConfigParam('values');
        $values = explode(chr(10), $values);

        $output['header'] = $this->getConfigParam('table_header');
        $output['columns'] = explode(';',$this->getConfigParam('columns'));

        if(!is_array($output['columns']) OR empty($output['columns'])){
            return false;
        }

        foreach ($values as $value){
            $value = explode(';', $value);
            if(!isset($value[2])){
                continue;
            }
            $set['title'] = $value[0];
            $set['columns'] = array($value[1],$value[2]);
            $output['data'][] = $set;
        }

        return $output;
    }

    public function getDownloads(){
        $values = $this->getConfigParam('downloads');
        $values = explode(chr(10), $values);


        foreach ($values as $value){
            trim($value);

            $value = explode(';', $value);

            if(!isset($value[1])){
                continue;
            }

            $title = trim($value[0]);
            $file = trim($value[1]);
            $description = isset($value[2]) ? trim($value[2]) : '';

            if (!filter_var($file, FILTER_VALIDATE_URL)) {
                continue;
            }

            $set['title'] = $title;
            $set['file'] = $file;
            $set['format'] = substr($file, -3);
            $set['size'] = $this->curl_get_file_size($file);
            $set['description'] = $description;
            $output[] = $set;

        }

        return $output;
    }

    function curl_get_file_size( $url ) {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_NOBODY, TRUE);

        $data = curl_exec($ch);
        $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

        curl_close($ch);

        $size = round($size/1024/1024,1) .'Mb';
        return $size;
    }


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

        $experts = ExpertModel::model()->findAll('name LIKE :query OR country LIKE :query LIMIT 20 OFFSET :skip', array(
            ':query' => '%' . $query . '%',
            ':skip' => $skip
        ));

        return $this->mapExperts($experts);
    }

    /**
     * Get a list of experts filtered by their country and name.
     *
     * @param string $name
     * @param int $page
     * @param string $country
     * @return array
     */
    public function getExpertsByCountry($name = '', $page = 1, $country): array
    {
        $skip = ($page - 1) * 20;

        $experts = ExpertModel::model()->findAll('country LIKE :country AND name LIKE :name LIMIT 20 OFFSET :skip', array(
            ':country' => '%' . $country . '%',
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
                'contact' => $expert->email,
                'onclick' => new \stdClass()
            );
        }, $experts);
    }

    public function getSubmittedCategoryIds()
    {
        
        $data = array();
        $allCategories = empty($this->sessionGet('categories')) ?
            array() : $this->sessionGet('categories');

        // Merge the currently submitted data
        $submitted_categories = [];

        foreach ($this->submitvariables as $submit_var => $submit_value) {
            if ( preg_match('~category~', $submit_var) ) {
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

    public function saveItemAndTagRelation($itemId, $tagIds) {
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

        foreach ($submittedVariables as $key => $value) {

            foreach ($requiredVariables as $var => $validation_type) {

                $validations = explode('|', $validation_type);

                foreach ($validations as $validation) {

                    if ($key != $var) {
                        continue;
                    }

                    if ($validation == 'empty' AND empty($value)) {
                        $this->validation_errors[$key] = '{#the_'. $key .'_field_is_required#}';
                    } else if ($value AND preg_match('~length~', $validation)) {
                        $rq_chars = str_replace('length:', '', $validation);
                        if (strlen($value) > $rq_chars)
                            $this->validation_errors[$key] = '{#the_' . $key . '_field_should_contain_' . $rq_chars . '_characters_max#}';
                    }
                }
            }
        }

        if ( !strstr($this->getMenuId(), 'edit_') ) {

            $slug = $this->playid . '-' . $this->getSubmittedVariableByName('name') . '-' . date('d-M-Y-H:m');

            $itemExists = ItemModel::model()->find('slug = :slug', array('slug' => $slug));

            if (!empty($itemExists)) {
                $this->validation_errors['duplicate'] = 'You just created a visit for the same team name. Please check and update the existing visit or enter another team name.';
            }

        }

    }

    /* this function treats variable as a json list where it adds a value
    note that if variable includes a string, it will overwrite it */
    public function addToVariable($variable,$value){

        $var = $this->getSavedVariable($variable);

        if($var){
            $var = json_decode($var,true);
            if(is_array($var) AND !empty($var)){
                if(in_array($value,$var)){
                    return false;
                } else {
                    array_push($var,$value);
                }
            }
        }

        if(!is_array($var) OR empty($var)){
            $var = array();
            array_push($var,$value);
        }

        $var = json_encode($var);
        $this->saveVariable($variable,$var);

    }

    /* this function treats variable as a json list where it adds a value
   note that if variable includes a string, it will overwrite it */
    public function addToVariableCounting($variable,$value){

        $var = $this->getSavedVariable($variable);

        if($var){
            $var = json_decode($var,true);
            if(is_array($var) AND !empty($var)){
                $found = 0;
                foreach ($var as $key => $item) {
                    $itemParts = explode('|', $item);
                    if ($itemParts[0] == $value) {
                        $itemParts[1] ++;
                        $found = 1;
                    }

                    $var[$key] = implode('|', $itemParts);
                }
               if (!$found) {
                   array_push($var, $value . '|1');
               }
            }
        }

        if(!is_array($var) OR empty($var)){
            $var = array();
            array_push($var,$value . '|1');
        }

        $var = json_encode($var);
        $this->saveVariable($variable,$var);

    }

	public function getFirstWeeklyOccurrence( $event ) {

		$map = $this->numberToWeekDays();
		$interval = $event->reminders[0]->pattern->separation_count;
		$days = explode(',', $event->reminders[0]->pattern->day_of_week);

		$byday = [];

		foreach ( $days as $day ) {
			$byday[] = $map[$day];
		}

		if ( empty($interval) )
			$interval = 1;

		$rrule = new \RRule([
			'FREQ' => 'WEEKLY',
			'INTERVAL' => $interval,
			'BYDAY' => implode(', ', $byday),
			'DTSTART' => date( 'Y-m-d', $event->reminders[0]->date ),
			'UNTIL' => date( 'Y-m-d', $event->reminders[0]->end_date ),
		]);

		foreach ($rrule as $occurrence ) {

			$occurrence_stamp = strtotime( $occurrence->format('F j, Y, g:i a') );

			// The Occurence period has already passed
			if ( $occurrence_stamp < time() ) {
				continue;
			}

			return 'Upcoming in '. $occurrence->format('l, dS \of F');
		}

		return '{#the_event_wouldn\'t_occur_again#}';
	}

	public function getFirstMontlyOccurrence( $event ) {

		$interval = $event->reminders[0]->pattern->separation_count;
		$day = $event->reminders[0]->pattern->day_of_month;

		if ( empty($interval) )
			$interval = 1;

		$rrule = new \RRule([
			'FREQ' => 'MONTHLY',
			'INTERVAL' => $interval,
			'BYMONTHDAY' => $day,
			'DTSTART' => date( 'Y-m-d', $event->reminders[0]->date ),
			'UNTIL' => date( 'Y-m-d', $event->reminders[0]->end_date ),
		]);

		foreach ($rrule as $occurrence ) {

			$occurrence_stamp = strtotime( $occurrence->format('F j, Y, g:i a') );

			// The Occurence period has already passed
			if ( $occurrence_stamp < time() ) {
				continue;
			}

			return 'Upcoming in '. $occurrence->format('l, dS \of F');
		}

		return '{#the_event_wouldn\'t_occur_again#}';
	}

	public function getEventPattern( $event_reminder ) {
		if ( isset($event_reminder->pattern->recurring_type) ) {
			return $event_reminder->pattern->recurring_type;
		}

		return false;
	}

	public function getRecurringDays( $days_data ) {
		$days = explode(',', $days_data);

		if ( count($days) == 1 ) {
			return jddayofweek($days[0] - 1, 1);
		}

		// Fix the order of the days
		sort($days);

		$output = '';
		$second_to_last = array_slice($days, -2, 1);

		foreach ( $days as $i => $day ) {
			$output .= jddayofweek($day - 1, 1);

			if ( $day == $second_to_last[0] ) {
				$output .= ' and ';
			} else if ( ($i+1) < count($days) ) {
				$output .= ', ';
			}

		}

		return $output;
	}

	public function getRecurringMonths( $day ) {
		$date = date( 'm-Y' );
		$date_obj = date_create( $day . '-'  . $date );
		return 'the ' . $date_obj->format('dS');
	}

	public function getRecurringSeparation( $separation_count ) {

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

    public function getFanclubs() {
        return ItemModel::model()->findAll('type = :type AND place_id = :placeId', array(
            ':type' => 'club',
            ':placeId' => $this->getSavedVariable('selected_football_club')
        ));
    }

    public function isFanclubLiked($itemId) {
        $items = ItemLikeModel::model()->findAll('item_id = :itemId AND play_id = :playId', array(
            ':itemId' => $itemId,
            ':playId' => $this->playid
        ));


        if ($items) {
            return current($items)->id;
        }

        return false;
    }

    public function likeFanclub($itemId) {
        $itemLiked = new ItemLikeModel();
        $itemLiked->play_id = $this->playid;
        $itemLiked->item_id = $itemId;
        $itemLiked->status = 'liked';
        $itemLiked->save();
    }

    public function dislikeFanclub($itemId) {

        if ( ItemLikeModel::model()->findByPk( $itemId ) ) {
            ItemLikeModel::model()->deleteByPk( $itemId );
            return true;
        }

        return false;
    }

}