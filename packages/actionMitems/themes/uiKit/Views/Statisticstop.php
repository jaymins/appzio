<?php

namespace packages\actionMitems\themes\uiKit\Views;

use packages\actionMitems\Models\Model as ArticleModel;
use packages\actionMitems\themes\uiKit\Components\Components as Components;

class Statisticstop extends Statistics
{
    /* @var Components */
    public $components;

    /* @var ArticleModel */
    public $model;

    public function tab1()
    {
        $this->layout = new \StdClass;

        $this->getTabs(2);

        $this->getFilterRow();

        $this->getTopCoutries();

        $this->getTopUsers();

        return $this->layout;
    }

    protected function getTabs($tab)
    {
        $this->layout->header[] = $this->uiKitTabNavigation(array(
            array(
                'text' => strtoupper('{#latest_visits#}'),
                'onclick' => $this->getOnclickOpenAction('latestvisits', false, [
                    'transition' => 'none'
                ]),
                'active' => $tab == 1
            ),
            array(
                'text' => strtoupper('{#top_5#}'),
                'onclick' => $this->getOnclickTab(1),
                'active' => $tab == 2
            )
        ));
    }

    private function getTopCoutries()
    {
        $countries = $this->getData('top_countries', 'mixed');

        if (empty($countries)) {
            return false;
        }

        $this->layout->scroll[] = $this->getHeading('{#top_5_countries#}');

        $this->layout->scroll[] = $this->getTableHeaders([
            '{#country#}',
            '#{#reviews#}',
        ]);

        foreach ($countries as $country) {
            $country_name = (isset($country['country']) AND !empty($country['country'])) ?
                $country['country'] : 'N/A';

            $num = (isset($country['frequency']) AND !empty($country['frequency'])) ?
                $country['frequency'] : 'N/A';

            $this->layout->scroll[] = $this->getStatisticsRow($country_name, $num);
            $this->layout->scroll[] = $this->getComponentDivider();
        }

        return true;
    }

    private function getTopUsers()
    {
        $users = $this->getData('top_users', 'mixed');

        if (empty($users)) {
            return false;
        }

        $this->layout->scroll[] = $this->getHeading('{#top_5_users#}');

        $this->layout->scroll[] = $this->getTableHeaders([
            '{#name#}',
            '#{#reviews#}',
        ]);

        foreach ($users as $user) {
            $name = (isset($user['user_details']['real_name']) AND !empty($user['user_details']['real_name'])) ?
                        $user['user_details']['real_name'] : 'N/A';

            $num = (isset($user['frequency']) AND !empty($user['frequency'])) ?
                        $user['frequency'] : 'N/A';

            $this->layout->scroll[] = $this->getStatisticsRow($name, $num);
            $this->layout->scroll[] = $this->getComponentDivider();
        }

        return true;
    }

    private function getHeading(string $text)
    {
        return $this->getComponentText($text, [], [
            'font-size' => '18',
            'padding' => '20 15 20 15',
        ]);
    }

    private function getStatisticsRow($value_left, $value_right)
    {
        return $this->getComponentRow([
            $this->getComponentColumn([
                $this->getComponentText($value_left, [], [
                    'color' => '#333333',
                    'font-size' => '13',
                    'padding' => '8 10 8 10'
                ])
            ], [], [
                'width' => '50%',
                'text-align' => 'center',
            ]),
            $this->getComponentColumn([
                $this->getComponentText($value_right, [], [
                    'color' => '#333333',
                    'font-size' => '13',
                    'padding' => '8 10 8 10'
                ])
            ], [], [
                'width' => '50%',
                'text-align' => 'center',
            ]),
        ], [], [
            'padding' => '6 8 6 8',
        ]);
    }

    private function getFilterRow()
    {
        $this->layout->header[] = $this->getComponentRow([
            $this->getFilterMonth(),
            $this->getFilterYear('{#year#}: ', 'filter_year'),
        ], [], [
            'width' => 'auto',
            'vertical-align' => 'middle',
            'padding' => '15 15 15 15',
        ]);

        $this->layout->header[] = $this->getComponentDivider();
    }

    private function getFilterMonth()
    {
        return $this->getComponentRow([
            $this->getComponentText('{#month#}: ', [], [
                'font-size' => 12,
                'color' => '#333333',
            ]),
            $this->getComponentText($this->model->getSavedVariable('filter_month', date('F')), [
                'variable' => 'filter_month',
            ], [
                'font-size' => 12,
                'color' => '#333333',
            ]),
        ], [
            'onclick' => $this->getOnclickShowDiv('filter_month', $this->getDivParams())
        ], [
            'vertical-align' => 'middle',
        ]);
    }

    private function getFilterYear()
    {
        return $this->getComponentRow([
            $this->getComponentText('{#year#}: ', [], [
                'font-size' => 12,
                'color' => '#333333',
            ]),
            $this->getComponentText($this->model->getSavedVariable('filter_year', date('Y')), [
                'variable' => 'filter_year',
            ], [
                'font-size' => 12,
                'color' => '#333333',
            ]),
        ], [
            'onclick' => $this->getOnclickShowDiv('filter_year', $this->getDivParams())
        ], [
            'vertical-align' => 'middle',
            'floating' => 1,
            'float' => 'right',
        ]);
    }

}