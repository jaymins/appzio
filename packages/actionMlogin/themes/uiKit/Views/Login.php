<?php
namespace packages\actionMlogin\themes\uiKit\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMlogin\themes\uiKit\Controllers\Controller;
use packages\actionMlogin\themes\uiKit\Models\Model as ArticleModel;
use packages\actionMlogin\Components\Components as Components;

class Login extends BootstrapView
{

    /* @var Components */
    public $components;

    /* @var ArticleModel */
    public $model;
    private $change_tab;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    public function tab1()
    {
        $this->layout = new \stdClass();

        $finishLogin = $this->getData('finishLogin', 'num');

        if ($finishLogin) {
            $this->layout->scroll[] = $this->getComponentFullPageLoaderAnimated(['text' => '{#logging_in#}']);
            $this->layout->onload[] = $this->getOnclickListBranches();
            $this->layout->onload[] = $this->getOnclickCompleteAction();
            return $this->layout;
        }

        $errors = $this->getData('errors', 'string');

        $this->setHeader();
        $this->renderLoginForm($errors);
        $this->renderButtons();

/*        $layout = new \stdClass();
        $layout->bottom = 0;
        $layout->width = $this->screen_width;

        $this->layout->overlay[] = $this->getComponentText('Hello',[
            'layout' => $layout
        ],['border-color' => '#000000',
            'height' => '360','width' => '100%']);*/

        return $this->layout;
    }

    public function tab2(){
        $this->layout = new \stdClass();
        $changeTab = $this->getData('change_tab', 'mixed');

        if($changeTab){
            $this->change_tab = true;
            return self::tab1();
        }

        $errors = $this->getData('errors', 'string');

        $this->setHeader();
        $this->renderForgotPwdForm($errors);
        $this->renderLostButtons();

        return $this->layout;
    }

    public function renderForgotPwdForm($errors = '')
    {

        $row[] = $this->components->uiKitGeneralField('email-to-reset', '{#email_to_reset#}', 'icon-envelope.png',['input_type' => 'email','error' => $errors]);
        $row[] = $this->components->uiKitDivider();

        $this->layout->scroll[] = $this->getComponentColumn($row,[],['width' => '100%','margin' => '20 15 8 15']);

        /*        if(!empty($errors)){
                    $this->layout->scroll[] = $this->getComponentText($errors, array(), array(
                        'color' => '#000000'
                    ));
                }*/
    }

    public function renderLostButtons()
    {

        $btns[] = $this->getComponentText('{#reset_password#}', array(
            'id' => 'do-regular-login',
            'onclick' => $this->getOnclickSubmit('Forgot/sendcode/'),
        ), array(
            'background-color' => '#ffffff',
            'border-width' => '1',
            'font-size' => '14',
            'border-color' => '#9b9b9b',
            'border-radius' => '20',
            'width' => '70%',
            'height' => '40',
            'padding' => '10 30 10 30',
            'text-align' => 'center'
        ));

        $btns[] = $this->components->getComponentSpacer(10);

        $btns[] = $this->getComponentText('{#cancel#}', array(
            'onclick' => $this->getOnclickTab(1),
        ), array(
            'background-color' => $this->color_top_bar_color,
            'color' => $this->color_top_bar_text_color,
            'font-size' => '14',
            'border-radius' => '20',
            'width' => '70%',
            'height' => '40',
            'padding' => '10 30 10 30',
            'text-align' => 'center'
        ));


        $btns[] = $this->components->getComponentSpacer(10);

        $this->layout->footer[] = $this->getComponentColumn($btns,[],[
            'text-align' => 'center','width' => '100%']);

        $this->layout->footer[] = $this->uiKitTermsText(['actionid' => $this->model->getConfigParam('terms_action')]);

        $this->layout->footer[] = $this->components->getComponentSpacer(15);

        /*
        $this->layout->footer[] = $this->getComponentColumn(array(
            $this->getComponentText(strtoupper('{#reset_password#}'), array(
                'id' => 'send-code',
                'onclick' => $this->getOnclickSubmit('Forgot/sendCode/'),
            ), array(
                'background-color' => '#ffffff',
                'border-width' => '1',
                'border-color' => '#9b9b9b',
                'border-radius' => '20',
                'width' => '80%',
                'padding' => '10 30 10 30',
                'text-align' => 'center'
            ))
        ), array(
            'text-align' => 'center',
            'margin' => '50 0 0 0'
        ));
        $this->layout->footer[] = $this->components->getComponentSpacer(10);

        $this->layout->footer[] = $this->getComponentColumn(array(
            $this->getComponentText('{#sing_in#}?', array('id' => 'reset-password-form',
                'onclick' =>  $this->getOnclickOpenAction('newloginaction')
            ), array( 'text-align' => 'center', 'width' => '50%'
            ))
        ), array(
            'text-align' => 'center',
            'width' => '100%'
        ));
        $this->layout->footer[] = $this->components->getComponentSpacer(20);*/
    }


