<?php

class zipcodesMobilehelpers extends MobilehelpersController
{
    public $data;

    public function tab1() {
        $this->data = new stdClass();

        $this->getCountries();

        return $this->data;
    }

	public function getCountries() {
        $value = '';
        if ( isset($this->submitvariables['searchterm']) AND !empty($this->submitvariables['searchterm']) ) {
            $value = $this->submitvariables['searchterm'];
        }

        $countries = $this->countriesMap();

        $suggestions_module = new ArticleSuggestedTerms();
        $suggestions_module->suggestions = array_values( $countries );

        $args = array(
            'style' => 'search-filter',
            'hint' => '{#search_countries#}',
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
            $this->displayAllCountries();
        }

        $this->data->scroll[] = $this->getLoader('Loading',array('color' => '#000000','visibility' => 'onloading'));

        if ( $this->menuid == 'searchbox' ) {
            if(isset($this->submitvariables['searchterm']) AND strlen($this->submitvariables['searchterm']) > 0){
                $searchterm = $this->submitvariables['searchterm'];
                $wordlist = $suggestions_module->getSuggestions( $searchterm, true );

                if ( !empty($wordlist) ) {
                    foreach($wordlist as $word){
                    	$code = array_search($word, $countries);
                        $this->data->scroll[] = $this->getText($word . ' (' . $code . ')' , array(
                            'style' => 'helper-search-result',
                            'onclick' => $this->closeAndSelect( $code )
                        ));
                        $this->data->scroll[] = $this->getText('', array( 'style' => 'row-divider' ));
                    }
                } else {
                    $this->data->scroll[] = $this->getText( 'No results found for: ' . $searchterm, array(
                        'style' => 'helper-search-result',
                        'onclick' => $this->closePopup()
                    ));
                }

            }
        }

        $this->data->scroll[] = $this->getText('Close popup', array( 'style' => 'helper-close-button', 'onclick' => $this->closePopup() ));
    }

    public function displayAllCountries() {

    	$countries = $this->countriesMap();
    	$count = 0;

        foreach ($countries as $code => $country) {
        	$count++;
        	$this->data->scroll[] = $this->getText($country . ' (' . $code . ')' , array(
                'style' => ( $count % 2 == 0 ? 'helper-search-result-even' : 'helper-search-result' ),
                'onclick' => $this->closeAndSelect( $code )
            ));
            $this->data->scroll[] = $this->getText('', array( 'style' => 'row-divider' ));
        }

    }

    public function closeAndSelect( $value ) {
        $onclick = new StdClass();
        $onclick->action = 'close-popup';
        $onclick->keep_user_data = 1;

        $update_params = array(
            $this->getVariableId('country_code') => $value
        );
        $onclick->set_variables_data = (object)$update_params;

        return $onclick;
    }

}