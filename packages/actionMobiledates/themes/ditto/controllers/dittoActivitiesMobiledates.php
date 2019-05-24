<?php

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.article.controllers.*');

Yii::import('application.modules.aelogic.packages.actionMobiledates.models.*');

class dittoActivitiesMobiledates extends dittoMobiledatesSubController {

    public function tab1(){
        $this->data = new stdClass();

        $this->getActivitySubView();

        return $this->data;
    }

    public function getActivitySubView() {
        $value = '';
        if ( isset($this->submitvariables['searchterm']) AND !empty($this->submitvariables['searchterm']) ) {
            $value = $this->submitvariables['searchterm'];
        }

        $suggestions_module = new ArticleSuggestedTerms();
        $suggestions_module->suggestions = $this->getSuggestionsList();

        $args = array(
            'style' => 'search-filter',
            'hint' => '{#free_text_search#}',
            'submit_menu_id' => 'searchbox',
            'variable' => 'searchterm',
            //'suggestions' => MobileexampleAccessor::getInitialWordList(10),
            'id' => 'something',
            'suggestions_style_row' => 'example_list_row',
            'suggestions_text_style' => 'example_list_text',
            'submit_on_entry' => 1,
            'activation' => 'keep-open',
        );

        $this->data->header[] = $this->getRow(array(
            $this->getFieldtext($value, $args)
        ), array( 'background-color' => $this->color_topbar));

        // Sponsored Activities would be shown if present
        if ( !$value ) {
            $this->displaySponsoredActivities();
        }

        $this->data->scroll[] = $this->getLoader('Loading',array('color' => '#000000','visibility' => 'onloading'));

        if ( $this->menuid == 'searchbox' ) {
            if(isset($this->submitvariables['searchterm']) AND strlen($this->submitvariables['searchterm']) > 0){
                $searchterm = $this->submitvariables['searchterm'];
                $wordlist = $suggestions_module->getSuggestions( $searchterm, true );

                if ( !empty($wordlist) ) {
                    foreach($wordlist as $word){
                        $this->data->scroll[] = $this->getText($word, array(
                            'style' => 'search-result',
                            'onclick' => $this->closeActivityPopup( $word )
                        ));
                        $this->data->scroll[] = $this->getText('', array( 'style' => 'row-divider' ));
                    }
                } else {
                    $this->data->scroll[] = $this->getText($searchterm, array(
                        'style' => 'search-result',
                        'onclick' => $this->closeActivityPopup( $searchterm )
                    ));
                }

            }
        }

        $onclick = new StdClass();
        $onclick->action = 'close-popup';
        $onclick->keep_user_data = 1;
        $this->data->footer[] = $this->getText('Close popup', array( 'style' => 'date-button', 'onclick' => $onclick ));
    }

    public function displaySponsoredActivities() {

        $activities = $this->getConfigParam( 'dates_sponsored_locations_list' );

        if ( empty($activities) ) {
            return false;
        }

        $activities = explode(PHP_EOL, $activities);

        foreach ($activities as $i => $activity) {

            $args[] = $this->getText($activity, array( 'style' => 'text-location-name-bold' ));
            $this->data->scroll[] = $this->getRow($args, array(
                'style' => 'location-info-row',
                'onclick' => $this->closeActivityPopup( $activity )
            ));
            $this->data->scroll[] = $this->getText('', array( 'style' => 'row-divider' ));

            unset($args);
        }

    }

    public function getSuggestionsList() {
        $suggestions = $this->getConfigParam( 'dates_locations_list' );

        if ( empty($suggestions) ) {
            return array();
        }
        
        return explode(';', $suggestions);
    }

    public function closeActivityPopup( $value ) {
        $onclick = new StdClass();
        $onclick->action = 'close-popup';
        $onclick->keep_user_data = 1;

        $update_params = array(
            $this->getVariableId('activity_idea') => $value
        );
        $onclick->set_variables_data = (object)$update_params;

        return $onclick;
    }

}