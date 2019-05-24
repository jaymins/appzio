<?php

Yii::import('application.modules.aegameauthor.models.*');
Yii::import('application.modules.aelogic.article.components.*');
Yii::import('application.modules.aelogic.packages.actionMobileregister.models.*');
Yii::import('application.modules.aelogic.packages.*.models.*');

class XmlMobileproperties extends MobilepropertiesController
{

    /** @var MobilepropertiesModel */
    public $propertyModel;

    private $action;
    private $actionconfig;
    private $db_offer_codes = array();

    public $branch_id;
    public $action_id;
    public $play_id;
    public $app_id;
    public $user_id;

    private $properties;

    /**
     * Initialize the property model
     */
    public function initModel() {
        $this->propertyModel = new MobilepropertiesModel();
        $this->propertyModel->game_id = $this->app_id;
        $this->propertyModel->play_id = $this->play_id;
        $this->propertyModel->factoryInit($this);
    }

    public function adminHook() {

        if ( !isset($_GET['control-importer']) ) {
            return false;
        }

        $mode = $_GET['control-importer'];

        $this->getCurrentAction();
        $this->getActionConfig();

        if ( !isset($this->actionconfig['user_play_id']) OR empty($this->actionconfig['user_play_id']) ) {
            return false;
        }

        $playid = $this->actionconfig['user_play_id'];
        
        if ( $mode == 'stop' ) {
            Aetask::deleteTask($playid, 'properties:updatexml', 'async');
        }

        if ( $mode == 'start' ) {

            Aetask::deleteTask($playid, 'properties:updatexml', 'async');

            $params = json_encode(array(
                'play_id' => $playid,
                'app_id' => $this->app_id,
            ));

            Aetask::registerTask($playid, 'properties:updatexml', $params, 'async', 0, 0, false, 1);
        }

        return true;
    }

    public function executeUpdate() {

        $this->initModel();

        $this->log( 'Properties model initialized ...' );

        // connect and login to FTP server
        $ftp_server = "ftp.expertagent.co.uk";
        $ftp_conn = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");
        ftp_login($ftp_conn, 'Represent', 'Vo).}.W0%z+j');

        if ( $ftp_conn ) {
            $this->log( 'FTP connection established ...' );
        }

        // Required if using from a console
        ftp_pasv($ftp_conn, true);

        $contents = ftp_nlist($ftp_conn, ".");
        $filename = end( $contents );

        ob_start();
        $result = ftp_get($ftp_conn, "php://output", $filename, FTP_BINARY);
        $data = ob_get_clean();

        if ( $result != 1 OR $result != true OR empty($data) ) {
            ftp_close($ftp_conn);
            return false;
        }

        $xml = simplexml_load_string($data);
        $xml_to_json = json_encode($xml);
        $json_to_array = json_decode($xml_to_json,TRUE);

        if ( !isset($json_to_array['branches']['branch']['properties']['property']) ) {
            ftp_close($ftp_conn);
            return false;
        }

        $this->log( 'Properties data is present into the file ...' );

        $this->properties = $json_to_array['branches']['branch']['properties']['property'];

        // Get all currently available property offer codes
        $this->getCurrentPrCodes();

        $this->insertProperties();

        // close connection
        ftp_close($ftp_conn);

        $this->log( 'FTP Connection closed!' );

        return true;
    }