    public function setHeader(){

        $mode = $this->model->getConfigParam('header_mode');
        $logo_active = $this->model->getConfigParam('header_logo');
        $logo = $this->model->getConfigParam('actionimage1');
        $bg = $this->model->getConfigParam('actionimage2');
        $text = $this->model->getConfigParam('header_text');
        $bg_img = $this->getImageFileName($bg,['imgwidth' => '900','priority' => 1]);
        $gradient_top = $this->model->getConfigParam('gradient_top');
        $gradient_bottom = $this->model->getConfigParam('gradient_bottom');

        $valign = $logo_active ? 'bottom' : 'middle';

        $width = $this->screen_width;

        if($logo_active){
            $layout = new \stdClass();
            $layout->bottom = '140';
            $layout->height = '90';
            $layout->center = 0;
            $layout->width = $this->screen_width;
            $overlay_high[] = $this->getComponentImage($logo,['layout'=>$layout,'priority' => 1],['width' => $width]);
            $layout->bottom = '65';
            $overlay_low[] = $this->getComponentImage($logo,['layout'=>$layout,'priority' => 1],['width' => $width]);
        } else {
            $overlay = [];
        }

        switch($mode){
            case 'header_image';
                $height = $this->screen_height / 4;

                    $col[] = $this->getComponentText($text,[],[
                        'text-align' => 'center',
                        'font-size' => 28,
                        'margin' => '30 40 30 40',
                        'color' => $this->color_top_bar_text_color]);
                    $this->layout->scroll[] = $this->getComponentColumn($col,[
                        'overlay' => $overlay_low
                    ],[
                        'height' => $height,
                        'background-image' => $bg_img,
                        'background-size' => 'cover',
                        'vertical-align' => $valign]);

                $this->renderNavigation();
                break;

            case 'header_tall_image';
                $height = round($this->screen_height / 2.3,0);

                    $col[] = $this->getComponentText($text,[
                    ],[
                        'text-align' => 'center',
                        'font-size' => 28,
                        'margin' => '30 40 30 40',
                        'color' => $this->color_top_bar_text_color]);
                    $this->layout->scroll[] = $this->getComponentColumn($col,[
                        'overlay' => $overlay_high
                    ],[
                        'height' => $height,
                        'background-image' => $bg_img,
                        'background-size' => 'cover',
                        'vertical-align' => $valign]);

                    $this->renderNavigation();
                break;

            case 'header_gradient':
                $gradient_top = \Helper::NormalizeColor($gradient_top);
                $gradient_bottom = \Helper::NormalizeColor($gradient_bottom);

                $gradient_top = str_replace('#', '', $gradient_top);
                $gradient_bottom = str_replace('#', '', $gradient_bottom);

                $height = round($this->screen_height / 2.3,0);

                $col[] = $this->getComponentText($text,[],[
                    'text-align' => 'center',
                    'font-size' => 28,
                    'margin' => '30 40 30 40',
                    'color' => $this->color_top_bar_text_color]);
                $this->layout->scroll[] = $this->getComponentColumn($col,[
                    'overlay' => $overlay_high
                ],[
                    'height' => $height,
                    "background-linear-color"=> "0deg,$gradient_bottom 0%,$gradient_top 100%",
                    'vertical-align' => $valign]);

                $this->renderNavigation();
                break;

            case 'header_plain';

                $height = round($this->screen_height / 2.3,0);

                $col[] = $this->getComponentText($text,[
                ],[
                    'text-align' => 'center',
                    'font-size' => 28,
                    'margin' => '30 40 30 40',
                    'color' => $this->color_top_bar_text_color]);
                $this->layout->scroll[] = $this->getComponentColumn($col,[
                    'overlay' => $overlay_high
                ],[
                    'height' => '280',
                    'background-color' => $this->color_top_bar_color,
                    'vertical-align' => $valign]);
                $this->renderNavigation();
                break;

            case 'header_no':
                $height = $this->screen_height - 360;

                if($logo_active){
                    $col[] = $this->getComponentImage($logo,[],['margin' => '0 80 30 80']);
                }
                $col[] = $this->getComponentText($text,[],[
                    'text-align' => 'center','font-size' => 28,'margin' => '0 40 0 40']);

                $this->layout->scroll[] = $this->getComponentColumn($col, [], [
                    'height' => $height,'vertical-align' => 'middle'
                ]);



                break;
        }


    }

