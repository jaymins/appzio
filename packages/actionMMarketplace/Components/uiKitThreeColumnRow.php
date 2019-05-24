<?php
namespace packages\actionMMarketplace\Components;

trait uiKitThreeColumnRow {

    private $content;

    /**
     * example
     * array(
     *  "image" => array(
     *      array (
     *          "type" => "image"
     *          "image" => "name of the image"
     *      )
     *  ),
     *  "details" => array(
     *      array (
     *          "type" => "text",
     *          "text" => "Bid title"
     *      )
     *      array (
     *          "type" => "text",
     *          "text" => "bid submited on 2018-21-03", (for status "Bid LOST")
     *          "style_suffix" => "bottom", (for status "status_lost") -> the style will be uikit_three_column_row_details_full_text_status_lost
     *      )
     *  ),
     *  "price" => array (
     *      array(
     *          "type" => "text",
     *          "text" => "Your price"
     *          "style_suffix" => "top_price"
     *      )
     *      array(
     *          "type" => "image_text",
     *          "image" => "the image for $",
     *          "text" => "1250"
     *      )
     *  )
     * )
     *
     * @param array $content
     * @param array $parameters
     * @return mixed
     */
    public function uiKitThreeColumnRow(array $content, $parameters = array()) {

        $this->content = $content;

        $default_params = array(
            'style' => 'uikit_three_column_row_container'
        );

        $default_params = array_merge($default_params, $parameters);

        return $this->getComponentRow(array_merge(
            $this->getImageColumn(),
            $this->getDescriptionColumn(),
            $this->getPriceColumn()
        ), $default_params);
    }

    private function getImageColumn() {

        if ( !isset($this->content['image']) OR empty($this->content['image']) ) {
            return [];
        }

        return array(
            $this->getComponentImage($this->content['image'], [
                'priority' => 9,
                'style' => 'uikit_three_column_row_image_full_image'
            ])
        );
    }

    private function getDescriptionColumn() {

        if ( !isset($this->content['details']) OR empty($this->content['details']) ) {
            return [];
        }

        $details = $this->content['details'];
        $data = [];

        foreach ($details as $detail) {

            if ( !isset($detail['type']) OR empty($detail['type']) ) {
                continue;
            }

            $type = $detail['type'];

            if ( $type == 'title' ) {
                $data[] = $this->getComponentText($detail['text'], array(
                    'style' => 'uikit_three_column_row_details_full_text'
                ));
            } elseif ( $type == 'description' ) {
                $data[] = $this->getComponentText($detail['text'], array(
                    'style' => 'uikit_three_column_row_details_full_text_bottom'
                ));
            } elseif ( $type == 'status' ) {

                $state = $detail['status_state'];
                $state_style = ( $state == 'won' ? 'uikit_three_column_row_details_full_text_status_won' : 'uikit_three_column_row_details_full_text_status_lost' );

                $data[] = $this->getComponentRow([
                    $this->getComponentText($detail['text'], array(
                        'style' => $state_style
                    ))
                ], [], [
                    'text-align' => 'left'
                ]);
            }

        }

        return array(
            $this->getComponentColumn($data, [], [
                'width' => 'auto',
                'padding' => '0 5 0 0',
                'vertical-align' => 'middle',
            ])
        );
    }

    private function getPriceColumn() {

        if ( !isset($this->content['price']) OR empty($this->content['price']) ) {
            return [];
        }

        $details = $this->content['price'];
        $data = [];

        foreach ($details as $detail) {

            if ( !isset($detail['type']) OR empty($detail['type']) ) {
                continue;
            }

            $type = $detail['type'];

            if ( $type == 'text' ) {
                $data[] = $this->getComponentText($detail['text'], array(
                    'style' => 'uikit_three_column_row_price_full_text'
                ));
            } elseif ( $type == 'image_text' ) {
                $data[] = $this->getComponentRow([
                    $this->getComponentImage($detail['image'], array(
                        'priority' => 9,
                        'style' => 'uikit_three_column_row_price_half_image'
                    )),
                    $this->getComponentText($detail['text'], array(), array(
                        'parent_style' => 'uikit_three_column_row_price_half_text',
                        'font-size' => ( strlen($detail['text']) > 4 ? '16' : '22' ),
                    ))
                ], [
                    'style' => 'uikit_three_column_row_price_column'
                ]);
            }

        }

        return array(
            $this->getComponentColumn($data, [], [
                'width' => 'auto',
                'text-align' => 'right',
            ])
        );
    }

}