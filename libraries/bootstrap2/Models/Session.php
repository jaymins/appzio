<?php

namespace Bootstrap\Models;

trait Session
{

    /**
     * Save route to session
     *
     * @param $route
     * @param bool $persist_route
     * @param bool $actionid
     */
    public function setRoute($route, $persist_route = true, $actionid = false)
    {

        $actionid = $actionid ? $actionid : $this->action_id;

        $persist = 'persist_route_' . $actionid;
        $current = 'current_route_' . $actionid;

        /* save the route to session */
        $this->sessionSet($current, $route);
        $this->sessionSet($persist, $persist_route);

    }

    /**
     * Save an array in session storage
     *
     * @param $array
     * @return bool
     */
    public function sessionSetArray($array)
    {
        if (isset($_REQUEST['cache_request']) AND $_REQUEST['cache_request'] == true) {
            return false;
        }

        if (is_array($array) AND !empty($array)) {
            foreach ($array as $key => $value) {
                $this->sessionSet($key, $value);
            }
        }

        return true;
    }

    /**
     * Save key value pair in session
     *
     * @param $key
     * @param $value
     * @param $force -- allows setting the value even if its a cache request from client
     * @return bool
     */
    public function sessionSet($key,$value,$force=false)
    {

        if (isset($_REQUEST['cache_request']) AND $_REQUEST['cache_request'] == true AND !$force) {
            return false;
        }

        /* if you delete session value + then set it again during the same call, this would
        make sure that the value doesn't get deleted */
        if(isset($this->session_storage['session-keys-to-delete'][$key])){
            unset($this->session_storage['session-keys-to-delete'][$key]);
        }

        $this->session_storage[$key] = $value;

        return true;
    }

    /**
     * Retrieve value from session by key
     *
     * @param $key
     * @return bool
     */
    public function sessionGet($key)
    {
        if (isset($this->session_storage[$key])) {
            return $this->session_storage[$key];
        } else {
            return false;
        }
    }

    /**
     * Unset a session variable
     *
     * @param $key
     * @return bool
     */
    public function sessionUnset($key)
    {
        if (isset($_REQUEST['cache_request']) AND $_REQUEST['cache_request'] == true) {
            return false;
        }

        if (isset($this->session_storage[$key])) {
            /* this is for API controller so that it doesn't overwrite old value to this */
            $this->session_storage['session-keys-to-delete'][$key] = true;
            unset($this->session_storage[$key]);
        }

        return true;
    }

}