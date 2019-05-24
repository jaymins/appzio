<?php

namespace backend\modules\tickers\controllers;

use backend\controllers\CrudBaseController;
use backend\modules\tickers\models\AeExtTicker;
use backend\modules\tickers\search\AeExtTicker as AeExtTickerSearch;
use dmstr\bootstrap\Tabs;
use Yii;
use yii\helpers\Json;
use yii\caching\Cache;

/**
* This is the class for controller "AeExtTickerController".
*/
class AeExtTickerController extends \backend\modules\tickers\controllers\base\AeExtTickerController
{

    public $endpoint = 'https://eodhistoricaldata.com/api/exchanges/';
    public $api_key = '5c078c9206fa25.45625653';

    private $exchange_id;
    private $sort_letters;
    private $ticker_data;

    /**
     * Creates a new AeExtTicker model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AeExtTicker;

        try {

            $this->exchange_id = 'us';

            if ( \Yii::$app->request->isPost AND $model->load($_POST) ) {
                
                $this->ticker_data = $this->findTickerData( $model->ticker );

                if ( empty($this->ticker_data) ) {
                    $msg = 'Ticker "' . $model->ticker . '" doesn\'t exist in our database';
                    $model->addError('_exception', $msg);

                    return $this->render('create', [
                        'model' => $model,
                    ]);
                }
                
                $model->company = $this->getTickerField('Name');
                $model->exchange = $this->getTickerField('Exchange');
                $model->exchange_name = $this->getTickerField('Exchange');
                $model->currency = $this->getTickerField('Currency');

                if ( $model->save() ) {
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    $model->load($_GET);
                }
            } else {
                $model->load($_GET);
            }

        } catch (\Exception $e) {
            $msg = (isset($e->errorInfo[2])) ? $e->errorInfo[2] : $e->getMessage();
            $model->addError('_exception', $msg);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionGettickers() {

        $request = Yii::$app->request;
        $this->exchange_id = $request->get('exchange_id');

        if ( empty($this->exchange_id) ) {
            return Json::encode([
                'output' => []
            ]);
        }

        $cache =  Yii::$app->cache;

        $this->setRealExchangeID();

        $key = 'exchange_cache_' . $this->exchange_id;
        $result = $cache->getOrSet($key, function () {
            $url = $this->endpoint . $this->exchange_id . '?api_token=' . $this->api_key . '&fmt=json';
            $result = @file_get_contents($url);
            return $result;
        }, 86400);

        $data = @json_decode( $result, true );

        if ( empty($data) ) {
            return Json::encode([
                'output' => []
            ]);
        }

        $output = [];

        $letter_options = [];

        if ( $this->sort_letters ) {
            $letter_options = $this->getLetterOptions();
        }

        foreach ($data as $item) {

            $name = ( $item['Name'] ? $item['Name'] : $item['Code'] );

            if ( $letter_options ) {

                foreach ($letter_options as $letter_option) {
                    if ( strpos($name, $letter_option) === 0 ) {
                        $output[$item['Code']] = [
                            'name' => $name,
                            'currency' => $item['Currency'],
                            'exchange' => ( $item['Exchange'] ? $item['Exchange'] : $this->exchange_id ),
                        ];
                    }
                }
                
            } else {
                $output[$item['Code']] = [
                    'name' => $name,
                    'currency' => $item['Currency'],
                    'exchange' => ( $item['Exchange'] ? $item['Exchange'] : $this->exchange_id ),
                ];
            }

        }

        // asort($output,SORT_STRING);

        return Json::encode([
            'output' => $output
        ]);
    }

    private function setRealExchangeID() {

        if ( !stristr($this->exchange_id, '::') ) {
            return false;
        }

        $pieces = explode('::', $this->exchange_id);
        
        $this->exchange_id = $pieces[0];

        if ( !isset($pieces[1]) )
            return false;
        
        $this->sort_letters = $pieces[1];

        return true;
    }

    private function getLetterOptions() {

        $options = [];

        switch ($this->sort_letters) {
            case '0-9':
                $options = [
                    '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'
                ];
                break;

            case 'a-e':
                $options = ['a', 'A', 'b', 'B', 'c', 'C', 'd', 'D', 'e', 'E'];
                break;

            case 'f-j':
                $options = ['f', 'F', 'g', 'G', 'h', 'H', 'i', 'I', 'j', 'J'];
                break;

            case 'k-o':
                $options = ['k', 'K', 'l', 'L', 'm', 'M', 'n', 'N', 'o', 'O', 'Ó', 'Ö', 'Ø'];
                break;

            case 'p-t':
                $options = ['p', 'P', 'q', 'Q', 'r', 'R', 's', 'S', 't', 'T'];
                break;

            case 'u-z':
                $options = ['u', 'U', 'v', 'V', 'w', 'W', 'x', 'X', 'y', 'Y', 'z', 'Z'];
                break;
        }

        return $options;
    }

    private function findTickerData( $ticker ) {

        $cache =  Yii::$app->cache;

        $result = $cache->getOrSet('exchange_cache_us', function () {
            $url = $this->endpoint . 'us?api_token=' . $this->api_key . '&fmt=json';
            $result = @file_get_contents($url);
            return $result;
        }, 86400);

        $result_indx = $cache->getOrSet('indx', function () {
            $url = $this->endpoint . 'indx?api_token=' . $this->api_key . '&fmt=json';
            $result = @file_get_contents($url);
            return $result;
        }, 86400);

        if ( empty($result) OR empty($result_indx) ) {
            return false;
        }

        $data_us = json_decode($result, true);
        $data_idx = json_decode($result_indx, true);

        $data = array_merge($data_us, $data_idx);

        foreach ($data as $entry) {
            $code = $entry['Code'];
            if ( $code == $ticker ) {
                return $entry;
            }
        }

        return false;
    }

    private function getTickerField($field) {

        if ( !isset($this->ticker_data[$field]) OR empty($this->ticker_data[$field]) )
            return false;

        return $this->ticker_data[$field];
    }

}