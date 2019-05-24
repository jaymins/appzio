<?php

namespace packages\actionMarticles\themes\examples\Views;

use ReflectionFunction;

class Articles extends Mainview
{

    public $layout;
    public $title;

    public $tab;
    private $documentation;

    public function tab1()
    {
        $this->layout = new \stdClass();
        $this->documentation = $this->getData('documentation', 'array');
        $item = $this->getData('documentation', 'array');
        $name = null;
        if (isset($item['methods'][$item['trait']]['name'])) {
            $name = $item['methods'][$item['trait']]['name'];
        }
        $this->layout->scroll[] = $this->components->HeaderComponent($name);
        $this->layout->scroll[] = $this->getTabNavigation(1);
        $this->layout->scroll[] = $this->getExampleView($name);
        $this->layout->overlay[] = $this->components->FullScreenBtn(3);

        return $this->layout;
    }

    public function tab2()
    {
        $this->layout = new \stdClass();

        $item = $this->getData('documentation', 'array');
        $name = $item['methods'][$item['trait']]['name'];

        $this->layout->scroll[] = $this->getHeader($name);
        $this->layout->scroll[] = $this->getTabNavigation(2);
        $this->layout->scroll[] = $this->getDocumentation();
        $this->layout->overlay[] = $this->components->BackBtn();
        return $this->layout;
    }

    public function tab3()
    {
        $this->layout = new \stdClass();

        $item = $this->getData('documentation', 'array');
        $name = null;
        if (isset($item['methods'][$item['trait']]['name'])) {
            $name = $item['methods'][$item['trait']]['name'];
        }
        $this->layout->scroll[] = $this->getComponentColumn([
            $this->getExampleView($name)
        ], [], [
            'vertical-align' => 'middle',
            'height' => $this->screen_height
        ]);

        $this->layout->overlay[] = $this->components->getComponentRow([
            $this->getComponentText('', [], [
                'width' => $this->screen_width,
                'height' => $this->screen_height
            ])
        ], [
            'onclick' => [
                $this->getOnclickTab(1)
            ]
        ], [
            'width' => $this->screen_width,
            'height' => $this->screen_height
        ]);
        return $this->layout;
    }

    private function getTabNavigation($tab = 1)
    {
        $tabs = [
            ['text' => 'Preview', 'onclick' => $this->getOnclickTab(1), 'active' => 0],
            ['text' => 'Documentation', 'onclick' => $this->getOnclickTab(2), 'active' => 0],
        ];
        $tabs[$tab - 1]['active'] = 1;

        return $this->getComponentRow([$this->components->uiKitTabNavigation($tabs)], [], [
            'padding' => '15 0 15 0'
        ]);
    }

    private function getHeader($title)
    {
        if (!$title) {
            $title = 'No example available';
        }
        return $this->components->HeaderComponent($title);
    }

    private function getExampleView($name)
    {
        if (!$name) {
            return $this->getComponentText('No example available');
        }
        $function_name = $name . 'UITEST';

        if ($name && method_exists($this->components, $function_name)) {

            return $this->components->$function_name();

        } else {
            return $this->getComponentText('No example available');
        }
    }

    private function getDocumentation()
    {

        $variables = $this->documentation['methods'][$this->documentation['trait']]['variables'];
        $summary = $this->documentation['doc_comment']['summary'];

        $doc [] = $this->getComponentRow([$this->getComponentText('Documentation', [], [

        ])], [], [
            'padding' => '15 0 15 0'
        ]);
        if ($summary) {
            $doc[] = $this->getComponentColumn([
                $this->getComponentText('Summary'),
                $this->getComponentText($summary),
            ], [], [
                'padding' => '15 0 15 0'
            ]);
        }

        if (is_array($variables) && count($variables)) {
            $list_variables = [];
            $list_variables[] = $this->getComponentText('Variables');
            foreach ($variables as $var) {
                $list_variables[] = $this->getComponentText($var);
            }
            $doc[] = $this->getComponentRow([
                $this->getComponentColumn($list_variables)
            ], [], [
                'padding' => '15 0 15 0'
            ]);
        }


        return $this->getComponentColumn($doc, [], [
            'padding' => '15 15 15 15'
        ]);
    }
}