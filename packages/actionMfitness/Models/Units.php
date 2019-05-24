<?php

/**
 * Default model for the action. It has access to all data provided by the BootstrapModel.
 *
 * @link http://docs.appzio.com/toolkit-section/models/
 */

namespace packages\actionMfitness\Models;
use Bootstrap\Models\BootstrapModel;

Trait Units
{
    public function swithcUnits()
    {


        if($this->getSubmittedVariableByName('units')){
            $this->saveVariable('units', $this->getSubmittedVariableByName('units'));
            return true;
        }

        if(!$this->getSavedVariable('units')){
            $this->saveVariable('units', 'imperial');
        } else {
            $current = $this->getSavedVariable('units');

            if($current == 'metric'){
                $this->saveVariable('units', 'imperial');
            } else {
                $this->saveVariable('units', 'metric');
            }

        }
    }


    public function convertUnits($unit,$value){

        switch($unit){
            case 'kg':
                $output['value'] = @round($value*2.20462,1);
                $output['unit'] = 'lbs';
                break;

            case 'l':
                $output['value'] = @round($value*1.76,1);
                $output['unit'] = 'pints';
                break;

            case 'dl':
                $output['value'] = @round($value*3.381,1);
                $output['unit'] = 'fl oz';
                break;

            case 'g':
                $output['value'] = @round($value*15.43,1);
                $output['unit'] = 'grains';
                break;

            default:
                $output['unit'] = $unit;
                $output['value'] = $value;
                break;
        }

        return $output;
    }



}