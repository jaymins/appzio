<?php

namespace packages\actionMgallery\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMgallery\Components\Components;
use packages\actionMgallery\Models\Model as ArticleModel;

/* this is the default view, that is extended
by the theme views. Note that the theme should
have the corresponding views, even though they would
not be used */

class Edit extends BootstrapView
{
    /* @var ArticleModel */
    public $model;

    /* @var Components */
    public $components;

    public function __construct($obj)
    {
        parent::__construct($obj);
    }

    /**
     * Main view entry point
     *
     * @return \stdClass
     */
    public function tab1()
    {
        $item = $this->getData('item', 'num');
        $this->layout = new \stdClass();
        $this->layout->header[] = $this->components->themeSmallHeader('{#progress_photos#}', [
            'onclick' => $this->getOnclickSubmit('swiper/default/' . $item)
        ]);
        $this->setImages();
        return $this->layout;
    }

    public function setImages()
    {
        $data = $this->getData('images', 'array');
        $item = $this->getData('item', 'num');

        if (!$data) {
            return false;
        }

        $row[] = $this->getComponentText('{#click_to_change#}', [], [
            'font-size' => '32',
            'font-ios' => 'OpenSans-ExtraBold',
            'text-align' => 'center',
            'width' => '100%',
            'font-android' => 'OpenSans-ExtraBold',
        ]);

        $this->layout->scroll[] = $this->getComponentRow($row, [], [
            'padding' => '15 20 25 20', 'background-color' => '#ffffff'
        ]);

        if ($data) {
            foreach ($data as $image) {
                if ($image->id == $item) {
                    $page[] = $this->getComponentImage($image->image, [
                        'variable' => 'newimage',
                    ], [
                        'margin' => '0 10 0 10',
                        'width' => $this->screen_width - 20,
                        'height' => $this->screen_height - 330,
                        'crop' => 'yes'
                    ]);
                }
            }
        }

        if (!isset($page)) {
            return false;
        }

        $this->layout->scroll[] = $this->getComponentColumn($page, [
            'onclick' => [
                $this->getOnclickImageUpload('newimage', ['max_dimensions' => '900', 'sync_open' => true]),
            ]
        ], [
            'width' => $this->screen_width,
            'height' => $this->screen_height - 330,
            'margin' => '-10 0 20 0',
        ]);

        $this->layout->scroll[] = $this->components->themeButton('{#save#}',
            [$this->getOnclickSubmit('edit/save/' . $item),
                $this->getOnclickOpenAction('gallery')
            ],
            'theme-icon-small-save-light.png'
        );


    }


}
