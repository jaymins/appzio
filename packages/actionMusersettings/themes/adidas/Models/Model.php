<?php


namespace packages\actionMusersettings\themes\adidas\Models;
use packages\actionMitems\Models\ItemModel;
use packages\actionMusersettings\Models\Model as BootstrapModel;
use packages\actionMitems\Models\ItemCategoryModel;
use packages\actionMvenues\Models\VenuesModel;

class Model extends BootstrapModel {

    public function saveCheckboxes($nameList, $prefix = '') {

        if ($nameList == 'category' || $nameList == 'my_styles') {
            $categories = ItemCategoryModel::model()->findAll('app_id = :name', array(':name' => $this->appid));
            $list = [];
            foreach ($categories as $category) {
                $list[] = $category->name;
            }
        } else {
            $list = explode(',', $this->getGlobalVariableByName($nameList));
        }

        $saved = [];

        foreach ($this->getAllSubmittedVariables() as $item => $value) {

            // Item not selected
            if ( empty($value) ) {
                continue;
            }

            $item_cleaned = str_replace($prefix . $nameList . '-', '', $item);
            $saved[] = trim($item_cleaned, '{#}');
        }
        
        /*foreach ($list as $item) {
            if ($this->getSubmittedVariableByName($prefix . $nameList . '-' . trim($item, '{#}'))) {
                $saved[] = trim($item, '{#}');
            }
        }*/

        if ($nameList == 'level' && $this->getSubmittedVariableByName($prefix . $nameList)) {
            $saved[] = $this->getSubmittedVariableByName($prefix . $nameList);
        }

        if (empty($saved)) {
            $this->validation_errors[$prefix . $nameList] = '{#you_must_select_at_least_one#}';
        }

        if(!$this->validation_errors){
            $variables[$prefix . $nameList] = implode(',', $saved);

            foreach ($saved as $one) {
                $this->addToVariable($prefix . $nameList, $one);
            }
        }

        if ($prefix && empty($saved)) {
            $variables[$prefix . $nameList] = ' ';
            $this->saveNamedVariables($variables);
        }

    }

    public function validateSettings(){
        $vars = $this->getAllSubmittedVariablesByName();

        foreach($vars as $key=>$var){

            switch ($key) {
                case 'firstname':
                    $this->firstname = strtolower($var);
                    $this->firstname = ucwords($this->firstname);
                    if (strlen($var) < 2) {
                        $this->validation_errors[$key] = '{#your_name_should_have_at_least_two_characters#}';
                    }
                    break;

                case 'lastname':
                    $this->lastname = strtolower($var);
                    $this->lastname = ucwords($this->lastname);
                    if (strlen($var) < 2) {
                        $this->validation_errors[$key] = '{#your_name_should_have_at_least_two_characters#}';
                    }
                    break;

                case 'email':
                    $this->email = strtolower($var);

                    if (!$this->validateEmail($this->email)) {
                        $this->validation_errors[$key] = '{#please_input_a_valid_email#}';
                    }
                    break;

                case 'phone':
                    $this->phone = str_replace(' ', '', $var);
                    if (!is_numeric($this->phone)) {
                        $this->validation_errors[$key] = '{#please_only_input_numbers#}';
                    }

                    if (strlen($this->phone) < 5) {
                        $this->validation_errors[$key] = '{#please_enter_a_valid_phone_number#}';
                    }

                    break;

            }
        }
    }

    /* save to variables */
    public function saveSettings(){

        $vars = $this->getAllSubmittedVariablesByName();

        $vars['email'] = $this->email;
        $vars['firstname'] = $this->firstname;
        $vars['lastname'] = $this->lastname;
        $vars['real_name'] = $this->firstname .' ' .$this->lastname;
        $vars['phone'] = $this->phone;

        $vars['name'] = $this->firstname;
        $vars['surname'] = $this->lastname;
        $vars['screen_name'] = $this->firstname;


        $vars['about_my_artwork'] = $this->getSubmittedVariableByName('about_my_artwork');
        $vars['what_i_like_to_do'] = $this->getSubmittedVariableByName('what_i_like_to_do');
        $vars['experience'] = $this->getSubmittedVariableByName('experience');
        $vars['instructions'] = $this->getSubmittedVariableByName('instructions');
        $vars['aftercare'] = $this->getSubmittedVariableByName('aftercare');
        $vars['apprenticeship'] = $this->getSubmittedVariableByName('apprenticeship');

        $this->saveNamedVariables($vars);
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

    public function removeFromVariable($variable,$value){

        $var = $this->getSavedVariable($variable);

        if($var){
            $var = json_decode($var,true);
            if(is_array($var) AND !empty($var)){
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

        if(!is_array($var) OR empty($var)){
            $var = array();
        }

        $var = json_encode($var);
        $this->saveVariable($variable,$var);

    }

    public function saveAddressData( $address_data ) {

        if ( empty($address_data) ) {
            return false;
        }

        $data = json_decode( $address_data, true );

        $needed_vars = array(
            'name' => 'address',
            'lat' => 'lat',
            'lon' => 'lon'
        );

        foreach ( $needed_vars as $api_var => $local_var ) {
            if ( !isset($data[$api_var]) OR empty($data[$api_var]) ) {
                continue;
            }

            $this->saveVariable( $local_var,  $data[$api_var] );
        }

        return true;
    }

    public function saveStates() {

        if ( empty($this->getSubmittedVariableByName('states')) ) {
            return false;
        }

        $states = $this->getSubmittedVariableByName('states');

        $data = explode(',', $states);
        $data_to_save = [];

        foreach ($data as $entry) {

            if ( empty($entry) OR strlen($entry) < 3 )
                continue;

            $data_to_save[] = trim($entry);
        }

        $this->saveVariable('states', $data_to_save);

        return false;
    }

    /**
     * Returns additional items added by the given artist.
     * The given item id is ignored.
     *
     * @param $artistId
     * @param $itemId
     * @return array|mixed|null|static[]
     */
    public function getOtherArtistItems($playId,$limit = 3)
    {
        return ItemModel::model()->findAll('play_id = :playId AND type = :type LIMIT '.$limit, array(
            ':playId' => $playId,
            ':type' => 'shop'
        ));
    }

    /**
     *
     * @todo Need to be fixed
     * @param $clubIds
     * @return array
     */
    public function getClubsDetails($clubIds) {
        $ids = explode(',', $clubIds);
        $data = [];
        foreach ($ids as $id) {
            $data[] = VenuesModel::model()->findByPk(2);
        }
        return $data;
    }
}