    private function insertProperties() {

        $vars = array();
        $current_pr_codes = array();
        
        foreach ($this->properties as $i => $property) {

            // Only properties from the 'Residential Lettings' department will be imported
            if ( $property['department'] != 'Residential Lettings' ) {
                continue;
            }

            $vars['name'] = $property['advert_heading'];

            if ( isset($property['street']) AND is_string($property['street']) ) {
                $address = $property['street'];

                if ( isset($property['town']) AND is_string($property['town']) ) {
                    $address .= ', ' . $property['town'];
                }

                $vars['address'] = $address;
            }

            if ( isset($property['district']) AND !is_array($property['district']) ) {
                $vars['district'] = $property['district'];
            } else {
	            $vars['district'] = '';
            }

            $vars['full_address'] = $this->getFullAddress( $property );

            if ( isset($property['latitude']) AND isset($property['longitude']) ) {
                $vars['district_lat'] = $property['latitude'];
                $vars['district_lng'] = $property['longitude'];
            }

            if ( $price = $this->getPrice( $property ) ) {
                $vars['price_per_month'] = $price['price_per_month'];
                $vars['price_per_week'] = $price['price_per_week'];
            }

            $offer_code = false;
            if ( isset($property['property_reference']) ) {
                $offer_code = $property['property_reference'];
	            $current_pr_codes[] = $offer_code;
                $vars['offer_code'] = $offer_code;
            }

            if ( isset($property['main_advert']) ) {
                $vars['description'] = $this->getCleanText( $property['main_advert'] );
            } else if ( !isset($property['main_advert']) AND isset($property['advert2']) ) {
                $vars['description'] = $this->getCleanText( $property['advert2'] );
            }

            if ( isset($property['bedrooms']) ) {
                $vars['num_bedrooms'] = $property['bedrooms'];
            }

            if ( isset($property['bathrooms']) ) {
                $vars['num_bathrooms'] = $property['bathrooms'];
            }

            // To do: check how to manage this
            $vars['full_management'] = 1;

            $images = $this->getImages( $property );

            $db_date = time();

            // This should be passed as timestamp as the Insert method would take care for it
            if ( isset($property['availableFrom']) AND !empty($property['availableFrom']) ) {
                $date = str_replace('/', '-', $property['availableFrom'] );
                $db_date = strtotime( $date );
            }

            $vars['temp_selected_date'] = $db_date;

            if ( $property_type = $this->getPropertyType( $property ) ) {
                $vars['property_type'] = $property_type;
            }

            if (
                $property['furnished'] == 'Unknown' OR
                $property['furnished'] == 'Furnished' OR
                $property['furnished'] == 'Part Furnished'
            ) {
                $vars['feature_unfurnished'] = 0;
                $vars['feature_furnished'] = 1;
                $vars['furnished'] = 1;
            } else {
                $vars['feature_unfurnished'] = 1;
                $vars['feature_furnished'] = 0;
                $vars['furnished'] = 0;
            }

	        if ( isset($property['portalFee']) AND $property['portalFee'] ) {
		        $vars['tenant_fee'] = $property['portalFee'];
	        }

            $vars['tenancy_option_shortlet '] = 0;
            $vars['tenancy_option_longlet '] = 1;
            $vars['xml_property'] = 1;

            $this->log( 'Insertion started ...' );

            // This would revert the availability status of certain properties
	        $vars['available'] = 1;

	        $this->propertyModel->submitvariables = $vars;
            $this->propertyModel->saveSubmit('auto', $offer_code, $images);

            unset( $vars );

            $this->log( 'Insertion successful ...' );
        }

	    $missing_items  = array_values(array_diff($this->db_offer_codes, $current_pr_codes));
        if ( $missing_items ) {
        	$this->markPropertiesAsInactive( $missing_items );
        }

        return 'All done : ))';
    }

    private function getFullAddress($property) {

        $address = '';

//        if ( isset($property['house_number']) ) {
//            $address .= $property['house_number'] . ', ';
//        }

        $addr_props = array(
            'street', 'district', 'area', 'town', 'country', 'postcode'
        );

        foreach ($addr_props as $i => $prop) {

            if ( !isset($property[$prop]) ) {
                continue;
            }

            if ( is_array($property[$prop]) ) {
                continue;
            }

            $address .= $property[$prop];

            if ( ($i+1) < count($addr_props) ) {
                $address .= ', ';
            }
        }

        return $address;
    }

