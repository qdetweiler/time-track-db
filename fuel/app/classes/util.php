<?php

class Util {
    /**
     * Round a time to the nearest interval
     * @param type $time time to round
     * @param type $interval interval (in seconds) to round to
     * @return type timestamp
     */
    public static function roundToInterval($time, $interval){
        
        $quotient = ((integer)floor($time/$interval));
        //PHP_Console::log($quotient);
        $remainder = $time%$interval;
        //PHP_Console::log($remainder);
        
        if($remainder < ($interval/2)){
            return $quotient*$interval;
        } else {
            return $quotient*$interval+$interval;
        }
        
    }
    
    /**
     * Round a time to the nearest interval, rounding up if
     * the time is greater than the threshold
     * @param type $time time to round
     * @param type $interval interval (in seconds) to round to
     * @param type $threshold threshold at which to round up
     * @return type timestamp
     * @pre $threshold is between 0 and $interval inclusive
     */
    public static function roundToThreshold($time, $interval, $threshold){
        
        $quotient = ((integer)floor($time/$interval));
        //PHP_Console::log($quotient);
        $remainder = $time%$interval;
        //PHP_Console::log($remainder);
        
        if($remainder < ($threshold)){
            return $quotient*$interval;
        } else {
            return $quotient*$interval+$interval;
        }
    }
    
    /**
     * Convert time from seconds to hours, minutes, and seconds
     * @param type $sec
     * @return type string
     */
    public static function sec2hms ($sec) {
 
        $hours = intval(intval($sec) / 3600); 
        $minutes = intval(($sec / 60) % 60); 
        $seconds = intval($sec % 60); 

        $hms = $hours. " hrs";
        $hms .= ($minutes!= 0) ? " ".$minutes." min" : '';
        $hms .= ($seconds != 0) ? $seconds." sec" : '';
        
        return $hms;
    }
    
}

?>
