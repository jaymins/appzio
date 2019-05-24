<?php

namespace packages\actionMarticles\themes\examples\Controllers;

use packages\actionMarticles\themes\uikit\Views\Categorylisting as ArticleView;
use packages\actionMarticles\themes\uikit\Models\Model as ArticleModel;
use ReflectionFunction;

class Controller extends \packages\actionMarticles\Controllers\Controller
{

    /* @var ArticleView */
    public $view;

    /* @var ArticleModel */
    public $model;
    public $title;

    public function __construct($obj)
    {
        parent::__construct($obj);

        $this->model->rewriteActionConfigField('background_color', '#ffffff');

        // $this->model->flushActionRoutes();
    }

    /**
     * This is the default action inside the controller. This function gets called, if
     * nothing else is defined for the route
     * @return array
     */
    public function actionDefault()
    {
        $this->model->sessionUnset('component');
        $doc = $this->getData();
        $data = [
            'modules'=>$doc['modules'],
            'components'=>$doc['components']
        ];
        return ['Mainview', $data];
    }

    private function getData($component_id = null){

        $dataFile = file_get_contents("protected/modules/aelogic/Bootstrap/documentation.json");
        $dataFileDecode = json_decode($dataFile, true);
        if ($component_id){

            foreach ($dataFileDecode['docs'] as $key=>$doc){
              if (isset($doc['trait'])&& $component_id==$doc['trait']){
                 return $doc;
              }
            }
        }
        $result['components'] = [];
        $result['modules'] = [];
        foreach ($dataFileDecode['hierarchy']['Components']['AppzioUiKit'] as $module => $row){
            $result['modules'][] = $module;
            if (is_array($row)){
               foreach ($row as $items){
                   $result['components'][$module][] =  trim($items,'.php');
               }
            }
        }

        return $result;
    }
    private function setID()
    {
       if($this->getMenuId('id')){
           $this->model->sessionSet('component',$this->getMenuId('id'));
           return $this->getMenuId('id');
       }elseif($this->model->sessionGet('component')){
           return $this->model->sessionGet('component');
       }else{
           return $this->model->sessionGet('component');
       }
    }
}