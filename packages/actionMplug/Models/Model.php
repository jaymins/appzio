<?php

/**
 * Default model for the action. It has access to all data provided by the BootstrapModel.
 *
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMplug\Models;
use Bootstrap\Models\BootstrapModel;

class Model extends BootstrapModel {

    /**
     * This variable doesn't actually need to be declared here, but but here for documentation's sake.
     * Validation erorr is an array where validation errors are saved and can be accessed by controller,
     * view and components.
     */
    public $validation_errors;


    public function doLogin()
    {
        $username = $this->getSubmittedVariableByName('username');
        $password = $this->getSubmittedVariableByName('password');

        \Yii::import('aegameauthor.models.*');

        $app = \AePMApps::model()->findByAttributes(array('test_username' => $username,'test_userpw' => $password));

        if(!$app){
            $this->validation_errors[] = 'No app found with this login information';
            return false;
        }

        $server = $app->server;

        /* debug */
        //$server = str_replace('https://', 'http://', $server);

        $api_key = $app->api_key;

        if(substr($server, -1) != '/'){
            $server = $server .'/';
        }

        $testurl = $server.'api/' .$api_key .'/apps/getiosbuildassets?secretkey=dj22asss-sk2231593';

        $test = @file_get_contents($testurl);
        $json_test = @json_decode($test);

        if(!isset($json_test->msg) OR $json_test->msg != 'ok'){
            $this->validation_errors[] = "Looks like we couldn't connect to the app. Please check the app congiguration";
            return false;
        }

        return array(
            'server' => $server,
            'api_key' => $api_key
        );

    }




}