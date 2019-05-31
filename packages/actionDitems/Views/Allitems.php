<?php

namespace packages\actionDitems\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionDitems\Components\Components as Components;
use packages\actionDitems\Models\ItemModel;
use packages\actionDitems\Models\Model as ArticleModel;

class Allitems extends BootstrapView
{
    /**
     * @var Components
     */
    public $components;

    /**
     * @var ArticleModel
     */
    public $model;

    /**
     * Main view entrypoint
     *
     * @return \stdClass
     */
    public function tab1()
    {
        $this->layout = new \stdClass();

        $items = $this->getData('items', 'array');

        $this->model->setBackgroundColor('#1e1e1e');

        if (empty($items) || is_null($items)) {
            $this->layout->scroll[] = $this->getComponentText('{#no_items_yet#}', array('style' => 'mit_no_items'));
        }

        // Initial padding
        $this->layout->scroll[] = $this->getComponentSpacer(10);

        foreach ($items as $item) {
            $this->layout->scroll[] = $this->getItemRow( $item );
        }

        return $this->layout;
    }

	protected function getItemRow($item) {
		$time = $item->time > 0 ?
			$item->time . ' h' : '';

		return $this->getComponentRow(array(
			$this->getComponentText($item->name, array(), array(
				'width' => '70%',
				'color' => '#ffffff',
			)),
			$this->getComponentText($time, array(), array(
				'color' => '#ffffff',
				'width' => '20%',
				'text-align' => 'right',
				'floating' => '1',
				'float' => 'right',
			))
		), array(), array(
			'padding' => '5 10 5 10',
			'width' => 'auto'
		));

	}

}