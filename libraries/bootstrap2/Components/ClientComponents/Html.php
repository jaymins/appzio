<?php

namespace Bootstrap\Components\ClientComponents;
use Bootstrap\Views\BootstrapView;

trait Html {

    /**
     * @param $content string, HTML formatted text. Note that this does not support all HTML notation and this is
     * one of the few exceptions where support differs between iOS and Android. On iOS only font size and color are
     * supported whereas Android uses a webview to render the content. This is due to performance, where Android is
     * much more performant on rendering html content. You can also apply normal appzio formatting for the entire text
     * block.
     * @param array $parameters selected_state, variable, onclick, style
     * <code>
     * $array = array(
     * 'selected_state' => 'style-class-name',
     * 'variable'   => 'variablename',
     * 'onclick' => $onclick, // this must be an object or an array of objects
     * 'style' => 'style-class-name',
     * );
     * </code>
     * @param array $styles -- please see the link for more information about parameters [link] Bootstrap\Components\ComponentStyles
     * @return \stdClass
     */

    public function getComponentHtml(string $content, array $parameters=array(),array $styles=array()) {
        /** @var BootstrapView $this */

		$obj = new \StdClass;
        $obj->type = 'msg-html';

        $obj = $this->attachStyles($obj,$styles);
        $obj = $this->attachParameters($obj,$parameters);
        $obj = $this->configureDefaults($obj);

        // special styling
        $content = $this->attachHtmlStyling($content,$styles);
        $obj->content = $content;

        return $obj;
	}

	private function attachHtmlStyling($content,$styles){

        $styling = '';

        $content = preg_replace('/(\s){2,}/s', '', $content);

        // add extra spacing between paragraphs
        $content = str_replace('</p><p>', '</p><p>', $content);

        if(isset($styles['font-size'])){
            $size = $styles['font-size'].'px';
            $styling .= "font-size:$size;";
        }

        if(isset($styles['font-ios']) AND $this->ios){
            $font = $styles['font-ios'];
            $styling .= "font-family:$font;";
        }

        if(isset($styles['font-ios']) AND $this->android){
            $font = $styles['font-android'];
            $styling .= "font-family:$font;";
        }

        //$content = str_replace('<strong>', '<strong>&nbsp;', $content);

        if($styling){
            $content = '<div style="'.$styling.'">'.$content.'</div>';
        }

        return $content;
    }

}