    public function renderNavigation()
    {
        $this->layout->scroll[] = $this->uiKitTabNavigation(array(
            array(
                'text' => strtoupper('{#sign_in#}'),
                'active' => true,
                'onclick' => $this->getOnclickOpenAction('login')
            ),
            array(
                'text' => strtoupper('{#sign_up#}'),
                'active' => false,
                'onclick' => $this->getOnclickOpenAction('register', false, array("sync_open" => 1))

            )
        ));
    }

    public function getFormTitle($title)
    {
        return $this->getComponentRow(array(
            $this->getComponentText($title, array(), array(
                'margin' => '10 0 10 10'
            ))
        ), array(), array(
            'width' => '100%'
        ));
    }

    public function renderLoginForm($errors = '')
    {

        $mode = $this->model->getConfigParam('header_mode');

        if ($mode == 'header_no') {
            $place = 'footer';
        } else {
            $place = 'scroll';
        }

        $mode = $this->model->getConfigParam('header_mode');

        $this->layout->$place[] = $this->getComponentSpacer(20);
        $col[] = $this->components->uiKitGeneralField('email', '{#email#}', 'icon-envelope.png');
        $col[] = $this->components->uiKitDivider();
        $col[] = $this->components->uiKitGeneralField('password', '{#password#}', 'icon-key.png');
        $col[] = $this->components->uiKitDivider();

        $this->layout->$place[] = $this->getComponentColumn($col, [], ['margin' => '0 15 0 15']);

        if ($mode == 'header_no') {

            $row[] = $this->getComponentText('{#register_a_new_account#}', array(
                'onclick' => $this->getOnclickOpenAction('register')
            ), array('text-align' => 'left',
                'font-size' => '13', 'padding' => '0 0 0 0', 'opacity' => '0.6'
            ));

            $row[] = $this->getComponentText('{#forgot_password#}?', array(
                'onclick' => $this->getOnclickTab(2)
            ), array(
                'text-align' => 'right',
                'font-size' => '13',
                'padding' => '0 0 0 0',
                'opacity' => '0.6',
                'floating' => 1,
                'float' => 'right'));

            $this->layout->$place[] = $this->getComponentRow($row, [], ['width' => '100%', 'margin' => '8 15 8 15']);
        } else {
            $this->layout->$place[] = $this->getComponentColumn(array(
                $this->getComponentText('{#forgot_password#}?', array(
                    'onclick' => $this->getOnclickTab(2)
                ), array('text-align' => 'right',
                    'width' => '100%', 'font-size' => '13', 'padding' => '8 15 0 0', 'opacity' => '0.6'

                ))
            ), array(
                'text-align' => 'center',
                'width' => '100%',
                'floating' => '1',
                'float' => 'right',
            ));
        }

        if (!empty($errors)) {
            $this->layout->$place[] = $this->getComponentText($errors, array(), array(
                'color' => '#e93f33',
                'text-align' => 'center'
            ));
        }

        if ($mode == 'header_no') {
            $this->layout->footer[] = $this->getComponentSpacer(20);
        }
    }

