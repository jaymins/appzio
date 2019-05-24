<?php

/**
 * Default model for the action. It has access to all data provided by the BootstrapModel.
 *
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMgallery\Models;
use Bootstrap\Models\BootstrapModel;

class Model extends BootstrapModel {

    /**
     * This variable doesn't actually need to be declared here, but but here for documentation's sake.
     * Validation erorr is an array where validation errors are saved and can be accessed by controller,
     * view and components.
     */
    public $validation_errors;
    public $output;
    public $editId;

    public function saveImages(){
        if($this->getSavedVariable('progress_photo')){
            $obj = new GalleryImageModel();
            $obj->play_id = $this->playid;
            $obj->image = $this->getSavedVariable('progress_photo');
            $obj->insert();
            $this->deleteVariable('progress_photo');
        }
    }

    public function getImages(){
        $output = GalleryImageModel::model()->findAllByAttributes(['play_id' => $this->playid]);

        if(!$output){
            return array();
        }

        $output = array_reverse($output);

        return $output;
    }

    public function deleteImage($id){
        @GalleryImageModel::model()->deleteAllByAttributes(['id' => $id,'play_id'=>$this->playid]);
    }

    public function updateImage($id)
    {
        $obj = GalleryImageModel::model()->findByAttributes(['play_id' => $this->playid,'id' => $id]);
        if($obj){
            $obj->image = $this->getSavedVariable('newimage');
            $obj->update();
        }

        $this->deleteVariable('newimage');
    }


}