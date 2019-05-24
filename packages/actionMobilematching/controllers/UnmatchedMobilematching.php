<?php

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.packages.actionMobilematching.models.*');

class UnmatchedMobilematching extends MobilematchingController
{

    private $metas;
    private $users;
    private $curr_index;

    public function tab1()
    {

        $this->rewriteActionConfigField('background_color', '#f9fafb');

        $this->data = new stdClass();
        $this->setHeader( 1 );

        $this->users = json_decode($this->getVariable('unmatched_users'));

        $this->data->scroll[] = $this->getImage('header-shadow.png', array(
            'imgwidth' => '1440',
            'width' => '100%',
        ));

        if (empty($this->users)) {
            $this->renderNoUsersMessage('{#there_are_no_unmatched_users#}');
            return $this->data;
        }

        foreach ($this->users as $i => $user) {
            $this->curr_index = $i;

            $userId = $user[0];
            $timeOfUnmatch = $user[1];

            $variables = AeplayVariable::getArrayOfPlayvariables($userId);
            $this->renderUnmatchedUser($userId, $variables, $timeOfUnmatch);
        }

        return $this->data;
    }

    public function tab2()
    {

        $this->rewriteActionConfigField('background_color', '#f9fafb');

        $this->initMobileMatching();

        $this->metas = new MobilematchingmetaModel();
        $this->metas->current_playid = $this->playid;

        $this->handleExtrasPayments();

        $this->data = new stdClass();
        $this->setHeader( 2 );

        $this->users = json_decode($this->getVariable('unmatched_me'));

        $this->data->scroll[] = $this->getImage('header-shadow.png', array(
            'imgwidth' => '1440',
            'width' => '100%',
        ));

        if (empty($this->users)) {
            $this->renderNoUsersMessage('{#you_have_not_been_unmatched_yet#}');
            return $this->data;
        }

        $this->getAddonBanner();

        foreach ($this->users as $i => $user) {
            $this->curr_index = $i;

            $userId = $user[0];
            $timeOfUnmatch = $user[1];

            $variables = AeplayVariable::getArrayOfPlayvariables($userId);
            $variables['blur'] = 1;
            $this->renderUnmatchedUser($userId, $variables, $timeOfUnmatch);
        }

        return $this->data;
    }

    private function renderUnmatchedUser($playId, $variables, $timeOfUnmatch)
    {
        $time = time() - $timeOfUnmatch;

        $dtF = new \DateTime('@0');
        $dtT = new \DateTime("@$time");
        $days = (int)$dtF->diff($dtT)->d;

        if( $days > 1 ) {
            $daysSinceUnmatch = $days . ' days ago';
        } else if ($days == 1) {
            $daysSinceUnmatch = '1 day ago';
        } else {
            $daysSinceUnmatch = 'Today';
        }

        $onclick = new stdClass();
        $onclick->action = 'open-action';
        $onclick->id = $playId;
        $onclick->back_button = true;
        $onclick->sync_open = true;
        $onclick->action_config = $this->getActionidByPermaname('userinfo');

        $name = '';

        if ( isset($variables['real_name']) AND $variables['real_name'] ) {
            $name = $variables['real_name'];
        } else if ( isset($variables['name']) AND $variables['name'] ) {
            $name = $variables['name'];
        }

        $this->data->scroll[] = $this->getRow(array(
            $this->getImage($variables['profilepic'], array(
                'vertical-align' => 'middle',
                'text-align' => 'center',
                'margin' => '5 1 5 1',
                'width' => '60',
                'height' => '60',
                'priority' => 9,
                'border-radius' => '3',
                'blur' => isset($variables['blur']) ? $variables['blur'] : '0',
                'crop' => 'round',
            )),
            $this->getColumn(array(
                $this->getText($name, array(
                    'font-size' => '17'
                )),
                $this->getText($daysSinceUnmatch, array(
                    'color' => '#9b9b9b',
                    'font-size' => '12',
                ))
            ), array(
                'vertical-align' => 'middle',
                'margin' => '0 0 0 20'
            )),
            $this->getRow(array(
                $this->getText('')
            ), array(
                'width' => '20%',
                'floating' => 1,
                'float' => 'right',
                'padding' => '12 0 0 0'
            ))
        ), array(
            'padding' => '5 15 5 10',
            'onclick' => $onclick
        ));

        if ( ($this->curr_index + 1) < count($this->users) ) {
            $this->data->scroll[] = $this->getHairline('#f3f3f3');
        }

    }

