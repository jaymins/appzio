<?php

Yii::import('application.modules.aelogic.Bootstrap.Models.*');
Yii::import('application.modules.aeapi.models.*');

/**
 * Class BootstrapMigrations
 *
 * Action specific migrations extend this class. The function runModuleMigrations is what
 * the migration manager is looking for. Log and error are shown on the web interface
 * and / or emailed.
 *
 */

class BootstrapMigrations extends Migrations implements BootstrapMigrationsInterface
{

    public $action = '';
    public $shortname = '';
    public $path;

    public $title;
    public $description;
    public $icon;


    public function __construct()
    {
        $this->action = str_replace('Migrations', '', get_class($this));
        $this->shortname = substr($this->action,6);
        $this->path = Yii::getPathOfAlias('application.modules.aelogic.packages.'.$this->action.'.Migrations');
    }

    /**
     * This function is called by the migration manager and should include calls to all
     * needed migration functions.
     * @return bool
     */
    public function runModuleMigrations(){
        return true;
    }


    /**
     * Runs migrations from an sql file inside the Migrations directory of
     * your action. Will replace {{app_id}} with the ID of the app when
     * run from the web.
     * @param $filename
     * @return bool
     */
    public function runMigrationFromFile($filename){
        $sql = @file_get_contents($this->path.DS.$filename);

        if(!$sql){
            self::$errors[] = $filename .' not found from action '.$this->action;
            return false;
        }

        if(isset($_REQUEST['gid'])){
            $sql = str_replace('{{app_id}}', $_REQUEST['gid'], $sql);
        } elseif(stristr($sql, '{{app_id}}')) {
            self::$errors[] = $filename .' has {{app_id}} content and should be run from the web only. Action: '.$this->action;
            return false;
        }
        
        if(!stristr($sql, 'START TRANSACTION')){
            $sql = 'START TRANSACTION;'.$sql.'COMMIT;';
        }

        self::$log[] = 'Ran migration for '.$filename;
        @Yii::app()->db->createCommand($sql)->query();
        return true;
    }


    /**
     * This is run automatically
     * @return bool
     */
    public function helperAddAction(){
        if(self::helperActionExists($this->shortname)){
            return false;
        }

        if(!$this->title){
            return false;
        }

        $title = $this->title;
        $icon = $this->icon ? $this->icon : 'new.png';
        $description = $this->description ? $this->description : '';

        $sql = "
          INSERT INTO `ae_game_branch_action_type` (`title`, `icon`, `shortname`, `id_user`, `description`, `version`, `channels`, `uiformat`, `active`, `global`, `githubrepo`, `adminfeedback`, `requestupdate`, `uses_table`, `has_statistics`, `has_export`, `invisible`, `hide_from_api`, `ios_supports`, `android_supports`, `web_supports`, `article_view`,`library`) VALUES
          ('$title', '$icon', '$this->shortname', 1, '$description', '1', '', 'native', 1, 1, '', '', 0, 0, 0, 0, 0, 0, 1, 1, 0, 1,'PHP2');
        ";

        @Yii::app()->db->createCommand($sql)->query();
        self::$log[] = 'Added action '.$this->shortname;

    }


    /**
     * For removing an existing relation. Not always 100% reliable if you have
     * duplicate relations.
     *
     * @param $table
     * @param $relation
     */
    public static function helperDropRelation($table, $relation)
    {
        $sql = "START TRANSACTION;
                ALTER TABLE $table DROP FOREIGN KEY $relation;
                COMMIT;";
        @Yii::app()->db->createCommand($sql)->query();
    }

    /**
     * Delete table. I hope you know what you are doing.
     * @param $table_name
     */
    public static function helperDropTable($table_name)
    {
        $sql = "DROP TABLE IF EXISTS {$table_name}";
        Yii::app()->db->createCommand($sql)->query();
    }


    /**
     * In some cases you might need to know the name of relation. This is a helper for finding that out.
     * @param $table
     * @param $target
     * @return bool
     */
    public static function helperGetRelationName($table, $target)
    {
        if (!self::helperTableExists($table)) {
            return false;
        }

        $sql = "SHOW CREATE TABLE $table";
        $createtable = @Yii::app()->db->createCommand($sql)->queryAll();

        if (!isset($createtable[0]['Create Table'])) {
            return false;
        }

        $constraints = explode('CONSTRAINT', $createtable[0]['Create Table']);

        foreach ($constraints as $constraint) {
            if (stristr($constraint, 'REFERENCES')) {
                $line = explode('`', $constraint);
                $name = $line[1];
                $external_table = $line[5];
                if ($external_table == $target) {
                    return $name;
                }
            }
        }

        return false;
    }

    /**
     * Check whether relation exists. Unfortunately not 100% reliable in all cases.
     * @param $table
     * @param $column
     * @param $target
     * @return bool
     */
    public static function helperRelationExists($table, $column, $target)
    {
        Yii::app()->db->schema->refresh();
        $sc = Yii::app()->db->schema->getTable($table);

        if (isset($sc->foreignKeys)) {
            $keys = $sc->foreignKeys;
            if (isset($keys[$column])) {
                if (isset($keys[$column][0][$target])) {
                    return true;
                }
            }
        }

        return false;

    }

    /**
     * Check whether given table exists.
     * @param $tablename
     * @param bool $debug
     * @return bool
     */
    public static function helperTableExists($tablename, $debug = false)
    {
        Yii::app()->db->schema->refresh();
        Yii::app()->db->schema->getTables();

        $schema = Yii::app()->db->schema->tableNames;

        if (is_array($schema) AND in_array($tablename, $schema)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check whether column exists for a given table.
     * @param $column
     * @param $table
     * @return bool
     */
    public static function helperColumnExists($column, $table)
    {
        $schema = Yii::app()->db->schema->getTable($table, true);

        if (isset($schema->columns[$column])) {
            return true;
        }

        return false;
    }

    /**
     * Check whether action exists in the database
     *
     * @param $shortname
     * @return bool
     */
    public static function helperActionExists($shortname)
    {

        $sql = "SELECT * FROM ae_game_branch_action_type
 				  WHERE shortname = '$shortname'
 				";

        $result = @Yii::app()->db->createCommand($sql)->queryAll();

        if ($result) {
            return true;
        }

        return false;
    }




}