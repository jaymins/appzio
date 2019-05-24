<?php

/*

    Layout code codes here. It should have structure of
    $this->data->scroll[] = $this->getElement();

    supported sections are header,footer,scroll,onload & control
    and they should always be arrays

*/

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.packages.actionMobileregister.models.*');
Yii::import('application.modules.aelogic.packages.actionMobilelogin.models.*');

class DetailsMobileclassifiedsView extends MobileclassifiedsView {

    public $data;
    public $theme;

    public function tab1(){
        $this->data = new StdClass();

        if (is_numeric($this->menuid)) {
            $this->sessionSet('current_item', $this->menuid);
        }

        $this->clearSavedPictureVariables();

        $this->toggleFavouriteItem();

        $currentItem = $this->itemsModel->getItem($this->sessionGet('current_item'));
        $creator = $this->getPlayVariables($currentItem['creator']);

        if(!isset($currentItem['id'])){
            $this->data->scroll[] = $this->getText('{#no_items_yet#}');
            return $this->data;
        }

        if(isset($creator['lat'])){
            $geolocation = $creator['lat'] . ',' . $creator['lon'];
            $request = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.$geolocation.'&sensor=false';
            $file_contents = file_get_contents($request);
            $json_decode = json_decode($file_contents);
            $location = '';
            foreach ($json_decode->results[0]->address_components as $address_components) {
                if (isset($address_components->types) && $address_components->types[0] == 'locality') {
                    $location .= $address_components->long_name . ', ';
                }
                if (isset($address_components->types) && $address_components->types[0] == 'country') {
                    $location .= $address_components->long_name;
                }
            }
        }


        $this->data->scroll[] = $this->renderSlider($currentItem['id'], $currentItem['creator'], $currentItem['pictures']);

        $price = '$ ' . $currentItem['price'] / 100;
        $this->data->scroll[] = $this->renderTitle($currentItem['title'], $price);

        $this->data->scroll[] = $this->renderLocation($currentItem['id'], $location, $currentItem['favourite'], $currentItem['creator']);

        $creatorName = isset($creator['real_name']) ? $creator['real_name'] : (isset($creator['name']) ? $creator['name'] : '');
        $posted = Yii::app()->dateFormatter->formatDateTime($currentItem['created_at'], 'long', null);  //'4 days ago',
        $this->data->scroll[] = $this->renderName($creator['profilepic'], $creatorName, $posted);

        $this->data->scroll[] = $this->renderCategory('Electronics > Home > ' . $currentItem['category']);

        $this->data->scroll[] = $this->renderDescription($currentItem['description']);

        $phone = isset($creator['phone']) ? $creator['phone'] : '';
        $this->data->footer[] = $this->renderButtons($phone);

        return $this->data;
    }

    private function renderSlider($id, $creator, $pictures)
    {
        $pictures = json_decode($pictures);
        if (!empty($pictures)) {
            foreach($pictures as $picture) {
                if (!empty($picture)) {
                    $swipes[] = $this->getOneSwipe($id, $creator, $picture);
                }
            }
        } else {
            $swipes[] = $this->getOneSwipe($id, $creator);
        }

        return $this->getSwipearea($swipes,[
            'width' => '100%',
            'animate' => 'nudge',
            'remember_position' => '1',
            'margin' => '-10 0 0 0'
        ]);
    }

    private function renderTitle($title, $price)
    {
        return $this->getRow([
            $this->getText($title, [
                'font-size' => '24',
                'color' => '#4B4B4B'
            ]),
            $this->getText($price, [
                'color' => '#6DC176',
                'floating' => 1,
                'float' => 'right'
            ])
        ], [
            'background-color' => '#FFFFFF',
            'padding' => '10 10 5 10'
        ]);
    }

