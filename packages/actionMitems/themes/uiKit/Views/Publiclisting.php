<?php

namespace packages\actionMitems\themes\uiKit\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMitems\Models\Model as ArticleModel;
use packages\actionMitems\themes\uiKit\Components\Components as Components;

class Publiclisting extends BootstrapView
{
    /* @var Components */
    public $components;

    /* @var ArticleModel */
    public $model;

    public function tab1()
    {
        $this->layout = new \stdClass();
        $this->getListView(1);
        return $this->layout;
    }

    public function tab2()
    {
        $this->layout = new \stdClass();
        $this->getListView(2);
        return $this->layout;
    }

    private function getListView(int $active_tab)
    {

        $this->model->rewriteActionConfigField('background_color', '#ffffff');

        $this->renderSearchField();

        $this->layout->header[] = $this->uiKitDivider();

        $this->layout->header[] = $this->setHeader($active_tab);

        // Provide a smoother UX
        if ($this->getData('current_tab', 'int') !== $active_tab) {

            $this->layout->scroll[] = $this->getComponentLoader(array(
                'style' => 'mit_loader',
            ));

            return $this->layout;
        }

        $items = $this->getData('items', 'array');

        if ($items) {
            $this->layout->scroll[] = $this->uiKitPeopleList($items, array(
                'infinite' => $this->getData('filtering', 'bool')
            ));

            $this->layout->scroll[] = $this->getComponentFormFieldText('', array(
                'variable' => 'recipient_email',
                'visibility' => 'hidden'
            ));
        } else {
            $this->layout->scroll[] = $this->getComponentText('{#currently_there_aren\'t_any_members_matching_your_criteria#}', array(
                'style' => 'mit_no_items'
            ));
        }

        return true;
    }

    public function getDivs()
    {
        $divs['email'] = $this->uiKitEmailDiv();

        return $divs;
    }

    protected function setHeader($tab)
    {
        return $this->uiKitTabNavigation(array(
            array(
                'text' => strtoupper('{#current_country#}'),
                'onclick' => $this->getOnclickTab(1),
                'active' => $tab == 1
            ),
            array(
                'text' => strtoupper('{#all#}'),
                'onclick' => $this->getOnclickTab(2),
                'active' => $tab == 2
            ),
        ), array(), array(
            'font-size' => 12
        ));
    }

    protected function renderSearchField()
    {
        $this->layout->header[] = $this->uiKitSearchField(array(
            'onclick_close' => $this->getOnclickSetVariables(array('searchterm' => ''))
        ));
    }

}