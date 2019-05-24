<?php

namespace packages\actionMnexudus\Views;
use Bootstrap\Views\BootstrapView;
use packages\actionMnexudus\Components\Components;

class Login extends BootstrapView {

    /* @var Components */
    public $components;
    public $theme;

    public function __construct($obj){
        parent::__construct($obj);
    }

    public function tab1(){
        $this->layout = new \stdClass();
        $this->loginHeader();
        $this->loginForm();
        return $this->layout;
    }


    public function tab2(){
        $this->layout = new \stdClass();
        $this->lostpwForm();
        return $this->layout;
    }


    public function lostpwForm(){

        $reset = $this->getData('password_reset', 'bool');


        if($reset){
            $this->passHeader(false);

            $this->layout->scroll[] = $this->getComponentSpacer(40);
            $this->layout->scroll[] = $this->getComponentText('{#please_check_your_email#}',[
                'style' => 'nexudus_uikit_loginheader'
            ]);
            $this->layout->scroll[] = $this->getComponentSpacer(30);

            $this->layout->scroll[] = $this->uiKitButtonHollow('{#back#}',[
                'onclick' => $this->getOnclickTab(1)
            ],[
                'color' => $this->color_top_bar_text_color,
                'border-color' => $this->color_top_bar_text_color,
            ]);

        } else {
            $this->passHeader();

            $col[] = $this->components->uiKitGeneralField('email', '{#email#}', 'icon-nexudus-envelope.png',['divider' => true]);
            $this->layout->scroll[] = $this->getComponentColumn($col,[],['margin' => '0 15 0 15']);


            $this->layout->scroll[] = $this->getComponentSpacer(30);

            $this->layout->scroll[] = $this->uiKitButtonFilled('{#reset#}',[
                'onclick' => $this->getOnclickSubmit('login/resetpass/reset')
            ],[
                'color' => $this->color_top_bar_text_color,
                // 'border-color' => $this->color_top_bar_text_color,
            ]);

            $this->layout->scroll[] = $this->getComponentSpacer(15);

            $this->layout->scroll[] = $this->uiKitButtonHollow('{#cancel#}',[
                'onclick' => $this->getOnclickTab(1)
            ],[
                'color' => $this->color_top_bar_text_color,
                'border-color' => $this->color_top_bar_text_color,
            ]);

            $this->layout->scroll[] = $this->getComponentSpacer(15);

        }


    }

    public function passHeader($info=true){
        $top_image = $this->model->getConfigParam('actionimage1');
        $second_image = $this->model->getConfigParam('actionimage2');

        if($top_image){
            $col[] = $this->getComponentImage($top_image,[],['width' => '120','margin' => '30 0 0 0']);
            $this->layout->scroll[] = $this->getComponentColumn($col,[],['text-align' => 'center']);
            unset($col);
        }

        if($info){
            $this->layout->scroll[] = $this->getComponentColumn([
                $this->getComponentText('{#please_input_your_email#} {#if_we_find_your_email_a_password_reset_link#} {#will_be_sent_to_that_email#}',['style' => 'nexudus_uikit_loginheader'])
            ],[],['margin' => '10 40 10 40']);
        }


    }


    public function loginHeader(){
        $top_image = $this->model->getConfigParam('actionimage1');
        $second_image = $this->model->getConfigParam('actionimage2');

        if($top_image){
            $col[] = $this->getComponentImage($top_image,[],['width' => '120','margin' => '30 0 0 0']);
            $this->layout->scroll[] = $this->getComponentColumn($col,[],['text-align' => 'center']);
            unset($col);
        }

        $richtext[] = $this->getComponentText('Book ',['style' => 'nexudus_uikit_loginheader']);
        $richtext[] = $this->getComponentText('silent work space',['style' => 'nexudus_uikit_loginheader_bold']);
        $richtext[] = $this->getComponentText(' on ',['style' => 'nexudus_uikit_loginheader']);
        $richtext[] = $this->getComponentText('pay per use',['style' => 'nexudus_uikit_loginheader_bold']);
        $richtext[] = $this->getComponentText(' basis, from as little as 15 minutes.',['style' => 'nexudus_uikit_loginheader']);

        $this->layout->scroll[] = $this->getComponentRichText($richtext,[],[
            'text-align' => 'center','margin' => '20 40 20 40']);

        unset($richtext);

        $richtext[] = $this->getComponentText('Simply select your booth and use the app to book, pay and unlock',['style' => 'nexudus_uikit_loginheader']);

        $this->layout->scroll[] = $this->getComponentRichText($richtext,[],[
            'text-align' => 'center','margin' => '0 40 20 40']);


        if($second_image){
            $col[] = $this->getComponentImage($second_image,[],['width' => $this->screen_width-80]);
            $this->layout->scroll[] = $this->getComponentColumn($col,[],['text-align' => 'center','margin' => '20 40 20 40']);
        }
    }

    public function loginForm(){

        $col[] = $this->components->uiKitGeneralField('email', '{#email#}', 'icon-nexudus-envelope.png',['divider' => true]);
        $col[] = $this->components->uiKitGeneralField('password', '{#password#}', 'icon-nexudus-lock.png',['divider' => true]);

        $this->layout->scroll[] = $this->getComponentColumn($col,[],['margin' => '0 15 0 15']);

       if(isset($this->model->validation_errors['password'])){
            $this->layout->scroll[] = $this->getComponentSpacer(10);
            $this->layout->scroll[] = $this->getComponentText('{#click_here_to_reset_your_password#}',[
                'onclick' => $this->getOnclickTab(2),
                'style' => 'nexudus_uikit_loginheader'
            ]);
        }

        $this->layout->footer[] = $this->getComponentSpacer(15);
        
        $this->layout->footer[] = $this->uiKitButtonHollow('{#login#}',[
            'onclick' => $this->getOnclickSubmit('dologin')
        ],[
            'color' => $this->color_top_bar_text_color,
            'border-color' => $this->color_top_bar_text_color,
        ]);

        $this->layout->footer[] = $this->getComponentSpacer(30);
        $this->layout->scroll[] = $this->getComponentSpacer(15);

        $richtext[] = $this->getComponentText('{#dont_have_an_account_yet#}? ',['style' => 'nexudus_uikit_loginheader']);
        $richtext[] = $this->getComponentText('{#sign_up#}.',['style' => 'nexudus_uikit_loginheader_bold']);

        $this->layout->scroll[] = $this->getComponentRichText($richtext,[
            'onclick' => $this->getOnclickOpenAction('register')
        ],['text-align' => 'center']);
    }

    public function getDivs(){
        $divs = new \stdClass();
        //$divs->countries = $this->components->getDivPhoneNumbers();
        return $divs;
    }



}
