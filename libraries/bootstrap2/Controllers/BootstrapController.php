<?php


namespace Bootstrap\Controllers;

use Bootstrap\Components\ClientComponents\Onclick;
use Bootstrap\Components\ComponentHelpers;
use Bootstrap\Components\ComponentParameters;
use Bootstrap\Router\BootstrapRouter;

/**
 * Class BootstrapController
 * @package Bootstrap\Controllers
 */
class BootstrapController implements BootstrapControllerInterface {

    /* this is here just to fix a phpstorm auto complete bug with namespaces */
    /* @var \Bootstrap\Models\BootstrapModel */
    public $phpstorm_bugfix;

    /**
     * The view instance. Views are responsible for returning the layout of the app dependining on the information passed from the controller.
     *
     * @var \Bootstrap\Views\BootstrapView
     */
    public $view;

    /**
     * The model instance. Models are used to query and save data to storage.
     * They also provide access to variable, session, validation and other utility methods.
     *
     * @var \Bootstrap\Models\BootstrapModel
     */
    public $model;

    /**
     * The router class instance. The router is responsible for instantiating the controller and the view.
     *
     * @var BootstrapRouter
     */
    public $router;

    /**
     * Current active tab
     *
     * @var
     */
    public $current_tab;

    /**
     * @var
     */
    private $view_name;

    /**
     * Active action name
     *
     * @var mixed
     */
    public $action_name;

    /**
     * Currently logged user id
     *
     * @var
     */
    public $playid;

    /**
     * Array of actions that should be triggered when the application loads.
     * These can vary but making a refresh on load can cause an infinite loop.
     *
     * @var
     */
    public $onloads;

    /**
     * Like onload, but will only execute once and force action to reload when accessing
     * the action second time.
     *
     * @var
     */
    public $loadonce;

    /**
     * Set this to true to suppress output (for async operations)
     */
    public $no_output = false;

    /**
     * Set to true if the current call is listbranches
     */
    public $list_branches;

    /**
     * Add onclicks here to command client non-interactively
     */
    public $commands = [];

    use Onclick;
    use ComponentParameters;
    use ComponentHelpers;

    /**
     * BootstrapController constructor.
     * @param $obj
     */
    public function __construct($obj){
        /* this exist to make the referencing of
        passed objects & variables easier */

        while($n = each($this)){
            $key = $n['key'];
            if(isset($obj->$key) AND !$this->$key){
                $this->$key = $obj->$key;
            }
        }

/*        echo(\Yii::app()->timeZone);
        echo('<br>');
        echo(time());
        echo(date_default_timezone_get());
        echo('<br>');
*/

        // Adjust the user's timezone
        if ( $timezone = $this->model->getSavedVariable('timezone_id') ) {
            if($this->isValidTimezone($timezone)){
                date_default_timezone_set($timezone);
            } else {
                date_default_timezone_set(\Yii::app()->timeZone);
            }
        }

/*        echo(time());
        echo(date_default_timezone_get());

        die();
*/
        $this->action_name = $this->router->getActionName();
    }

    private function isValidTimezone($timezone) {
        return in_array($timezone, timezone_identifiers_list());
    }

    /**
     * Default entry point for controllers
     *
     * @return array
     */
    public function actionDefault(){
        return ['View','viewerror'];
    }

    /**
     * @return array
     */
    public function viewError(){
        return ['View','viewerror'];
    }

    /**
     * Return current menu id if set
     *
     * @return mixed
     */
    public function getMenuId(){
        return $this->router->getMenuId();
    }

    /**
     * Collects location once
     *
     * @return mixed
     */
    public function collectLocation( $timetolive = false, $cache_name = 'location-asked' ){
        $cache = \Appcaching::getGlobalCache($cache_name . $this->playid);

        if(!$cache){
            $task = new \stdClass();
            $task->action = 'ask-location';
            \Appcaching::setGlobalCache($cache_name . $this->playid,true, $timetolive);
            $this->onloads[] = $task;
        } else {
            return false;
        }
    }

    /**
     * Collects location once
     *
     * @return mixed
     */
    public function getTimedBool( $timetolive = 720, $cache_name = 'timed-bool' ){
        $cache = \Appcaching::getGlobalCache($cache_name . $this->playid);

        if($cache){
            if($cache+$timetolive > time()){
                \Appcaching::setGlobalCache($cache_name . $this->playid,time(), $timetolive);
                return true;
            } else {
                return false;
            }
        } else {
            \Appcaching::setGlobalCache($cache_name . $this->playid,time(), $timetolive);
            return true;
        }
    }

    /* this is a special action you can call to flush routes */
    public function actionFlushroutes(){
        $this->model->flushActionRoutes();
        $this->no_output = true;
        return ['Blank',array()];
    }

}