    private function renderLocation($id, $location, $favourite, $creator)
    {
        $rows[] = $this->getImage('location_pint.png', [
            'crop' => 'yes',
            'width' => '12',
            'margin' => '3 5 0 0'
        ]);

        $rows[] = $this->getText($location, [
            'color' => '#BCBCBC',
            'font-size' => '20'
        ]);

        if ($creator != $this->itemsModel->factory->playid) {
            $rows[] = $this->getImage($favourite ? 'star_selected.png' : 'star_not_selected.png', [
                'crop' => 'yes',
                'width' => '20',
                'floating' => 1,
                'float' => 'right',
                'onclick' => $this->getOnclick('id', false, 'favourite-' . $id)
            ]);
        }

        return $this->getRow($rows, [
            'background-color' => '#FFFFFF',
            'padding' => '0 10 15 10'
        ]);
    }

    private function renderName($profilepic, $name, $posted)
    {
        $rows[] = $this->getHairline('#EFEFEF', [
            'width' => $this->screen_width - 10,
            'padding' => '10 5 10 5'
        ]);

        $rows[] = $this->getRow([
            $this->getImage($profilepic, [
                'crop' => 'yes',
                'width' => '40',
                'border-radius' => '20',
                'margin' => '10 10 10 0'
            ]),
            $this->getText($name, [
                'color' => '#909090',
                'margin' => '10 0 10 0'
            ]),
            $this->getText($posted, [
                'color' => '#D0D0D0',
                'floating' => 1,
                'float' => 'right'
            ])
        ]);

        return $this->getColumn($rows, [
            'padding' => '0 10 0 10',
            'background-color' => '#FFFFFF'
        ]);
    }

    private function renderCategory($category)
    {
        return $this->getRow([
            $this->getText($category, [
                'color' => '#B0B0B0',
                'margin' => '10 0 10 0'
            ])
        ], [
            'padding' => '0 10 0 10',
            'background-color' => '#F3F3F3',
            'shadow-color' => '#DFE3E6',
            'shadow-offset' => '0 3',
            'shadow-opacity'  => '0.9'
        ]);
    }

    private function renderDescription($description)
    {
        return $this->getRow([
            $this->getText($description, [
                'color' => '#939393',
                'margin' => '10 0 10 0'
            ])
        ], [
            'padding' => '0 10 0 10',
        ]);
    }

    private function renderButtons($phone)
    {
        return $this->getRow([
            $this->getColumn([
                $this->getRow([
                    $this->getImage('call_icon.png'),
                    $this->getText('CALL', [
                        'color' => '#FFFFFF',
                        'font-size' => '20',
                        'margin' => '0 0 0 10'
                    ])
                ]),
            ], [
                'background-color' => '#45D194',
                'vertical-align' => 'middle',
                'width' => $this->screen_width / 2 - 1,
                'text-align' => 'center',
                'padding' => '20 0 20 0',
                'onclick' => $this->getOnclick('url',false,'tel:' . $phone)
            ]),
            $this->getVerticalSpacer('2', [
                'color' => '#F0F3F8'
            ]),
            $this->getColumn([
                $this->getRow([
                    $this->getImage('sms_icon.png'),
                    $this->getText('SMS', [
                        'color' => '#FFFFFF',
                        'font-size' => '20',
                        'margin' => '0 0 0 10'
                    ])
                ]),
            ], [
                'background-color' => '#F3665C',
                'vertical-align' => 'middle',
                'width' => $this->screen_width / 2 - 1,
                'text-align' => 'center',
                'padding' => '20 0 20 0',
                'onclick' => $this->getOnclick('url',false,'sms:' . $phone)
            ])
        ]);
    }

    private function getOneSwipe($id, $creator, $picture = null)
    {
        $row[] = $this->getImage($picture, [
            'crop' => 'yes',
            'height' => $this->screen_width,
            'defaultimage' => 'profile-add-photo-grey.png'
        ]);

        $clicker = new StdClass();
        $clicker->action = 'open-action';
        $clicker->action_config = $this->getActionidByPermaname('edititem');
        $clicker->id = $id;
        $clicker->sync_open = '1';
        $clicker->back_button = '1';

        $params = [
            'width' => $this->screen_width
        ];

        if ($creator == $this->itemsModel->factory->playid) {
            $params['onclick'] = $clicker;
        }

        return $this->getColumn($row, $params);
    }
}