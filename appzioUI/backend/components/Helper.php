<?php

namespace backend\components;

use backend\modules\fitness\models\AeGame;
use Yii;
use yii\web\UploadedFile;

class Helper
{

    public static function isAssoc(array $arr)
    {
        if (array() === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    public static function getPic($value)
    {

        if (preg_match('~http~', $value)) {
            return $value;
        }

        if (!preg_match('~user_original_images~', $value)) {
            $value = '/documents/games/' . Yii::$app->session['app_id'] . '/user_original_images/' . $value;
        }

        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $value)) {
            return 'http://www.realcommercial.com.au/building/assets/images/avatar_placeholder.png';
        }

        $url_path = \Yii::$app->params['rootPath'] . $value;
        return $url_path;
    }

    private static function customMenuConfig($app_id){

        $keyname = 'custom_config_'.$app_id;

        if(isset($_POST['fieldset']) AND $_POST['fieldset'] == 'default') {
            Yii::$app->session->remove($keyname);
            return false;
        }

        $root = \Yii::getAlias('@backend');
        $path = $root . '/config/fieldsets/';

        if(isset($_POST['fieldset'])){
            if(file_exists($path.$_POST['fieldset'])){
                $configs = file_get_contents($path.$_POST['fieldset']);
                Yii::$app->session->set('custom_config_'.$app_id, $_POST['fieldset']);
                return $configs;
            }
        } elseif(Yii::$app->session->get($keyname)){
            $file = Yii::$app->session->get($keyname);
            $configs = file_get_contents($path.$file);
            return $configs;
        }

        return false;

    }

    /*
    config file is determined in the following order:
        - user selection
        - mapping table which is based on api_key
        - userconfigs (not used anymore)
        - default
    */

    public static function getConfigFile($app_id = false)
    {

        if (empty($app_id)) {
            $app_id = Yii::$app->session['app_id'];
        }

        $custom = self::customMenuConfig($app_id);

        if($custom){
            return json_decode($custom,true);
        }

        if($app_id){
            $app = AeGame::findOne(['id' => $app_id]);
            $root = \Yii::getAlias('@backend');
            $path = $root . '/config/';
            $aliases = file_get_contents($path.'fieldset_mapping.json');
            $aliases = @json_decode($aliases,true);

            foreach ($aliases as $alias){
                if($alias['api_key'] == $app->api_key){
                    $file = $path.'fieldsets/'.$alias['fieldset'];
                    $configs = @file_get_contents($file);
                    if($configs){
                        Yii::$app->session->set('custom_config_'.$app_id, $alias['fieldset']);
                        return json_decode($configs,true);
                    }
                }
            }
        }

        $root = \Yii::getAlias('@backend');
        $path = $root . '/config/userconfigs/';

        if (isset(Yii::$app->session['config_id']) AND file_exists($path . Yii::$app->session['config_id'] . '.json')) {
            $configs = file_get_contents($path . Yii::$app->session['config_id'] . '.json');
        } else if ($app_id AND file_exists($path . $app_id . '.json')) {
            $configs = file_get_contents($path . $app_id . '.json');
        } else {
            $configs = file_get_contents($path . 'default-config.json');
        }

        if (empty($configs)) {
            die('Configuration file error!');
        }

        return json_decode($configs, true);
    }

    /*
    * Configure the filter variables here
	* The system should be able to return different variables based on the selected app
    */
    public static function getVariablesConfig()
    {
        $configs = self::getConfigFile();

        if (isset($configs['filter_vars']) AND !empty($configs['filter_vars'])) {
            return $configs['filter_vars'];
        }

        // Missing configs
        return array();
    }

    public static function getMenusConfig()
    {
        $configs = self::getConfigFile();

        if (isset($configs['menus']) AND !empty($configs['menus'])) {
            return $configs['menus'];
        }

        // Missing configs
        return array();
    }

    public static function getThemeListing(){

        $root = \Yii::getAlias('@backend');
        $path = $root . '/config/fieldsets/';
        $ouput = array();

        foreach(scandir($path) AS $file){
            if(stristr($file, '.json')){
                $ouput[] = $file;
            }

        }

        return $ouput;
    }

    public static function getUploadPath()
    {

        $configs = self::getConfigFile();

        if (isset($configs['upload_path'])) {
            return $configs['upload_path'];
        }

        return false;
    }

