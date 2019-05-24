<?php

class MobileloginDeseeController extends MobileloginController
{
    /**
     * Make authentication checks before rendering the login page
     * @return bool
     */
    public function checkLoginBeforeRendering()
    {
        $loggedin = $this->getSavedVariable('logged_in');

        if ($loggedin) {
            $this->logoutForm();
            return true;
        }

        if ($this->getConfigParam('only_logout')) {
            $this->data->scroll[] = $this->getFullPageLoader();
            return true;
        }
    }

    /**
     * Get header image name
     * @return bool|string
     */
    public function getHeaderImage()
    {
        $imageName = 'desee-login-logo.png';
        $imageFile = '';

        if ($this->getConfigParam('actionimage1')) {
            $imageFile = $this->getConfigParam('actionimage1');
        } elseif ($this->getImageFileName($imageName)) {
            $imageFile = $imageName;
        }

        return $imageFile;
    }

    /**
     * Get login view padding height
     * @return int
     */
    public function getHeight()
    {
        $height = 45;

        if ($this->aspect_ratio > 0.57) {
            $height = 20;
        }

        return $height;
    }

    public function finishLogin($skipreg = false, $fblogin = false, $line = false, $tokenlogin = false)
    {
        $this->loggingInHeader();
        $this->addToDebug('Reg line:' . $line);

        if ($skipreg) {
            $this->addToDebug('closing shop:' . $line);

            AeplayBranch::closeBranch($this->getConfigParam('register_branch'), $this->playid);
            AeplayBranch::closeBranch($this->getConfigParam('login_branch'), $this->playid);
            AeplayBranch::activateBranch($this->getConfigParam('logout_branch'), $this->playid);
            $this->saveVariable('logged_in', 1);
            // Store the user's last login
            $this->saveVariable('last_login', time());

            if ($fblogin OR $tokenlogin) {
                $this->deleteVariable('fb_universal_login');
            }
        }

        $complete = new StdClass();
        $complete->action = 'complete-action';
        $this->data->onload[] = $complete;

        $complete = new StdClass();
        $complete->action = 'list-branches';
        $this->data->onload[] = $complete;

        if ($this->getSavedVariable('oauth_in_progress')) {
            $string = explode('_', $this->getSavedVariable('oauth_in_progress'));
            if (isset($string[1])) {
                $complete = new StdClass();
                $complete->action = 'open-action';
                $complete->action_config = $string[1];
                $this->data->onload[] = $complete;
            }
        }
    }
}