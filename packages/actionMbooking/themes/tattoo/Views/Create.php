<?php
namespace packages\actionMbooking\themes\tattoo\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMbooking\Models\Model as ArticleModel;
use packages\actionMbooking\themes\tattoo\Components\Components;

class Create extends BootstrapView
{

    /* @var Components */
    public $components;

    public $theme;

    /**
     * Your model and Bootstrap model methods are accessible through this variable
     * @var ArticleModel
     */
    public $model;

    public function tab1()
    {
        $this->layout = new \stdClass();
        $this->model->rewriteActionConfigField('background_color', '#ffffff');

        if ($this->getData('saved', 'bool')) {
            $this->layout->onload[] = $this->getOnclickOpenAction('bookingslist');
            return $this->layout;
        }
        $item = $this->getData('item', 'object');

        if(!is_object($item) AND !isset($item->id)){
            return $this->layout;
        }

        $images = @json_decode($item->images);

        $firstname = isset($item->owner['firstname']) ? $item->owner['firstname'] : '{#anonymous#}';

        $this->renderHeader($firstname);

        $this->renderImageSlider($images);

        $this->layout->scroll[] = $this->getArtistRow($item);

        if ( isset($item->tags) ) {
            $this->renderTags($item->tags);
        }

        if (!empty($item->category)) {
            $this->renderCategory($item->category);
        } else if ( isset($item->categories) ) {
            $this->renderCategories($item->categories);
        }

        $this->layout->scroll[] = $this->components->getComponentSpacer(25);
        $this->layout->scroll[] = $this->components->uiKitBackgroundHeader(strtoupper('{#choose_date#}'));

        $this->renderDatePicker();
        $this->layout->scroll[] = $this->components->getComponentSpacer(10);

        $this->layout->scroll[] = $this->components->uiKitBackgroundHeader(strtoupper('{#choose_hour#}'));

        $this->renderTimePicker();

        $this->layout->scroll[] = $this->components->getComponentSpacer(10);
        $this->layout->scroll[] = $this->components->uiKitBackgroundHeader(strtoupper('{#note_to#} ' . $firstname ));

        $this->renderNotesField();

        $this->renderSubmitButton($firstname);

        return $this->layout;
    }

    public function getSeparator()
    {
        return $this->getComponentText('', array(), array(
            'background-color' => '#4a4a4d',
            'height' => '1',
            'width' => '100%',
        ));
    }

    public function renderCategory($category)
    {
        $this->layout->scroll[] = $this->getComponentRow(array(
            $this->components->getBookTattooItemCategory($category->name)
        ), array(), array(
            'margin' => '5 15 15 15'
        ));
    }

    public function renderCategories($categories)
    {
        $items = [];

        foreach ($categories as $category) {
            $items[] = $this->components->getBookTattooItemCategory($category->name);
        }

        $this->layout->scroll[] = $this->getComponentWrapRow($items, [], [
            'margin' => '5 15 15 15'
        ]);

        return true;
    }

    public function renderTags($tags)
    {
        $items = array();

        $maxLength = 25;
        $currentLength = 0;

        $maxPerRowCount = 3;
        $currentCount = 0;
        $isTitle = true;

        foreach ($tags as $tag) {
            if ($currentLength >= $maxLength || $currentCount > $maxPerRowCount) {
                $this->layout->scroll[] = $this->getComponentRow($items, array(), array(
                    'padding' => '0 10 0 10'
                ));
                $items = array();
                $currentLength = 0;
                $currentCount = 0;
            }
            if($isTitle){
                $items[] = $this->components->getBookTattooItemTag('Tags:');
                $isTitle = false;
            }

            $items[] = $this->components->getBookTattooItemTag($tag->name);
            $currentLength += strlen($tag->name);
            $currentCount++;
        }

        $this->layout->scroll[] = $this->getComponentRow($items, array(), array(
            'padding' => '0 10 0 10'
        ));
    }

    protected function renderTimePicker()
    {
        $this->layout->scroll[] = $this->components->uiKitHintedTime(date('H'), 0, array(
            'disable_hint' => true,
        ), array(
            'margin' => '0 15 0 15'
        ));
        $this->renderValidationErrors('time');
    }

    protected function renderNotesField()
    {
        $this->layout->scroll[] = $this->getComponentFormFieldTextArea('', array(
            'variable' => 'notes',
            'hint' => 'Notes...',
        ), array(
            'margin' =>  '0 0 0 0',
            'background-color' => '#ffffff',
            'padding' =>  '10 15 10 15',
            'color' => '#000000'
        ));

        $this->renderValidationErrors('notes');
    }

    protected function renderSubmitButton($name)
    {
        $this->layout->footer[] = $this->getComponentRow(array(
            $this->getComponentText('Book with ' . $name, array(
                    'onclick' => $this->getOnclickSubmit('Controller/save')
            ), array(
                "background-color" => "#fbe121",
                "color" => "#333333",
                "border-radius" => "20",
                "width" => "80%",
                "padding" => "10 30 10 30",
                "text-align" => "center"
            ))
        ), array(), array(
            "text-align" => "center",
            'margin' => '0 0 10 0'
        ));
    }

