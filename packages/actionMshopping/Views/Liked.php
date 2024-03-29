<?php

namespace packages\actionMshopping\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMshopping\Controllers\Components;
use packages\actionMshopping\Models\Model as ArticleModel;

use function stristr;

class Liked extends BootstrapView
{
    /* @var \packages\actionMshopping\Components\Components */
    public $components;
    public $theme;
    public $margin;
    public $grid;
    public $deleting;
    public $presetData;
    /* @var ArticleModel */
    public $model;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    /**
     * Default view entry point
     *
     * @return \stdClass
     */
    public function tab1()
    {
        $this->layout = new \stdClass();
        $this->model->setBackgroundColor('#1b1b1b');

        $liked = $this->getData('liked', 'array');

        if (is_null($liked) || empty($liked)) {
            $this->layout->scroll[] = $this->getComponentText('{#you_haven\'t_liked_any_items_yet#}', array(), array(
                'color' => '#ffffff',
                'text-align' => 'center',
                'margin' => '20 0 0 0',
                'font-weight' => 'bold'
            ));

            return $this->layout;
        }

        foreach ($liked as $item) {
            $this->renderItem($item->item);
        }

        return $this->layout;
    }

    /**
     * Render single item
     *
     * @param $item
     */
    protected function renderItem($item)
    {
        $artist = \AeplayVariable::getArrayOfPlayvariables($item->play_id);
        $images = $item->getImages();

        $this->layout->scroll[] = $this->components->getItemCard(array(
            'item' => $item,
            'artist' => $artist,
            'image' => $images->itempic
        ));
    }
}