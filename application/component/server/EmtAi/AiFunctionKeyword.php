<?php

namespace app\component\server\EmtAi;



/**
 * AI工具类
 * Class AiTools
 * @package app\component\server\EmtAi
 */

class AiFunctionKeyword
{
    //获取当天日期
    public static function getToday(){
        return date("Y-m-d");
    }

    public static function getTomorrow(){
        return date("Y-m-d",time()+3600*24*1);
    }

    //获取后天日期
    public static function afterTomorrow(){
        return date("Y-m-d",time()+3600*24*2);
    }
    //获取大后天日期
    public static function nextThreeDay(){
        return date("Y-m-d",time()+3600*24*3);
    }

    public static function getWeekday($matchs,$day){
//        echo $day;
        //获取当天星期几
        $w = date("w");
        if($day>$w){
            return date("Y-m-d",time()+3600*24*($day-$w));
        }elseif (($day==$w)){
            return date("Y-m-d");
        }else{
//            echo 33;
            return date("Y-m-d",time()+3600*24*(7-($w-$day)));
        }
    }

    //获取上午
    public static function getPeriodAm(){
        return "am";
    }

    //获取上午
    public static function getPeriodPm(){
        return "pm";
    }


    //获取几月几号
    public static function getMonthAndDay($matchs){
        $getDate =  date("Y").'-'.$matchs[1].'-'.$matchs[2];
        return date("Y-m-d",strtotime($getDate));
    }

    //获取几号
    public static function getDay($matchs){
        $getDate = date("Y-m").'-'.$matchs[1];
        return date("Y-m-d",strtotime($getDate));
    }

}

