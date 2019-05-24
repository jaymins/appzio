<?php


namespace packages\actionMbooking\themes\tattoo\Models;
use packages\actionMbooking\Models\BookingModel;
use packages\actionMbooking\Models\Model as BootstrapModel;
use packages\actionMitems\Models\ItemModel;

class Model extends BootstrapModel {

    public function getBookingsByStatusAndTime($status, $role)
    {
        if($role == 'user'){
            $condition = 't.play_id = :playId';
        }
        else{
            $condition = 'assignee_play_id = :playId';
        }

        $condition .= ' AND t.status = :status';


        if($status === 'todays'){
            $status = 'confirmed';
            $condition .= " AND FROM_UNIXTIME(date,'%Y-%m-%d') = CURDATE()";
        }

        if($status === 'passed'){
            $status = 'confirmed';
            $condition .= " AND FROM_UNIXTIME(date,'%Y-%m-%d') < CURDATE()";
        }

        return BookingModel::model()
            ->with('item')
            ->findAll(array(
                'order' => 't.id DESC',
                'condition' => $condition,
                'params' => array(
                    ':playId' => $this->playid,
                    ':status' => $status
                )
            ));
    }

    public function getItem(int $itemId): ItemModel
    {
        $item = ItemModel::model()->with('tags','category')->findByPk($itemId);

        if (empty($item) || is_null($item)) {
            return new ItemModel();
        }

        if(!$item->city AND $item->lat AND $item->lon){
            $address = $this->coordinatesToAddress($item->lat,$item->lon);

            if(isset($address['city'])){
                $item->city = $address['city'];
            }

            if(isset($address['country'])){
                $item->country = $address['country'];
                $item->update();
            }
        }

        if(isset($item->tags)){
            foreach($item->tags as $tag){
                $values['title'] = $tag->name;
                $values['id'] = $tag->id;
                $tags[] = $values;
            }

            if(isset($tags) AND !empty($tags)){
                $item->pretty_tags = $tags;
            } else {
                $item->pretty_tags = array();
            }
        }

        $item->owner = \AeplayVariable::getArrayOfPlayvariables($item->play_id);
        return $item;
    }

    /**
     * Returns the booking timestamp created from the date
     * and the hour specified by the person creating it.
     *
     * @return false|int
     */
    protected function getBookingTimestamp()
    {

        $variables = $this->getAllSubmittedVariablesByName();

        $date = date('Y-m-d', $variables['date']);
        $time = $variables['hour'] . ':' . $variables['minutes'];
        $dateObj = new \DateTime($date . ' ' . $time, new \DateTimeZone($this->model->getSavedVariable('timezone_id')));
        $dateObj->setTimezone(new \DateTimeZone('Europe/London'));
        $timestamp = $dateObj->getTimestamp();

        return $timestamp;
    }

    /*
     * Update the user's timezone on certain interval
     * As per default, the update action would happen every 30 minutes
     */
    public function updateTimezone( $timetolive = 600 ) {
        // Update the user's timezone on certain interval
        $location_data = $this->getTimezone(
            $this->getSavedVariable( 'lat' )    ,
            $this->getSavedVariable( 'lon' ),
            '',
            $timetolive
        );

        if ( $location_data ) {
            $this->saveVariable('timezone_id', $location_data['timezone_id']);
            $this->saveVariable('offset_in_seconds', $location_data['offset_in_seconds']);
        }
    }

}
