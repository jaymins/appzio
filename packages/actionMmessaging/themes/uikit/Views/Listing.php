<?php

namespace packages\actionMmessaging\themes\uikit\Views;

use packages\actionMmessaging\Views\Listing as BootstrapView;
use packages\actionMmessaging\themes\uikit\Components\Components;

class Listing extends BootstrapView {

    public $layout;
    public $title;

    /* @var Components */
    public $components;

    public $tab;

    public function tab1(){
        $this->layout = new \stdClass();
        
        $matches = $this->getData('matches', 'mixed');

        if ( empty($matches) ) {
            $this->layout->scroll[] = $this->getComponentText('{#no_matches_yet#}', [], [
                'text-align' => 'center',
                'padding' => '20 15 20 15',
            ]);

            return $this->layout;
        }

        foreach ($matches as $match) {

            $params = [];

            if ( isset($match['chat_data']['context_key']) ) {
                $params['onclick'] = $this->getOnclickOpenAction('chatb2', false, [
                    'id' => $match['chat_data']['context_key'],
                    'back_button' => 1,
                    'sync_open' => 1,
                    'sync_close' => 1,
                    'viewport' => 'bottom',
                ]);
            }

            $this->layout->scroll[] = $this->uiKitMatchItem($match, $params);
            $this->layout->scroll[] = $this->uiKitDivider();
        }

        return $this->layout;
    }

}