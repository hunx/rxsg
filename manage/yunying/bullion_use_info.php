<?php
/**
 * @inform 运营接口 -- 及时数据 -- 元宝消耗情况
 * @author 张昌彪
 * @param null
 * @return array(today_bullion_use,last_bullion_use_all)
 * @example  
 */
if (!defined("MANAGE_INTERFACE"))
    exit;
try {
    //参数判断

    if (isset($day) && !empty($day)) //如果有某一天的值传递，就返回当天的最高在线
        {
        if (!isset($distance) || empty($distance)) {
            $distance = 24;
        }
        $spacetime = 86400 / $distance;
        for ($i = 1; $i < $distance+1; $i++) {
            $result = sql_fetch_one_cell("select count(distinct(uid)) from log_money where count<0 and time>$day and time < ($day + $spacetime*$i)");
            if (empty($result)){$result=0;}
            $cur_bullion_use[] = $result;
        }

        $last_bullion_use_all = sql_fetch_one_cell("select count(distinct(uid)) from log_money where count<0 and time < $day and time >=$day-86400");
        if (mysql_error()) {
            throw new Exception(mysql_error());
        }
        if (empty($last_bullion_use_all)){$last_bullion_use_all=0;}
        $ret['content']['last_bullion_use_all'] = $last_bullion_use_all;
        $ret['content']['cur_bullion_use'] = $cur_bullion_use;
    } else { //如果没有就传递当天的在线及时数据
        $day = date('Ymd');
        $cur_bullion_use = sql_fetch_one_cell("select count(distinct(uid)) from log_money where count<0 and time>unix_timestamp($day) and time < unix_timestamp()");
        $last_bullion_use_all = sql_fetch_one_cell("select count(distinct(uid)) from log_money where count<0 and time < unix_timestamp($day) and time >=unix_timestamp($day)-86400");

        if (mysql_error()) {
            throw new Exception(mysql_error());
        }
        if (empty($cur_bullion_use)){$cur_bullion_use=0;}
        if (empty($last_bullion_use_all)){$last_bullion_use_all=0;}
        $ret['content']['cur_bullion_use'] = $cur_bullion_use;
        $ret['content']['last_bullion_use_all'] = $last_bullion_use_all;
    }
}
catch (exception $e) {
    $ret['error'] = $e->getMessage();
}






?>