<?php

namespace Bootstrap\Components\ClientComponents;
use Bootstrap\Views\BootstrapView;

trait Image {

    /**
     *
     * Returns an image for the view. Feed it with a filename (can be from variable or images directory). Note that
     * the filename which gets exposed to client is not the same that the one in the actions images directory.
     *
     * @param $content string, filename or url
     * @param array $parameters selected_state, variable, onclick, style, image_fallback (when clicked, change to this image), selected_state,
     * lazy (loads after view), tap_to_open, tap_image (image file name)
     * @param array $styles  -- please see the link for more information about parameters [link] Bootstrap\Components\ComponentStyles
     * @return \stdClass
     * @example $this->layout->scroll[] = $this->getComponentImage('mybutton.png',array('onclick' => $onclick,'style' => 'btn_image');
     */

    public function getComponentImage(string $filename, array $parameters=array(),array $styles=array()) {
        /** @var BootstrapView $this */


        if(isset($parameters['use_filename']) AND $parameters['use_filename'] == 1){
            $file = $filename;
        } else {
            $file = $this->getImageFileName($filename,$parameters);
        }

        // Check if $filename is an external URL
        if ( empty($file) ) {
            if (filter_var($filename, FILTER_VALIDATE_URL) !== false) {
                $file = $filename;
            }
        }

        if($file){
            $obj = new \StdClass;
            $obj->type = 'image';
            $obj->content = $file;

            $obj = $this->attachStyles($obj,$styles);
            $obj = $this->attachParameters($obj,$parameters);
            $obj = $this->configureDefaults($obj);

            return $obj;
        } else {
            if (SERVER_ENVIRONMENT_DEVELOPMENT === false) {
                $this->setError('Image not found '.$filename);
                return $this->getComponentText('');
            } else {
                return $this->getComponentText('Image not found '.$filename);
            }
        }

    }

}