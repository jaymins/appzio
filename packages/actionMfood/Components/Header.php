<?php

namespace packages\actionMfood\Components;

trait Header
{
    /**
     * @param array $content [
     * 'icon',
     * 'title',
     * 'floating_element',
     * 'content',
     * 'tabs',
     * 'links']
     * @param array $params
     * @param array $styles
     * @return array
     */
    public function getHeader(array $content = [], array $params = [], array $styles = [])
    {
        $title = $this->addParam('title', $content, false);
        $description = $this->addParam('description', $content, false);
        $icon = $this->addParam('icon', $content, false);
        $floating_element = $this->addParam('floating_element', $content, false);
        $tabs = $this->addParam('tabs', $content, false);
        $links = $this->addParam('links', $content, false);
        $content_element = $this->addParam('content', $content, false);

        $style = [
            'width' => '100%',
            'min-height' => '100',
        ];

        $title_content = [];

        if ($description) {
            $header[] = $this->getComponentText($description, [], [
                'font-size' => 18,
                'font-weight' => 'bold',
                'color' => '#999999',
                'padding' => '25 10 0 20',
                'width' => 'auto'
            ]);
        }

        if ($icon) {
            $title_content[] = $this->getComponentImage($icon, [], [
                'width' => '10%'
            ]);
        }

        if ($title) {
            $title_content[] = $this->getComponentText($title, [], [
                'font-size' => 35,
                'font-weight' => 'bold',
                'color' => $this->color_top_bar_text_color,
                'padding' => '10 10 10 0',
                'width' => '70%'
            ]);
        }

        if ($floating_element) {
            $element = $floating_element;
            $title_content[] = $this->getComponentImage(
                $element['icon'],
                $element['action'],
                $element['style']
            );
        }

        $header[] = $this->getComponentRow($title_content, [], [
            'vertical-align' => 'middle',
            'padding' => '15 20 15 20'
        ]);

        if ($tabs && $params['activeTab']) {
            $header[] = $this->getTabs($tabs, $params['activeTab'], []);
        }

        if ($links && !$tabs && !$content_element) {
            $header[] = $this->getLinks($links);
        }

        if ($content_element) {
            $header[] = $content_element;
        }

        if (isset($styles['background-image'])) {
            $style['background-image'] = $this->getImageFileName($styles['background-image'], [
                'imgwidth' => '1366',
                'imgheight' => '768',
            ]);
            $style['background-size'] = 'cover';
        }

        return $this->getComponentColumn($header, [], $style);
    }

    /**
     * @param $tabNavigation
     * @param $active_tab
     * @return mixed
     */
    private function getTabs($tabNavigation, $active_tab)
    {
        $styles = array(
            'background-color' => 'transparent',
            'border-color' => 'transparent',
            'active_tab_color' => $this->color_top_bar_text_color,
            'tab_color' => '#000099',
            'width' => '100%',
            'border-width' => '0 !important',
            'vertical-align' => 'middle',
            'font-size' => 18,
            'color' => $this->color_top_bar_text_color
        );

        $active_tab = $active_tab - 1;
        $tabNavigation[$active_tab]['active'] = true;
        return $this->getComponentRow([
            $this->uiKitTabNavigation($tabNavigation, [], $styles)
        ], [], $styles);
    }

    /**
     * @param array $links
     * @return array
     */
    public function getLinks(array $links)
    {

        $items = array_chunk($links, 2);
        $content = [];

        foreach ($items as $links_array) {
            $row_data = [];

            foreach ($links_array as $i => $link) {
                $column_data = [];

                if ($link['icon']) {
                    $column_data[] = $this->getComponentImage($link['icon'], [
                        'onclick' => $link['onclick']
                    ], [
                        'height' => '18',
                    ]);
                }

                $column_data[] = $this->getComponentText(strtoupper($link['text']), [
                    'onclick' => $link['onclick']
                ], [
                    'font-size' => 16,
                    'padding' => '0 15 0 15',
                    'color' => $this->color_top_bar_text_color
                ]);

                $row_data[] = $this->getComponentColumn([
                    $this->getComponentRow($column_data, [], [
                        'vertical-align' => 'middle',
                    ])
                ], [], [
                    'width' => '50%',
                    'padding' => '15 15 15 15',
                    'text-align' => ($i ? 'right' : 'left'),
                ]);
            }

            $content[] = $this->getComponentRow($row_data);
        }

        return $this->getComponentRow([
            $this->getComponentColumn($content)
        ], [], [
            'padding' => '0 5 0 5',
            'vertical-align' => 'middle',
        ]);

    }

}