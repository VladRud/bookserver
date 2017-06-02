<?php

namespace common\helpers;

use Yii;
/**
 * Class DateHelper
 *
 * @copyright Copyright (c) 2015 MyCashBack Team
 * @author NIkadimas <pavlo.nikadimas@gmail.com>
 */
class DateHelper {

    /**
     * Get current datetime
     * 
     * @param string $format 
     * @param boolean $gmt 
     * @return string 
     */
    public static function getCurrentDateTime($format = 'Y-m-d H:i:s', $gmt = true) {
        if ($gmt) {
            return gmdate($format, time());
        }

        return date($format, time());
    }

    /**
     * Get GTM datetime 
     * 
     * @param int $timestamp 
     * @param string $format 
     * @return string 
     */
    public static function getGTMDatetime($timestamp, $format = 'Y-m-d H:i:s') {
        return gmdate($format, $timestamp);
        
    }
    
    /**
     * @param int $timestamp 
     * @param string $format 
     * @return string
     */
    public static function getDate($timestamp, $format = 'Y-m-d H:i:s'){
        return date($format, $timestamp);
    }
    
    /**
     * Get interval
     * @param int $timestamp
     * @param boolean $gtm
     * @return string
     */
    public static function getIntervar($timestamp, $gtm = true) {
        $tokens = array(
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second',
        );
        
        if ($gtm) {
            $time = strtotime(gmdate('Y-m-d H:i:s')) - $timestamp;
        }else{
            $time = time() - $timestamp;
        }
        
        foreach ($tokens as $unit => $text) {
            if ($time < $unit)
                continue;
            $numberOfUnits = floor($time / $unit);
            
            if($text == 'second' && $numberOfUnits < 10){
                return Yii::t('app', 'Just Now');
            }
            
            return Yii::t('app', " {n, plural, =1{# $text} other{# {$text}s}} ago", ['n' => $numberOfUnits]);
            
        }
    }

}