    private function renderNoUsersMessage($text)
    {
        $this->data->scroll[] = $this->getText($text, array(
            'text-align' => 'center',
            'padding' => '10 10 10 10'
        ));
    }

    private function setHeader( $active )
    {

        $toggleSidemenu = new stdClass();
        $toggleSidemenu->action = 'open-sidemenu';

        $this->data->header[] = $this->getRow(array(
            $this->getImage('ic_menu_new.png', array(
                'width' => '20',
                'onclick' => $toggleSidemenu
            )),
            $this->getText('{#unmatched#}', array(
                'color' => '#ff6600',
                'width' => '90%',
                'text-align' => 'center',
            ))
        ), array(
            'background-color' => '#FFFFFF',
            'padding' => '10 20 10 20',
            'width' => '100%',
        ));

        $tabs = array(
            'tab1' => '{#unmatched_by_me#}',
            'tab2' => '{#unmatched_by_them#}'
        );

        $params = array(
            'active' => $active,
            'color' => $this->colors['top_bar_text_color'],
            'color_topbar' => $this->color_topbar,
            'color_topbar_hilite' => $this->colors['top_bar_text_color'],
            'btn_padding' => '12 10 12 10',
            'divider' => true,
        );

        $this->data->header[] = $this->getRow(array(
            $this->getTabs( $tabs, $params )
        ));

    }

    private function getAddonBanner() {

        if ( $this->metas->checkMeta('chat-with-blocked') ) {
            return false;
        }

        $this->registerProductDiv( 'chat-with-blocked', 'm_chat_with_blocked.png', '{#second_chance#}', 'Chat for up to 12 hours with your unmatched for next 30 days.', 'second_chance.001', 'second_chance.001' );

        // Action after purchase
        if ( isset($_REQUEST['purchase_completed']) AND $_REQUEST['purchase_completed'] == '1' ) {
            $this->mobilematchingmetaobj->play_id = $this->playid;
            $this->mobilematchingmetaobj->meta_key = 'chat-with-blocked';
            $this->mobilematchingmetaobj->meta_value = time();
            $this->mobilematchingmetaobj->meta_limit = 'time';
            $this->mobilematchingmetaobj->saveMeta();
        }

        $this->data->scroll[] = $this->getRow(array(
            $this->getImage('unmatched-addon-image.png'),
            $this->getText('{#save_your_chance_and_buy_addon_chat_even_if_you_are_unmatched#}', array(
                'color' => '#ffffff',
                'padding' => '0 0 0 20',
                'font-size' => '17',
            )),
        ), array(
            'background-color' => '#7ed321',
            'vertical-align' => 'middle',
            'padding' => '12 20 12 20',
            'margin' => '-10 0 0 0',
            'onclick' => $this->showProductDiv( 'chat-with-blocked' ),
        ));

        $this->data->scroll[] = $this->getSpacer(6, array(
            'background-color' => '#f9fafb',
        ));

        return true;
    }

    public function handleExtrasPayments() {

        if ( !isset($_REQUEST['purchase_product_id']) ) {
            return false;
        }

        $product_id = $_REQUEST['purchase_product_id'];
        $card_config = $this->metas->getCardByProductID( $product_id );

        if ( empty($card_config) ) {
            return false;
        }

        $this->metas->play_id = $this->playid;
        $this->metas->meta_key = $card_config['trigger'];
        $this->metas->meta_value = ( $card_config['measurement'] == 'time' ? time() : $card_config['amount'] );
        $this->metas->meta_limit = $card_config['measurement'];
        $this->metas->saveMeta();

        return true;
    }

}