    protected function renderHeader($name)
    {
        $this->layout->header[] = $this->getComponentRow(array(
            $this->getComponentRow(array(
                $this->getComponentImage('back-arrow.png', array(
                    'onclick' => $this->getOnclickGoHome()
                ), array(
                    'vertical-align' => 'middle',
                    'width' => '30',
                    'floating' => true,
                    'float' => 'left'
                ))
            ), array(), array(
//                'width' => $this->screen_width / 3.1,
                'width' => '10%',

            )),
            $this->getComponentRow(array(
                $this->getComponentText('Book with ' . $name, array(), array(
                    'vertical-align' => 'middle',
                    'color' => '#ffffff'
                ))
            ), array(), array(
//                'width' => $this->screen_width / 3.1,
                'width' => '80%',

                'text-align' => 'center',
            )),
            $this->getComponentRow(array(
                $this->getBlockButton()
            ), array(), array(
//                'width' => $this->screen_width / 3.1,
                'width' => '10%',
                'text-align' => 'right'
            ))
        ), array(), array(
            'vertical-align' => 'middle',
            'text-align' => 'center',
            'padding' => '10 0 10 0',
            'background-color' => '#29292c'
        ));
    }

    protected function renderDatePicker()
    {
        $this->layout->scroll[] = $this->components->uiKitHintedCalendar('Date', 'date', time(), array(
            'active_icon' => 'calendar_black.png',
            'inactive_icon' => 'calendar_black.png',
            'disable_hint' => true,
        ), array(
            'margin' => '0 15 0 15',
        ));

        $this->renderValidationErrors('date');
    }

    protected function renderValidationErrors($variableName)
    {
        if (isset($this->model->validation_errors[$variableName])) {
            $this->layout->scroll[] = $this->components->validationErrorText($this->model->validation_errors[$variableName]);
        }
    }

    public function getBlockButton()
    {
        $isLiked = $this->getData('isLiked', 'bool');

        return $this->components->uiKitButtonBlock(array(
            'isLiked' => $isLiked
        ));
    }

    public function renderImageSlider($images)
    {
        if (empty($images) || is_null($images)) {
            return false;
        }

        $fallback_image_path = $this->getImageFileName('image-placeholder.png', array('debug' => false, 'imgwidth' => 900, 'imgheight' => 720, 'imgcrop' => 'yes'));
        $height = round($this->screen_width / 1.25, 0);

        $image_styles['imgwidth'] = '900';
        $image_styles['imgheight'] = '720';
        $image_styles['width'] = $this->screen_width;
        $image_styles['height'] = $height;
        $image_styles['imgcrop'] = 'yes';
        $image_styles['not_to_assetlist'] = true;
        $image_styles['priority'] = '9';

        $navi_styles['margin'] = '-60 0 0 0';
        $navi_styles['text-align'] = 'center';

        $content = array();
        $current = 1;

        foreach ($images as $image) {
            $content[] = $this->getComponentColumn(array(
                $this->getComponentImage($image, array(
                    'priority' => 9,
                    'tap_to_open' => 1,
                    'image_fallback' => $fallback_image_path
                ), $image_styles),
                $scroll[] = $this->getComponentSwipeNavi(count((array)$images), $current, 'white', array(), $navi_styles)
            ));

            $current++;
        }

        $this->layout->scroll[] = $this->getComponentSwipe($content, array(), array(
            'background-color' => '#1d1d1d'
        ));
    }

    public function getArtistRow($item)
    {

        if ( !isset($item->owner) ) {
            return $this->getComponentText('{#missing_item#}');
        }
        
        $profilpic = isset($item->owner['profilepic']) ? $item->owner['profilepic'] : 'icon_camera-grey.png';
        $firstname = isset($item->owner['firstname']) ? $item->owner['firstname'] : '{#anonymous#}';
        $lastname = isset($item->owner['lastname']) ? $item->owner['lastname'] : '{#anonymous#}';
        $time = $item->time > 0 ? 'approx. ' . $item->time .' h to complete' : '{#ask_for_time#}';
//        $price = $item->owner['price'] > 0 ?  $item->owner['price'] .' per hour' : '{#ask_for_price#}';

        if(isset($item->owner['prices']) && $item->owner['price'] > 0){
            $price = $item->owner['price'] .' per hour';
            $isLogoPrice = true;
        }
        else{
            $isLogoPrice = false;
            $price = '{#ask_for_price#}';
        }

        return $this->getComponentColumn(array(
            $this->getComponentRow(array(
                $this->getComponentRow(array(
                    $this->getComponentImage('clock_2.png', array(), array(
                        'width' => '18',
                        'margin' => '0 5 0 0',
                    )),
                )),
                $this->getComponentRow(array(
                    $this->getComponentText($time, array(), array(
                        'floating' => '1',
                        'float' => 'left',
                        'color' => '#fcc037',
                        'font-size' => '13'
                    )),
                )),
            ), array(), array(
                'padding' => '9 15 9 15',
                'vertical-align' => 'middle',
            )),
            $this->getComponentRow(array(
                $this->getComponentImage($profilpic, array(
                    'priority' => 9,
                ), array(
                    'width' => '55',
                    'crop' => 'round',
                    'margin' => '0 10 0 0',
                )),
                $this->getComponentColumn(array(
                    $this->getComponentRow(array(
                        $this->getComponentText($firstname . ' ' . $lastname, array(), array(
                            'color' => '#000000',
                            'font-size' => '18',
                            'font-weight' => 'bold',
                        )),
                    ), array(), array(
                    )),
                    $this->getComponentRow(array(
                        $this->getComponentImage('american-dollar-symbol.png', array(), array(
                            'width' => '14',
                            'vertical-align' => 'middle'
                        )),
                        $this->getComponentText($price, array(), array(
                            'color' => '#000000',
                            'font-size' => '18',
                        )),
                    ), array(), array())
                ))
            ), array(), array(
                'padding' => '5 15 5 15',
                'vertical-align' => 'middle'
            )),
        ));
    }

}