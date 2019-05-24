<?php

/**
 * This is the model class for table "ae_game".
 *
 * The followings are the available columns in table 'ae_game':
 * @property string $id
 * @property string $user_id
 * @property string $category_id
 * @property string $name
 * @property integer $active
 * @property string $icon
 * @property string $length
 * @property integer $timelimit
 * @property string $description
 *
 * The followings are the available model relations:
 * @property User $user
 * @property AeCategory $category
 * @property AeGameRole[] $aeGameRoles
 * @property AeGameUser[] $aeGameUsers
 * @property Aebranch[] $aeLevels
 */
Yii::import('application.modules.aelogic.packages.*');
Yii::import('application.modules.aelogic.packages.actionMobileplaces.models.*');
Yii::import('application.modules.aelogic.packages.actionMobileplaces.widgets.*');

class MobileplacesStatistics extends Controller
{

    public $templatepath = '../modules/aelogic/packages/actionMobileplaces/templates/';
    public $baseurl;
    public $uploadpath;
	public $actiondata;
	public $configdata;

    public $gid;    // game id, set by the game author
    public $aid;    // action id

      // objects
    public $diarymain;
    public $diarywidget;

    public $masterclass = 'MobileplacesModel';
    public $html;
    public $aelevel;
    public $gamedata;
    public $levelsdata;
    public $branchcount;
    public $aegamerole;


    public function renderStatistics(){

        if(isset($_REQUEST['tempfile'])){
            return $this->handleImporting();
        }

        if($_FILES AND isset($_FILES['import']['tmp_name'])){
            return $this->handleUpload();
        }

        $output = Yii::app()->mustache->GetRender($this->templatepath.'uploadform',array('bits' => $this->html));
        return $output;

    }

    public function uploadForm(){
        return Yii::app()->mustache->GetRender($this->templatepath.'uploadform',array('bits' => $this->html));
    }

    public function handleUpload(){

        $content = @file_get_contents($_FILES['import']['tmp_name']);
        $linefeed = Controller::detectEol($content);
        $data = explode($linefeed,$content);
        $datalines = '';
        $count = 0;

        if(empty($data)){
            return true;
        }

        foreach ($data as $line) {
            $datalines .= '<tr>';
            $count++;

            if (!empty($data)) {
                $linedata = explode(';', $line);

                foreach ($linedata as $linedataitem) {
                    $datalines .= '<td>' . $linedataitem . '</td>';
                }

            }
            $datalines .= '</tr>';

            if ($count == 4) {
                break;
            }
        }

        $datalines .= '<tr>';
        $count = 0;
        foreach ($linedata as $control){
            $datalines .= '<td>' .$this->getSelector($control,$count). '</td>';
            $count++;
        }
        $datalines .= '</tr>';


        $datalines .= '<tr>';
        $count = 0;

        foreach ($linedata as $control){
            $datalines .= '<td>' .$this->getNameingfield($control,$count). '</td>';
            $count++;
        }
        $datalines .= '</tr>';
        $output = Yii::app()->mustache->GetRender($this->templatepath.'importer',array('data' => $datalines,'tempfile' => $content));
        return $output;
    }

    public function getSelector($data,$count){

        $fieldnames = array('no import','lat','lon','name','address','zip','city','county','country','CUSTOM');

        $html = '<select name="selector_'.$count.'" style="width:150px;">';

        foreach ($fieldnames as $field){
            $html .= '<option value="'. $field .'">'.$field.'</option>';
        }

        $html .= '</select';
        return $html;
    }

    public function getNameingfield($data,$count){
        return 'custom field title<br><input type="text" name="customfield_' .$count .'" style="width:150px;">';
    }


    public function handleImporting(){
        $content = $_REQUEST['tempfile'];
        $linefeed = Controller::detectEol($content);
        $data = explode($linefeed,$content);
        $count = 0;
        $time = time();

        foreach ($data as $line){
            $count++;

            if($count == 1 AND isset($_REQUEST['first_line_header']) AND $_REQUEST['first_line_header'] == 'on'){
                continue;
            }

            $columncount = 0;
            $linedata = explode(';', $line);
            $obj = new MobileplacesModel();

            foreach ($linedata as $column){
                $selectorname = 'selector_' .$columncount;
                $fieldname = 'customfield_' .$columncount;
                $customvalue = $_REQUEST[$fieldname];
                $selectorvalue = $_REQUEST[$selectorname];

                if($selectorvalue != 'CUSTOM' AND $selectorvalue != 'no import'){
                    $obj->$selectorvalue = $column;
                } else {
                    $config[$customvalue] = $column;
                }

                $columncount++;
            }

            if(isset($config)){
                $obj->info = json_encode($config,JSON_UNESCAPED_UNICODE);
            }

            $obj->game_id = $this->gid;
            $obj->import_date = $time;
            if(isset($_REQUEST['do_geo_address_translation']) AND $_REQUEST['do_geo_address_translation'] == 'on'){
                $coordinates = ThirdpartyServices::addressToCoordinates($this->gid,$obj->country,$obj->city,$obj->address);
                if(isset($coordinates['lat']) AND isset($coordinates['lon'])){
                    $obj->lat = $coordinates['lat'];
                    $obj->lon = $coordinates['lon'];
                }
            }
            $obj->insert();
            unset($config);
        }

        return 'Imported ' .$count .' rows. Let\'s hope everything went ok';

    }



}