<?php

class matchswappMobilemissingdetailsSubController extends MobilemissingdetailsController {

    public function tab1(){

        $this->data = new StdClass();

        if(isset($this->varcontent['name'])){
            $name = $this->varcontent['name'];
            $first_name = explode( ' ', $name )[0];
            $text = '{#hey#} '. $first_name .', {#missing_fields_description#}';
        } else {
            $first_name = false;
            $text = '{#hey#}, {#missing_fields_description#}';
        }

        $this->data->scroll[] = $this->getText( $text, array( 'style' => 'missing-details-title' ) );

        $this->getMissingFields();

        if ( $this->show_error ) {
            $this->data->scroll[] = $this->getText( '{#missed_fields#}.', array('style' => 'details-error') );
        }

        if ( isset($this->menuid) AND $this->menuid == 'submit-data' ) {
            $text = ( $this->show_error ? '{#submit_details#}' : '{#thank_you#}!' );
            $this->data->footer[] = $this->getTextbutton($text,array('id' => 'submit-data', 'style' => 'general_button_style_red'));
        } else {
            $this->data->footer[] = $this->getTextbutton('{#submit_details#}',array('id' => 'submit-data', 'style' => 'general_button_style_red'));
        }
        
        return $this->data;
    }

}