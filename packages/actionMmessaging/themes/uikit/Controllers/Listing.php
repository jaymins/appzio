<?php

/**
 * Themes model extends actions main model and further the BootstrapModel
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMmessaging\themes\uikit\Controllers;

use packages\actionMmessaging\themes\uikit\Views\Listing as ArticleView;
use packages\actionMmessaging\themes\uikit\Models\Model as ArticleModel;

class Listing extends \packages\actionMmessaging\Controllers\Chat {

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj){
        parent::__construct($obj);
    }

    public function actionDefault(){

        $this->model->query_key = 'two-way-matches';

        $data = [];
        $data['matches'] = $this->getMatchesFormatted();

        return ['Listing', $data];
    }

    private function getMatchesFormatted() {

        $matches = $this->model->getMatches();

        if ( empty($matches) ) {
            return false;
        }

        $output = [];

        $fields = [
            'name', 'real_name', 'email', 'age', 'profile_comment', 'profilepic', 'country', 'city', 'user_play_id',
            'chat_data'
        ];

        foreach ($matches as $match_data) {

            $data = [];

            foreach ($match_data as $key => $value) {

                if ( !in_array($key, $fields) ) {
                    continue;
                }

                $data[$key] = $value;
            }

            $output[] = $data;
        }

        return $output;
    }

    /*
     * To do: We need to put this method into use
     */
    private function truncate_words( $str, $words_count = 10 ) {

        $pieces = explode(' ', $str);
        $output = array();

        foreach ($pieces as $i => $word) {
            if ( $i < $words_count ) {
                $output[] = $word;
            }
        }

        if ( empty($output) ) {
            return $str;
        }

        return implode(' ', $output);
    }

}