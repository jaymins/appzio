<?php
namespace packages\actionMlogin\themes\demo\Views;

use packages\actionMlogin\themes\demo\Models\Model as ArticleModel;

class Resetpassword extends Login
{
    /* @var ArticleModel */
    public $model;

/*    public function __construct($obj)
    {
        parent::__construct($obj);
    }*/

    public function tab1(){
        $this->layout = new \stdClass();
        return $this->layout;
    }

    public function tab2()
    {
        $this->layout = new \stdClass();

        $errors = $this->getData('errors', 'string');

        $this->setHeader();
        $this->renderResetPwdForm($errors);
        $this->renderButtons();

        return $this->layout;
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

    public function renderResetPwdForm($errors = '')
    {
        $form[] = $this->components->demoGeneralField('temp_code', '{#code#}', 'icon-envelope.png');
        $form[] = $this->components->demoDivider();

        $form[] = $this->components->demoGeneralField('password', '{#new_password#}', 'icon-key.png');
        $form[] = $this->components->demoDivider();

        $form[] = $this->components->demoGeneralField('confirm_password', '{#confirm_password#}', 'icon-key.png');
        $form[] = $this->components->demoDivider();

        $this->layout->scroll[] = $this->getComponentColumn($form,[],['margin' => '15 15 15 15']);
    }

    public function renderButtons()
    {

        $btns[] = $this->getComponentText('{#reset_password#}', array(
            'onclick' => $this->getOnclickSubmit('Forgot/resetPassword/'),
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

        $btns[] = $this->components->getComponentSpacer(15);

        $this->layout->footer[] = $this->getComponentColumn($btns,[],['text-align' => 'center']);

    }

}