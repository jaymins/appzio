<?php

namespace packages\actionMsubscription\themes\pttrader\Views;

use packages\actionMsubscription\Views\View as BootstrapView;

class View extends BootstrapView
{

    /* @var \packages\actionMsubscription\Components\Components */
    public $components;
    public $theme;

    private $subscriptions;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    /* view will always need to have a function called tab1 */
    public function tab1()
    {
        $this->layout = new \stdClass();

        $this->subscriptions = $this->getData('subscriptions', 'array');
        $this->layout->header[] = $this->uiKitTopbarWithButtons([
            'leftSection' => [
                'image' => 'icon-back.png',
                'onclick' => $this->getOnclickOpenAction('home', false, [
                    'sync_open' => 1,
                ]),
            ],
            'centerSection' => [
                'title' => '{#subscription#}',
                'onclick' => '',
            ],
        ], [
            'background-color' => '#890e4f'
        ]);
        if (!empty($this->subscriptions)) {
            $this->layout->scroll[] = $this->getComponentText('{#subscription_already_purchased#}', [], [
                'font-size' => '19',
                'text-align' => 'center',
                'padding' => '15 15 25 15',
            ]);
        }

        $this->getMonthlyPaymentBlock();

        $this->getYearlySubscriptionBlock();

        $this->layout->scroll[] = $this->getComponentText('{#terms_and_conditions#}', [
            'style' => 'subscription_link',
            'onclick' => $this->getOnclickOpenAction('terms', false, [
                "sync_open" => 1,
                'back_button' => 1
            ])
        ]);

        return $this->layout;
    }

    private function getMonthlyPaymentBlock()
    {

        $priceMonthly = $this->getData('monthly_subscription_price', 'string');
        $iosMonthly = $this->getData('monthly_subscription_code_ios', 'string');
        $androidMonthly = $this->getData('monthly_subscription_code_android', 'string');

        if ($this->subscriptions) {
            
            if (isset($this->subscriptions['monthly_subscription'])) {
                $color = '#890e4f';
            } else {
                $color = '#B2B4B3';
            }

            $this->layout->scroll[] = $this->getComponentRow([
                $this->getComponentText('{#buy_monthly_subscription#} - $' . $priceMonthly, [
                    'uppercase' => true,
                ], [
                    'parent_style' => 'pt-button',
                    'background-color' => $color,
                    'font-size' => '14'
                ])
            ], [
                'style' => 'pt-button-container'
            ]);

            return true;
        }

        $this->layout->scroll[] = $this->getComponentText('{#explanation_monthly#}', [], [
            'text-align' => 'center',
            'padding' => '15 15 15 15',
        ]);

        $this->layout->scroll[] = $this->getComponentRow([
            $this->getComponentText('{#buy_monthly_subscription#} - $' . $priceMonthly, [
                'uppercase' => true,
            ], [
                'parent_style' => 'pt-button',
                'font-size' => '14'
            ])
        ], [
            'onclick' => $this->getOnclickPurchase($iosMonthly, $androidMonthly, [
                'id' => 'bought_monthly'
            ]),
            'style' => 'pt-button-container'
        ]);

        return true;
    }

    private function getYearlySubscriptionBlock()
    {

        $priceAnnual = $this->getData('annual_subscription_price', 'string');
        $iosAnnual = $this->getData('annual_subscription_code_ios', 'string');
        $androidAnnual = $this->getData('annual_subscription_code_android', 'string');

        if ($this->subscriptions) {

            if (isset($this->subscriptions['yearly_subscription'])) {
                $color = '#890e4f';
            } else {
                $color = '#B2B4B3';
            }

            $this->layout->scroll[] = $this->getComponentRow([
                $this->getComponentText('{#buy_yearly_subscription#} - $' . $priceAnnual, [
                    'uppercase' => true,
                ], [
                    'parent_style' => 'pt-button',
                    'background-color' => $color,
                    'font-size' => '14'
                ])
            ], [
                'style' => 'pt-button-container'
            ]);

            $this->getRestoreButton();

        } else {

            $this->layout->scroll[] = $this->getComponentText('{#explanation_yearly#}', [], [
                'text-align' => 'center',
                'padding' => '15 15 15 15',
            ]);

            $this->layout->scroll[] = $this->getComponentRow([
                $this->getComponentText('{#buy_yearly_subscription#} - $' . $priceAnnual, [
                    'uppercase' => true,
                ], [
                    'parent_style' => 'pt-button',
                    'font-size' => '14'
                ])
            ], [
                'onclick' => $this->getOnclickPurchase($iosAnnual, $androidAnnual, [
                    'id' => 'bought_yearly'
                ]),
                'style' => 'pt-button-container'
            ]);

        }

        $this->layout->scroll[] = $this->getComponentText('{#explanation_monthly_more#}', array(
            'style' => 'subscription_smalltext'
        ));

        $this->layout->scroll[] = $this->getComponentText('{#explanation_yearly_more#}', array(
            'style' => 'subscription_smalltext'
        ));

        if ($this->model->getSavedVariable('system_source') == 'client_iphone') {
            $this->layout->scroll[] = $this->getComponentText('{#ios_payment_info_1#}', array(
                'style' => 'subscription_smalltext'
            ));

            $this->layout->scroll[] = $this->getComponentText('{#ios_payment_info_2#}', array(
                'style' => 'subscription_smalltext'
            ));
        } else {
            $this->layout->scroll[] = $this->getComponentText('{#android_payment_info_1#}', array(
                'style' => 'subscription_smalltext'
            ));

            $this->layout->scroll[] = $this->getComponentText('{#android_payment_info_2#}', array(
                'style' => 'subscription_smalltext'
            ));
        }


        $this->layout->scroll[] = $this->getComponentSpacer(15);
    }

    private function getRestoreButton() {

        if ( $this->model->getSavedVariable('system_source') == 'client_android' ) {
            return false;
        }

        $this->layout->scroll[] = $this->getComponentRow([
            $this->getComponentText('{#restore_previous_purchase#}', [
                'uppercase' => true,
                'onclick' => $this->getOnclickPurchaseRestore()
            ], [
                'parent_style' => 'pt-button',
                'background-color' => '#449ED1',
                'font-size' => '14'
            ])
        ], [
            'style' => 'pt-button-container'
        ]);

        return true;
    }

}