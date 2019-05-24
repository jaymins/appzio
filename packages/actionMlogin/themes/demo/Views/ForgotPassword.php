<?php
namespace packages\actionMlogin\themes\demo\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMlogin\themes\demo\Models\Model as ArticleModel;

class ForgotPassword extends Login
{
    /* @var ArticleModel */
    public $model;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    public function tab2()
    {
        $this->layout = new \stdClass();

        $errors = $this->getData('errors', 'string');

        $this->setHeader();
        $this->renderForgotPwdForm($errors);
        $this->renderLostButtons();

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



    public function getHeaderImage($image)
    {
        return $this->getComponentImage($image, array(), array(
            'width' => '200',
            'margin' => '20 0 20 0'
        ));
    }

    public function renderHeader($text)
    {
        $this->layout->header[] = $this->getComponentRow(array(
            $this->getComponentColumn(array(), array(), array(
                'width' => '15%',
            )),
            $this->getComponentColumn(array(
                $this->getComponentText($text, array(), array(
                    'vertical-align' => 'middle',
                    'color' => '#ffffff'
                ))
            ), array(), array(
                'text-align' => 'center',
                'width' => '70%',
            )),
            $this->getComponentRow(array(), array(), array(
                'width' => '15%',
                'text-align' => 'right',
                'vertical-align' => 'middle',
            ))
        ), array(), array(
            'vertical-align' => 'middle',
            'padding' => '10 0 10 0',
            'height' => '200',
            'background-color' => '#77b981',
            'width' => 'auto'
        ));
    }

}