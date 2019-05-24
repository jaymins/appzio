<?php


namespace packages\actionMarticles\themes\examples\Models;
use packages\actionMarticles\Models\Model as BootstrapModel;


class Model extends BootstrapModel {
    private $file;
    private $dataBuilder;
    public function __construct($obj)
    {
        $file = '/modules/aelogic/Bootstrap/documentation.json';
        $dataBuilder = new Builder($file);

        parent::__construct($obj);
    }

    private function getData(){
        $this->
        $data = [];
        return $data;
    }
    public function getListComponents(){
        $list = [];
        $list = $this->getData();
        return $list;
    }
    public function getComponent($id){


        if($id){

        }

        $components = [];
        return $components;
    }
}
class Builder implements getDataInterface{

    public function fileData(string $file){

        return  file_get_contents($file);
    }
}
interface getDataInterface{
    function fileData(string $file);
};

