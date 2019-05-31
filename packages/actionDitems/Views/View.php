<?php

namespace packages\actionDitems\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionDitems\Components\Components;
use packages\actionDitems\Models\Model as ArticleModel;

/* this is the default view, that is extended
by the theme views. Note that the theme should
have the corresponding views, even though they would
not be used */

class View extends BootstrapView
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
        $this->model->rewriteActionConfigField('background_color', '#edac34');
        $this->layout = new \stdClass();

        if ($this->getData('close', 'bool')) {
            $this->layout->onload[] = $this->getOnclickGoHome();
            return $this->layout;
        }

        $item = $this->getData('item', 'object');
        
        if (empty($item) || is_null($item)) { 
            return $this->layout;
        }

        if (empty((array)$item)) {
            $this->layout->scroll[] = $this->getComponentText('Whoops, please try again!', array(), array(
                'color' => '#ffffff',
                'text-align' => 'center',
                'padding' => '20 0 20 0',
            ));

            return $this->layout;
        }

        // Set the view subject to be the name of the item
        $this->model->rewriteActionField('subject', !empty($item->name) ? $item->name : '');

        $images = json_decode($item->images);

        $this->renderHeader($item->name);

        $this->renderImageSlider($images);

        $this->layout->scroll[] = $this->getArtistRow($item);

        if (!empty($item->category)) {
            $this->renderCategory($item->category);
        } else {
            $this->renderCategories($item->categories);
        }

        $this->layout->scroll[] = $this->getSeparator();

        $this->renderTags($item->tags);

        $this->getItemDescription($item->description);

        $this->getButtonsForRole($this->model->getSavedVariable('role'), $item);

        return $this->layout;
    }

    protected function renderHeader($itemName)
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
                'width' => $this->screen_width / 3.1,
            )),
            $this->getComponentRow(array(
                $this->getComponentText($itemName, array(), array(
                    'vertical-align' => 'middle',
                    'color' => '#ffffff'
                ))
            ), array(), array(
                'width' => $this->screen_width / 3.1,
                'text-align' => 'center',
            )),
            $this->getComponentRow(array(
                $this->getBlockButton()
            ), array(), array(
                'width' => $this->screen_width / 3.1,
                'text-align' => 'right'
            ))
        ), array(), array(
            'vertical-align' => 'middle',
            'text-align' => 'center',
            'padding' => '3 0 3 0'
        ));
    }

    public function getBlockButton()
    {
        $isLiked = $this->getData('isLiked', 'bool');

        return $this->components->uiKitButtonBlock(array(
            'isLiked' => $isLiked
        ));
    }

    public function getArtistRow($item)
    {

        $profilpic = isset($item->owner['profilepic']) ? $item->owner['profilepic'] : 'icon_camera-grey.png';
        $firstname = isset($item->owner['firstname']) ? $item->owner['firstname'] : '{#anonymous#}';
        $lastname = isset($item->owner['lastname']) ? $item->owner['lastname'] : '{#anonymous#}';
        $time = $item->time > 0 ? $item->time .' h' : '{#ask_for_time#}';
        $price = $item->price > 0 ? '$' . $item->price .' per h' : '{#ask_for_time#}';
        $time .= ' / '.$price;

        return $this->getComponentRow(array(
            $this->getComponentImage($profilpic, array(), array(
                'width' => '35',
                'crop' => 'round',
                'margin' => '0 10 0 0'
            )),
            $this->getComponentText($firstname . ' ' . $lastname, array(), array(
                'color' => '#ffffff',
                'font-size' => '18',
                'font-weight' => 'bold'
            )),
            $this->getComponentText($time, array(
                'style' => 'item_details_price'
            ))
        ), array(), array(
            'padding' => '5 10 5 10',
            'vertical-align' => 'middle'
        ));
    }

    public function getSeparator()
    {
        return $this->getComponentText('', array(), array(
            'background-color' => '#282828',
            'height' => '1',
            'width' => '100',
            'margin' => '0 0 15 10'
        ));
    }

    public function getItemDescription($description)
    {
        if ( empty($description) ) {
            return false;
        }

        $this->layout->scroll[] = $this->getComponentText($description, array(
            'style' => 'item_description'
        ));
    }

    public function renderCategory($category)
    {
        $this->layout->scroll[] = $this->getComponentRow(array(
            $this->components->getItemTag($category->name)
        ), array(), array(
            'margin' => '5 15 15 15'
        ));
    }

    protected function renderCategories($categories)
    {
        $items = [];

        foreach ($categories as $category) {
            $items[] = $this->components->getItemTag($category->name);
        }

        $this->layout->scroll[] = $this->getComponentWrapRow($items, [], [
            'margin' => '5 15 15 15'
        ]);

        return true;
    }

    /**
     * View will have different buttons depending on the role
     *
     * @param $role
     * @param $item
     */
    public function getButtonsForRole($role, $item)
    {
        if ($role === 'artist') {
            $this->getArtistButtons($item);
        } else if ($role === 'user') {
            $this->getUserButtons($item);
        }

    }

    /**
     * Renders artist role buttons
     *
     * @param $item
     */
    public function getArtistButtons($item)
    {
        if ($this->model->playid === $item->play_id) {
            $this->layout->footer[] = $this->getActionButton(
                strtoupper('{#edit#}'),
                'primary',
                $this->getOnclickRoute('Edit/default/' . $item->id, false)
            );
        }
    }

    /**
     * Renders user role buttons
     *
     * @param $item
     */
    public function getUserButtons($item)
    {
        $isLiked = $this->getData('isLiked', 'bool');
        $isBooked = $this->getData('isBooked', 'bool');

        $this->layout->scroll[] = $this->getComponentSpacer(20);

        if ($isLiked) {
            $this->renderOtherItems($item);
            if (!$isBooked) {
                $this->layout->footer[] = $this->getActionButton('{#book_with_#}' . $item->owner['firstname'], 'primary', $this->getBookPopup());
            }
            $this->layout->footer[] = $this->getActionButton('{#chat_with_#}' . $item->owner['firstname'], 'chat', $this->openChat($item->play_id));
//            $this->layout->footer[] = $this->getActionButton('{#remove_from_liked#}', 'danger', $this->removeFromLiked());
        } else {
            $this->renderButtons($item->id);
            $this->renderOtherItems($item);
        }
    }

    /**
     * Returns a customized button.
     * Allowed types - primary, chat, danger.
     *
     * @param string $text
     * @param string $type
     * @param $onclick
     * @return \stdClass
     */
    public function getActionButton($text = '', $type = 'primary', $onclick)
    {
        return $this->getComponentText($text, array(
            'style' => 'button_' . $type,
            'onclick' => $onclick
        ));
    }

    public function getBookPopup()
    {
        return $this->getOnclickOpenAction('createbooking', $this->model->getActionidByPermaname('createbooking'), array(
            'open_popup' => 1,
            'sync_close' => 1,
            'sync_open' => 1
        ));
    }

    public function openChat($artistId)
    {
        $matchUserWithArtist = $this->getOnclickRoute('Controller/match/' . $artistId, false);

        $openChat = new \stdClass();
        $openChat->action = 'open-action';
        $openChat->id = $this->getTwoWayChatId($this->model->playid, $artistId);
        $openChat->back_button = true;
        $openChat->sync_open = true;
        $openChat->viewport = 'bottom';
        $openChat->action_config = $this->model->getActionidByPermaname('chat');

        $matchUserWithArtist[] = $openChat;

        return $matchUserWithArtist;
    }

    public function getTwoWayChatId($playId, $artistId)
    {
        if ($playId < $artistId) {
            $chatid = $playId . '-chat-' . $artistId;
        } else {
            $chatid = $artistId . '-chat-' . $playId;
        }

        return $chatid;
    }

    public function removeFromLiked()
    {
        return $this->getOnclickRoute('Controller/remove', false);
    }

    private function renderImageSlider($images)
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

    private function renderTags($tags)
    {
        $items = array();

        $maxLength = 25;
        $currentLength = 0;

        $maxPerRowCount = 3;
        $currentCount = 0;

        foreach ($tags as $tag) {
            if ($currentLength >= $maxLength || $currentCount > $maxPerRowCount) {
                $this->layout->scroll[] = $this->getComponentRow($items, array(), array(
                    'padding' => '0 10 0 10'
                ));
                $items = array();
                $currentLength = 0;
                $currentCount = 0;
            }

            $items[] = $this->components->getItemTag($tag->name);
            $currentLength += strlen($tag->name);
            $currentCount++;
        }

        $this->layout->scroll[] = $this->getComponentRow($items, array(), array(
            'padding' => '0 10 0 10'
        ));
    }

    private function renderButtons($itemId)
    {
        $column[] = $this->getComponentImage('tatjack-dislike-icon.png', array(
            'onclick' => $this->getOnclickRoute('Controller/select/skip', false, array(
                'item_id' => $itemId
            ))
        ), array(
            'width' => '25%',
            'priority' => '1',
            'margin' => '0 80 0 8'
        ));

        $column[] = $this->getComponentImage('tatjack-like-icon.png', array(
            'onclick' => $this->getOnclickRoute('Controller/select/like', false, array(
                'item_id' => $itemId
            ))
        ), array(
            'width' => '25%',
            'priority' => '1',
        ));

        $this->layout->scroll[] = $this->getComponentRow($column, array(), array(
            'text-align' => 'center',
            'noanimate' => true,
            'margin' => '20 0 0 0'
        ));

        $this->layout->scroll[] = $this->getComponentSpacer(20);

    }

    public function renderOtherItems($item)
    {
        $otherItems = $this->model->getOtherArtistItems($item->play_id, $item->id, 100);

        if (empty($otherItems)) {
            return true;
        }

        $this->layout->scroll[] = $this->getComponentColumn(array(
            $this->getComponentImage($item->owner['profilepic'], array(), array(
                'width' => '25',
                'crop' => 'round',
                'margin' => '0 10 0 0'
            )),
            $this->getComponentText(strtoupper('{#other_works_from#} ' . $item->owner['firstname']), array(), array(
                'color' => '#ffffff',
                'font-size' => '16',
                'font-weight' => 'bold',
                'width' => '100%',
                'text-align' => 'center',
                'margin' => '10 0 0 0'
            )),
        ), array(), array(
            'text-align' => 'center',
            'width' => '100%',
            'margin' => '10 0 0 0'
        ));

        $rows = array_chunk( $otherItems, 3 );

	    $output_rows = array();

	    foreach ( $rows as $row_items ) {

		    $otherItemsRow = array();

		    foreach ( $row_items as $row_item ) {
			    $onclick = new \stdClass();
			    $onclick->action = 'open-action';
			    $onclick->back_button = 1;
			    $onclick->action_config = $this->model->getActionidByPermaname('singletattoo');
			    $onclick->sync_open = 1;
			    $onclick->id = $row_item->id;

			    $images = $row_item->getImages();
			    $otherItemsRow[] = $this->getComponentImage($images->itempic, array(
				    'onclick' => $onclick
			    ), array(
				    'width' => $this->screen_width / 3.3,
				    'height' => $this->screen_width / 3.3,
				    'crop' => 'yes',
				    'margin' => '0 8 0 0',
				    'border-radius' => '3',
			    ));
			}

		    $output_rows[] = $this->getComponentRow($otherItemsRow, array(), array(
			    'padding' => '10 10 10 10',
			    'margin' => '20 0 0 0'
		    ));

        }

	    $this->layout->scroll[] = $this->getComponentSwipe($output_rows, array(), array('width' => '100%'));

    }

    public function getDivider()
    {
        return $this->getComponentText('', array('style' => 'mreg_divider'));
    }
}
