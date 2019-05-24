<?php


namespace packages\actionMregister\themes\adidas\Models;

use packages\actionMregister\Models\Model as BootstrapModel;

class Model extends BootstrapModel
{
    public function validateMyEmail()
    {
        $email = trim(strtolower($this->getSubmittedVariableByName('email')));

        if (!$this->validateEmail($email)) {
            $this->validation_errors['email'] = '{#please_enter_valid_email#}';
            return true;
        }

        $exists = $this->findPlayFromVariables('email', 'reg_phase', $email, 'complete');

        if ($exists) {
            $this->validation_errors['email'] = '{#looks_like_this_email_already_exists#}';
            $this->validation_errors['email_exists'] = true;
        }

        if (!$this->validation_errors) {
            $this->saveVariable('email', $email);
        }
    }

    /* adds required variables and closes the login */
    public function closeLogin($dologin = true)
    {
        if ($this->getConfigParam('require_login') == 1) {

            return true;
        }



        $this->saveVariable('reg_phase', 'complete');

        if ($this->getSavedVariable('fb_universal_login')) {
            $this->saveVariable('fb_id', $this->getSavedVariable('fb_universal_login'));
        }

        $branch = $this->getConfigParam('login_branch');

        if ($dologin) {
            $this->saveVariable('logged_in', 1);
        }

        if (!$branch) {
            return false;
        }

        $introductionBranchId = $this->getConfigParam('intro_branch');

        $this->rewriteActionField('activate_branch_id', $introductionBranchId);

        \AeplayBranch::closeBranch($branch, $this->playid);
        return true;
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

    /* save to variables */
    public function savePage1()
    {
        $vars['password'] = sha1(strtolower(trim($this->password)));
        $vars['email'] = $this->email;
        $vars['phone'] = $this->phone;
        $vars['firstname'] = $this->firstname;
        $vars['lastname'] = $this->lastname;
        $vars['real_name'] = $this->firstname . ' ' . $this->lastname;
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

}