    public function renderButtons()
    {

        if($this->change_tab){
            $login[] = $this->getOnclickTab(1);
        }

        $login[] = $this->getOnclickSubmit('Controller/login/');

        $btns[] = $this->getComponentText('{#sign_in#}', array(
                'onclick' => $login,
            ), array(
                'background-color' => '#ffffff',
                'border-width' => '1',
                'font-size' => '14',
                'border-color' => '#9b9b9b',
                'border-radius' => '20',
                'width' => '70%',
                'height' => '40',
                'padding' => '10 30 10 30',
                'text-align' => 'center'
            ));

        $btns[] = $this->components->getComponentSpacer(10);
        $btns[] = $this->getFBLoginButton();
        $btns[] = $this->components->getComponentSpacer(10);

        $this->layout->footer[] = $this->getComponentColumn($btns,[],[
            'text-align' => 'center','width' => '100%']);

        $this->layout->footer[] = $this->uiKitTermsText(['actionid' => $this->model->getConfigParam('terms_action')]);

        $this->layout->footer[] = $this->components->getComponentSpacer(15);
    }

    public function getHeaderImage($image)
    {
        return $this->getComponentImage($image, array(), array(
            'width' => '200',
            'margin' => '20 0 20 0'
        ));
    }

    public function getFBLoginButton()
    {

        $onclick[] = $this->getOnclickFacebookLogin([
            'id' => 'do-fb-login',
            'sync_open' => 1,
            'read_permissions' => ['email','public_profile']]);

        //$onclick[] = $this->getOnclickSubmit('Controller/fblogin/');

        return
            $this->getComponentRow(array(
                $this->getComponentImage('fb-icon-new.png', array(), array(
                    'width' => '35',
                    'padding' => '0 0 5 10'
                )),
                $this->getComponentText('{#sign_in_with_facebook#}', array(
                    'onclick' => $onclick), array(
                    'color' => '#ffffff',
                    'font-size' => '14',
                    'text-align' => 'center',
                    'padding' => '0 30 0 30',
                ))
            ), array(), array(
                'background-color' => '#3c5a99',
                'border-width' => '1',
                'border-color' => '#3c5a99',
                'border-radius' => '20',
                'width' => '70%',
                'padding' => '5 0 5 0',
                'text-align' => 'center'
            ));
    }

    public function getInstLoginButton()
    {
        return $this->getComponentColumn(array(
            $this->getComponentRow(array(
                $this->getComponentText(strtoupper('{#sign_in_with_instagram#}'), array(
                    'id' => 'do-inst-login',
                    'onclick' => $this->getOnclickSubmit('Controller/InstLogin/'),
                ), array(
                    'color' => '#ffffff',
                    'font-size' => '14',
                    'text-align' => 'center',
                    'padding' => '0 30 0 30',

                ))
            ), array(), array(
                'background-color' => '#a438a5',
                'border-width' => '1',
                'border-color' => '#a438a5',
                'border-radius' => '20',
                'width' => '80%',
                'padding' => '5 0 5 0',
                'text-align' => 'center'
            ))

        ), array(
            'text-align' => 'center',
            'margin' => '50 0 0 0'
        ));
    }

}