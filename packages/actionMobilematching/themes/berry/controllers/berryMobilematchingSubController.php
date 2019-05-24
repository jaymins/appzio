<?php

class berryMobilematchingSubController extends MobilematchingController
{
    /**
     * Renders matches
     *
     * @param bool $skipfirst
     * @return bool
     */
    public function showMatches($skipfirst = false)
    {
        $search_dist = $this->getSavedVariable('distance') ? $this->getSavedVariable('distance') : 10000;
        $query_by_gender = $this->queryByGender();
        $users = $this->mobilematchingobj->getUsersNearby($search_dist, 'exclude', $query_by_gender, false, false, $this->units);

        $partyModeOn = $this->getVariable('party_mode');

        if ($partyModeOn) {
            // TODO: use a better identifier for being in party mode
            $this->data->scroll[] = $this->getText('{#party_mode#}', array(
                'color' => '#FFFFFF',
                'text-align' => 'center',
            ));
        }

        if ($this->mobilematchingobj->debug) {
            $this->addToDebug($this->mobilematchingobj->debug);
        }

        if (empty($users)) {
            $this->notFound();
            return false;
        } else {
            $swipestack = $this->buildSwipeStack($users, $skipfirst);
        }

        if (empty($swipestack)) {
            $this->notFound();
        } else {
            $this->data->scroll[] = $this->swipe($swipestack);
            $this->setFooter();
        }
    }

    public function matches()
    {
        if ($this->menuid == 'toggle-party-mode') {
            $mode = $this->getVariable('party_mode');
            $this->saveVariable('party_mode', !$mode);
        }

        if (strstr($this->menuid, 'no_')) {
            $id = str_replace('no_', '', $this->menuid);
            $this->skip($id);
            return true;
        }

        if (strstr($this->menuid, 'yes_')) {
            $id = str_replace('yes_', '', $this->menuid);
            $this->doMatch($id);
            return true;
        }

        $this->showMatches();

    }

    public function setFooterButtons()
    {
        $mode = $this->getPartyMode();

        $modeText = $mode == true ? '{#on#}' : '{#off#}';

        $onclick = new stdClass();
        $onclick->action = 'submit-form-content';
        $onclick->id = 'toggle-party-mode';
        $onclick->sync_open = 1;

        $this->data->footer[] = $this->getTextbutton('{#party_mode_is#} ' . strtoupper($modeText), array(
            'id' => 'id',
            'onclick' => $onclick
        ));
    }

    protected function getPartyMode()
    {
        $mode = $this->getVariable('party_mode');
        return $mode;
    }

    public function getAMatch($users)
    {
        // For performance reasons we only query 40 users at a time
        if (count($users) > 40) {
            $users = array_splice($users, 0, 40);
        }

        $pointer = false;

        foreach ($users as $i => $user) {
            if (!isset($user['play_id'])) {
                continue;
            }

            $vars = AeplayVariable::getArrayOfPlayvariables($user['play_id']);

            if (isset($vars['profilepic'])) {
                $vars['play_id'] = $user['play_id'];
                $vars['distance'] = $user['distance'];

                if (isset($user['party_mode'])) {
                    $vars['party_mode'] = $user['party_mode'];
                } else {
                    $vars['party_mode'] = false;
                }

                $outvars[] = $vars;
                if (!$pointer) {
                    $this->mobilematchingobj->setPointer($user['play_id']);
                    $pointer = true;
                }
            } else {
                $this->mobilematchingobj->initMatching($user['play_id']);
                $this->mobilematchingobj->skipMatch();
            }
        }

        if (!empty($outvars)) {
            return $outvars;
        }

        $this->notFound();
        return false;
    }

    public function filter($one)
    {
        if ($this->filterByPartyMode($one) == false) {
            $this->addToDebug('Filtered by party mode');
            return false;
        }

        if ($this->filterByDistance($one) == false) {
            $this->addToDebug('Filtered by distance');
            return false;
        }

        if ($this->filterByAge($one) == false) {
            $this->addToDebug('Filtered by age');
            return false;
        }

        if ($this->filterByReligion($one) == false) {
            $this->addToDebug('Filtered by religion');
            return false;
        }

        if ($this->filterBySex($one) == false) {
            $this->addToDebug('Filtered by sex ' . $this->getSavedVariable('gender'));
            return false;
        }

        if ($this->filterByHcp($one) == false) {
            $this->addToDebug('Filtered by hcp');
            return false;
        }

        if ($this->filterByAvailability($one) == false) {
            $this->addToDebug('Filtered by availability');
            return false;
        }

        if (isset($one['hide_user']) AND $one['hide_user'] == '1') {
            return false;
        }

        return true;
    }

    protected function filterByPartyMode($one)
    {
        $partyMode = $this->getPartyMode();

        if ($partyMode != $one['party_mode']) {
            return false;
        }

        return true;
    }
}