    public static function addImageUploads($model, $control_name)
    {
        $image_files = array(
            'photo', 'image', 'icon', 'background_image'
        );

        foreach ($image_files as $image_file) {

            if (!isset($_FILES[$control_name]['name'][$image_file]) OR empty($_FILES[$control_name]['name'][$image_file])) {
                continue;
            }

            $destination = Helper::getUploadPath();

            if ($destination) {
                $file = UploadedFile::getInstance($model, $image_file);
                copy($file->tempName, $destination . $file->name);

                $model->{$image_file} = $file->name;

            }

        }

        return $model;
    }

    public static function getUploadURL()
    {

        $configs = self::getConfigFile();

        if (isset($configs['upload_url'])) {
            return $configs['upload_url'];
        }

        return false;
    }

    public static function getArticlePreview($model)
    {
        if (empty($model->content) OR !@json_decode($model->content, true)) {
            return 'No content yet';
        }

        $data = json_decode($model->content, true);

        ob_start();

        foreach ($data as $entry) {

            $type = $entry['type'];

            ?>

            <div class="preview-entry <?php echo $type ?>">
                <h5>Field type: <strong><?php echo $type; ?></strong></h5>

                <?php if (($type === 'text' OR $type === 'notes_box' OR $type === 'template') AND !is_array($entry['content'])) :

                    $styles = '';

                    if (isset($entry['styles']) AND !empty($entry['styles'])) {
                        $styles = Helper::getStyles($entry['styles']);
                    }

                    ?>

                    <?php echo '<p style="' . $styles . '">' . $entry['content'] . '</p>'; ?>

                    <?php if (isset($entry['params']['link'])) : ?>

                    <em>Links to: <?php echo Helper::getLink($entry['params']['link']); ?></em>

                <?php endif; ?>

                <?php elseif (($type == 'richtext' OR $type == 'wraprow') AND is_array($entry['content'])): ?>

                    <p class="article-rich-content">

                        <?php foreach ($entry['content'] as $item) :
                            $styles = '';

                            if (isset($item['styles']) AND !empty($item['styles'])) {
                                $styles = Helper::getStyles($item['styles']);
                            }

                            ?>

                            <small style="<?php echo $styles; ?>"><?php echo $item['content']; ?></small>

                            <?php if (isset($item['params']['link'])) : ?>

                            <br/><em>Links to: <?php echo Helper::getLink($item['params']['link']); ?></em>

                        <?php endif; ?>

                        <?php endforeach; ?>

                    </p>

                <?php elseif ($type == 'image' AND isset($entry['image_id']) AND $entry['image_id']): ?>

                    <p>Image ID: <?php echo $entry['image_id']; ?></p>

                <?php elseif ($type == 'video' AND isset($entry['video_link']) AND $entry['video_link']):
                    $link = $entry['video_link'];
                    ?>

                    <p>Video URL: <a target="_blank" href="<?php echo $link; ?>"><?php echo $link; ?></a></p>

                <?php elseif ($type == 'gallery' AND isset($entry['ref']) AND $entry['ref']): ?>

                    <p>Image set reference: <?php echo $entry['ref']; ?></p>

                <?php endif; ?>

            </div>

            <?php
        }

        return ob_get_clean();
    }

    private static function getStyles($styles)
    {

        $output = '';

        foreach ($styles as $prop => $value) {

            if (is_numeric($value))
                $value = $value . 'px';

            $output .= $prop . ': ' . $value . '; ';
        }

        return $output;
    }

    private static function getLink($link)
    {

        if (stristr($link, 'action')) {
            return 'action <strong>' . str_replace('action:', '', $link) . '</strong>';
        }

        return $link;
    }

    public static function adjustWidgetFields(array $view_fields, array $allowed_fields)
    {
        $result = [];
        $fields = self::getAllowedFields($allowed_fields);

        foreach ($view_fields as $view_field) {
            if (is_array($view_field)) {
                $field_type = $view_field['attribute'];
                if (in_array($field_type, $fields)) {
                    $result[] = $view_field;
                }
            } else {
                if (in_array($view_field, $fields)) {
                    $result[] = $view_field;
                }
            }
        }

        return $result;
    }

    public static function getFieldClass(string $field_name, array $allowed_fields)
    {

        $fields = self::getAllowedFields($allowed_fields);

        if (!in_array($field_name, $fields)) {
            return 'hidden-input-field';
        }

        return 'regular-field';
    }

    public static function getAllowedFields(array $allowed_fields)
    {
        $fields = [];

        foreach ($allowed_fields as $field_key => $field_value) {
            if (is_int($field_key)) {
                $fields[] = $field_value;
            } else {
                $fields[] = $field_key;
            }
        }

        return $fields;
    }

}