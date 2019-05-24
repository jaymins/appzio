<?php

namespace packages\actionMregister\themes\cityapp\Models;

use packages\actionMregister\Models\Model as BootstrapModel;

class Model extends BootstrapModel
{

    /* save to variables */
    public function savePage1()
    {
        $vars['password'] = sha1(strtolower(trim($this->password)));
        $vars['email'] = $this->email;
        $vars['username'] = $this->username;
        $vars['real_name'] = $this->username;
        $this->saveNamedVariables($vars);
    }

    /* adds required variables and closes the login */
    public function closeLogin($dologin = true)
    {

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

    public function saveAddressData($address_data)
    {

        if (empty($address_data)) {
            return false;
        }

        $data = json_decode($address_data, true);

        $needed_vars = array(
            'name' => 'address',
            'lat' => 'lat',
            'lon' => 'lon'
        );

        foreach ($needed_vars as $api_var => $local_var) {
            if (!isset($data[$api_var]) OR empty($data[$api_var])) {
                continue;
            }

            $this->saveVariable($local_var, $data[$api_var]);
        }

        return true;
    }

}