<?php

class stopwatch{
    private static float $start_time = 0;
    private static bool $is_started = false;
    public static function start(){
        static::$start_time = microtime(true);
        static::$is_started = true;
    }
    public static function stop():float{
        if(!static::$is_started){
            return false;
        }
        $res = microtime(true) - static::$start_time;
        static::$is_started = false;
        return $res;
    }
}