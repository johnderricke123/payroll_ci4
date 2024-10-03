<?php

    //******************************RETREIVE OVERTIME RENDERED******************************
    function retreive_overtime($array_duties){
        print_r($array_duties."////");die();
        // foreach($array_duties as $overtimes){
        //     $time_rendered = $overtimes;
        //     $regular_duty_time = "08:00:00";
            
        //     if($time_rendered > $regular_duty_time){
        //         $end_datetime = new DateTime($time_rendered); 
        //         $diff2 = $end_datetime->diff(new DateTime($regular_duty_time));         
        //         $time2 = $diff2->h.":".$diff2->i.":"."00";
        //         return $overtime_rendered[] = $time2; 
        //     }
        // }
    }
    //******************************RETREIVE OVERTIME RENDERED******************************

    function time_add($IN, $OVERTIMEOUT, $OVERTIMEIN, $OUT) {
        //var_dump($IN."//".$OVERTIMEOUT."//".$OVERTIMEIN."//".$OUT);die();
        if($IN != false && $OVERTIMEOUT != false){
            $start_datetime = new DateTime($IN); 
            $diff = $start_datetime->diff(new DateTime($OVERTIMEOUT));    
            $time = $diff->h.":".$diff->i.":"."00";
        }else{
            $time = "No data";
        }
         
        if($OVERTIMEIN != false && $OUT != false){
            $end_datetime = new DateTime($OVERTIMEIN); 
            $diff2 = $end_datetime->diff(new DateTime($OUT)); 
            $time2 = $diff2->h.":".$diff2->i.":"."00";
        }else{
            $time2 = "No data";
        }
        if($IN != false && $OUT != false && $OVERTIMEIN != true && $OVERTIMEOUT != true){
            $end_datetime = new DateTime($IN); 
            $diff2 = $end_datetime->diff(new DateTime($OUT)); 
            $time2 = $diff2->h.":".$diff2->i.":"."00";
        }
    
        //var_dump($time."//".$time2);die();
        // $time = $diff->h.":".$diff->i.":"."00";
        // $time2 = $diff2->h.":".$diff2->i.":"."00";
        if($time2 == "No data" && $time == "No data"){
            //var_dump("no data");die();
            return $result = "00:00:00";    
        }
        if($time2 != "No data" && $time == "No data"){
            return $result = date("H:i:s",strtotime($time2));    
        }
        if($time2 == "No data" && $time != "No data"){
            return $result = date("H:i:s",strtotime($time));    
        }

        if($time2 != false && $time != false){
            $secs = strtotime($time2)-strtotime("00:00:00");
            return $result = date("H:i:s",strtotime($time)+$secs);    
        }


     }
//**************************ADDS ALL TOTAL HOURS PER DAY************************** 
     function time_all_add($array_duties) {
        $timestamps = $array_duties;

        $totalSeconds = 0;
        //var_dump($timestamps);die();
        foreach ($timestamps as $timestamp) {
            list($hours, $minutes, $seconds) = explode(':', $timestamp);
            $totalSeconds += ($hours * 3600) + ($minutes * 60) + $seconds;
        }
        
        // Convert total seconds back to H:i:s
        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;
        
        // Format the result
        return $result = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        
    }
//**************************ADDS ALL TOTAL HOURS PER DAY************************** 

//**************************DEDUCTS TOTAL WITH OVERTIME HOURS************************** 
function time_deducts($ot_total_arr) {
    // Define the time durations
    $time1 = '30:16:00'; // 30 hours, 16 minutes, 0 seconds
    $time2 = '06:16:00'; // 6 hours, 16 minutes, 0 seconds
    
    // Create DateTime objects
    $dateTime1 = new DateTime($time1);
    $dateTime2 = new DateTime($time2);
    
    // Calculate the difference
    $interval = $dateTime1->diff($dateTime2);
    
    // Format the result
    $result = $interval->format('%H:%I:%S');
    
    echo "The result of subtracting $time2 from $time1 is: $result\n";
die();    
echo json_encode($ot_total_arr);die();    
// $date1 = new DateTime('30:16:00');
// echo json_encode($date1);die();
// $date2 = new DateTime('06:16:00');
// return $date2;
//echo json_encode("testing");die();

// $interval = $date1->diff($date2);
// $result = $interval->format('%H:%I:%S');
// echo "Result: " . $result;
// die();
    
}
//**************************DEDUCTS TOTAL WITH OVERTIME HOURS************************** 
