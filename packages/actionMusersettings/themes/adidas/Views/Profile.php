<?php

namespace packages\actionMusersettings\themes\adidas\Views;

use Bootstrap\Views\BootstrapView;
use packages\actionMusersettings\themes\adidas\Models\Model as ArticleModel;

class Profile extends BootstrapView {

    /* @var ArticleModel */
    public $model;

	/* @var \packages\actionMswipematch\Components\Components */
	public $components;
	public $theme;
	public $user_data;

	public function __construct($obj) {
		parent::__construct($obj);
	}

	/* view will always need to have a function called tab1 */
	public function tab1(){
		$this->layout = new \stdClass();

		$this->user_data = $this->getData('userData', 'array');

		$title = '';
		if (!empty($this->user_data['firstname'])) {
			$title = $this->user_data['firstname'] . "'s Profile";
		}

		if (!empty($this->user_data['real_name'])) {
			$realname = $this->user_data['real_name'];
		} else {
			$realname = $this->user_data['firstname'] . " " . $this->user_data['lastname'];
		}

        $this->model->rewriteActionField('subject', !empty($title) ? $title : '');

		$this->showProfilePic();

		$this->layout->scroll[] = $this->getComponentRow(array(
            $this->getComponentText(trim($realname), array(), array(
                'font-size' => '22'
            ))
        ), array(), array(
            'margin' => '5 15 5 15',
            'vertical-align' => 'middle',
        ));

		$distance = 'N/A';
		if (!empty($this->user_data['address'])) {
			$distance = $this->user_data['distance'] . ' km';
		}

		$this->layout->scroll[] = $this->getComponentRow(array(
            $this->getComponentImage('location.png', array(), array(
                'width' => '18',
            )),
            $this->getComponentText($distance, array(
                'style' => 'user_list_info_location'
            ))
        ), array(), array(
			'margin' => '10 15 10 15'
		));

        $this->renderClubs();

		$this->renderOtherItems();
		return $this->layout;
	}


	public function showProfilePic() {
        if (empty($this->user_data['profilepic'])) {
            $this->user_data['profilepic'] = 'icon_camera-grey.png';
        }

        $this->layout->scroll[] = $this->getComponentImage($this->user_data['profilepic'],
            array(
                'imgwidth' => '900',
                'imgheight' => '720'
            ),
            array(
                'width' => $this->screen_width
            ));
    }

    public function renderOtherItems()
    {
        $otherItems = $this->model->getOtherArtistItems($this->user_data['playid'], 100);

        if (empty($otherItems)) {
            return false;
        }

        $this->layout->scroll[] = $this->components->getComponentSpacer(30);

        $this->layout->scroll[] = $this->components->uiKitBackgroundHeader(strtoupper($this->user_data['firstname'] . '\'s {#memorabilia#} '));

        $rows = array_chunk( $otherItems, 3 );

        $output_rows = array();

        foreach ( $rows as $row_items ) {

            $otherItemsRow = array();

            foreach ( $row_items as $row_item ) {
                $onclick = new \stdClass();
                $onclick->action = 'open-action';
                $onclick->back_button = 1;
                $onclick->action_config = $this->model->getActionidByPermaname('itemdetail');
                $onclick->sync_open = 1;
                $onclick->id = $row_item->id;

                $images = $row_item->getImages();

                if(strlen($row_item->name) > 10){
                    $row_item->name = substr($row_item->name, 0, 8) . '...';
                }

                $otherItemsRow[] = $this->getComponentColumn(array(
                    $this->getComponentRow(array(
                        $this->getComponentImage($images->itempic, array(
                            'onclick' => $onclick
                        ), array(
                            'width' => '100%',
                            'height' => $this->screen_width / 3.3,
                            'crop' => 'yes',
                            'border-radius' => '3',
                        ))
                    ), array(), array(
                        'width' => '100%',
                        'height' => $this->screen_width / 3.3,
                    )),
                    $this->getComponentRow(array(
                        $this->getComponentText($row_item->name, array(), array(
                            'color' => '#000000',
                            'margin' => '0 0 0 0',
                            'padding' => '3 0 0 0',
                        ))
                    ), array(), array(
                        'width' => '100%',
                        'text-align' => 'center',
                    ))
                ), array(), array(
                    'width' => $this->screen_width / 3.3,
                    'padding' => '0 3 0 3',
                ));

            }

            $output_rows[] = $this->getComponentRow($otherItemsRow, array(), array(
                'margin' => '20 15 20 15',
                'width' => 'auto'
            ));

        }

        if ($output_rows) {
            $this->layout->scroll[] = $this->getComponentSwipe($output_rows);
        }
    }

    public function getSeparator()
    {
        return $this->getComponentText('', array(), array(
            'background-color' => '#4a4a4d',
            'height' => '1',
            'width' => '100%',
        ));
    }

    public function renderClubs() {
        $otherItems = $this->model->getClubsDetails($this->user_data['selected_football_club']);

        if (empty($otherItems)) {
            return false;
        }

        $this->layout->scroll[] = $this->components->getComponentSpacer(30);

        $this->layout->scroll[] = $this->components->uiKitBackgroundHeader(strtoupper($this->user_data['firstname'] . '\'s {#favorite_clubs#} '));

        $rows = array_chunk( $otherItems, 3 );

        $output_rows = array();

        foreach ( $rows as $row_items ) {

            $otherItemsRow = array();

            foreach ( $row_items as $row_item ) {

                if(strlen($row_item->name) > 10){
                    $row_item->name = substr($row_item->name, 0, 8) . '...';
                }

                $otherItemsRow[] = $this->getComponentColumn(array(
                    $this->getComponentRow(array(
                        $this->getComponentImage("Bayern.png", array(),
                            array(
                            'width' => '100%',
                            'height' => $this->screen_width / 3.3,
                            'crop' => 'yes',
                            'border-radius' => '3',
                        ))
                    ), array(), array(
                        'width' => '100%',
                        'height' => $this->screen_width / 3.3,
                    )),
                    $this->getComponentRow(array(
                        $this->getComponentText($row_item->name, array(), array(
                            'color' => '#000000',
                            'margin' => '0 0 0 0',
                            'padding' => '3 0 0 0',
                        ))
                    ), array(), array(
                        'width' => '100%',
                        'text-align' => 'center',
                    ))
                ), array(), array(
                    'width' => $this->screen_width / 3.3,
                    'padding' => '0 3 0 3',
                ));

            }

            $output_rows[] = $this->getComponentRow($otherItemsRow, array(), array(
                'margin' => '20 15 20 15',
                'width' => 'auto'
            ));

        }

        if ($output_rows) {
            $this->layout->scroll[] = $this->getComponentSwipe($output_rows);
        }
    }
}