    private function getPrice($property) {

        if ( !isset($property['numeric_price']) ) {
            return false;
        }

        $price_week = round($property['numeric_price']);

        return array(
            'price_per_month' => ($price_week * 52) / 12,
            'price_per_week' => $price_week,
        );
    }

    private function getImages($property) {

        if ( !isset($property['pictures']['picture']) ) {
            return false;
        }

        $images = array();
        $count = 0;

        foreach ($property['pictures']['picture'] as $picture) {

            if ( !isset($picture['filename']) OR empty($picture['filename']) ) {
                continue;
            }

            $count++;

            if ( $count == 1 ) {
                $local_filename = $this->saveImage( $picture['filename'] );
                $images['propertypic'] = $local_filename;
            } else {
                $images['propertypic' . $count] = $picture['filename'];
            }

        }

        return json_encode($images);
    }

    private function getPropertyType($property) {

        if ( !isset($property['property_type']) OR empty($property['property_type']) ) {
            return false;
        }
        
        $type = strtolower( $property['property_type'] );

        if ( preg_match('~flat~', $type) ) {
            return 'flat';
        } else if ( preg_match('~house~', $type) ) {
            return 'house';
        } else {
            return 'room';
        }

    }

    public function saveImage( $image ) {
        if ( !$this->remoteFileExists($image) ) {
            return false;
        }

        $app_id = $this->app_id;
        
        if ( isset($_SERVER['DOCUMENT_ROOT']) AND !empty($_SERVER['DOCUMENT_ROOT']) ) {
            $base_path = $_SERVER['DOCUMENT_ROOT'] . dirname($_SERVER['PHP_SELF']);
        } else {
            $base_path = dirname(__FILE__);
	        $base_path = substr($base_path, 0, strpos($base_path, DIRECTORY_SEPARATOR . 'protected'));
        }

        $path = $base_path . '/documents/games/' . $app_id . '/original_images/';

        if ( !is_dir($path) ) {
            mkdir($path,0777);
        }

        $filename = basename($image);
        $new_file = $path . $filename;

        if ( !file_exists($new_file) ) {
            $is_copied = copy($image, $new_file);

//	        $p = new ImageMagick($new_file);
//	        $signature = $p->getSignature();

            if ( $is_copied ) {
                return $filename;
            } else {
                return false;
            }

        } else {
            return $filename;
        }

    }

    // Check if the Requested remote image exists
    private function remoteFileExists( $file ) {
        $url = @getimagesize($file);

        if ( is_array($url) ) {
            return true;
        }

        return false;
    }

    private function log( $message ) {
        echo $message . "\n";
    }

    private function getCurrentAction() {
        $action = Aeaction::model()->findByPk( $this->action_id );

        if ( empty($action) ) {
            return false;
        }

        $this->action = $action;

        return true;
    }

    private function getActionConfig() {

        if ( !isset($this->action->config) ) {
            return false;
        }

        $this->actionconfig = json_decode( $this->action->config, true );

        return true;
    }

	private function getCleanText( $text ) {
		return strip_tags(
			html_entity_decode(stripslashes(nl2br($text)),ENT_QUOTES,'UTF-8')
		);
	}

	private function getCurrentPrCodes() {
		$all_properties = $this->propertyModel->findAllByAttributes(array(
			'xml_imported' => 1
		));

		if ( empty($all_properties) ) {
			return false;
		}

		foreach ( $all_properties as $property ) {
			if ( empty($property->offer_code) ) {
				continue;
			}

			$this->db_offer_codes[] = $property->offer_code;
		}

		return true;
	}

	private function markPropertiesAsInactive( $missing_items ) {

		foreach ( $missing_items as $missing_offer_code ) {
			$property = $this->propertyModel->findByAttributes(array(
				'offer_code' => $missing_offer_code
			));

			if ( $property ) {
				$property->available = 0;
				$property->update();
			}

    	}

    	return true;
	}

}