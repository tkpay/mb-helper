<?php

namespace mb\helper;

class Crypt
{
    private static $aliasDict = 'zEF3vWhuirMDBSO8Tb0Ag9yLXktNaeV5oYxlKnGqZ4mfsRwc1UpjH2JdC76QIP';

    /**
     * 将ID转换为字符串别名
     *
     * @param int $id
     * @return string
     */
    public static function aliasEncode($id, $dict = '')
    {
        if (empty($dict)) {
            $dict = self::$aliasDict;
        }
        $id = intval($id);
        $array = array();
        while ($id != 0) {
            $surplus = $id % 30;
            $id = ($id - $surplus) / 30;
            $array[] = $surplus;
        }
        $array = array_reverse($array);
        $ret = '';
        foreach ($array as $entry) {
            if ($entry == 0) {
                $char = substr($dict, 0, 4);
            } else {
                $char = substr($dict, $entry * 2 + 2, 2);
            }
            $ret .= $char[rand(0, strlen($char) - 1)];

        }
        $left = 4 - strlen($ret);
        if ($left > 0) {
            $pad = substr($dict, 0, 4);
            $ret = substr(str_shuffle($pad), 0, $left) . $ret;
        }

        return $ret;
    }

    /**
     * 将字符串别名转换为ID
     *
     * @param string $alias
     * @return int
     */
    public static function aliasDecode($alias, $dict = '')
    {
        if (empty($dict)) {
            $dict = self::$aliasDict;
        }
        $len = strlen($alias);
        if ($len < 4) {
            return 0;
        }
        $ret = 0;
        for ($i = 0; $i < $len; $i++) {
            $char = substr($alias, $i, 1);
            $idx = strpos($dict, $char);
            if ($idx === false) {
                return 0;
            }
            if ($idx > 3) {
                if ($idx % 2 == 1) {
                    $idx--;
                }
                $num = ($idx - 2) / 2;
            } else {
                $num = 0;
            }
            $ret += $num * pow(30, $len - $i - 1);
        }

        return $ret;
    }

    /**
     * 使用base进行url编码
     * @param $str
     * @return mixed
     */
    public static function baseUrlEncode($str)
    {
        return str_replace(array('/', '+', '='), array('-', '.', '_'), base64_encode($str));
    }

    /**
     * 使用base进行url解码
     * @param $str
     * @return bool|string
     */
    public static function baseUrlDecode($str)
    {
        return base64_decode(str_replace(array('-', '.', '_'), array('/', '+', '='), $str));
    }

}