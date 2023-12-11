<?php

namespace mb\helper;


class Core
{
    /**
     * 指定长度返回随机字符串
     *
     * @param int $length
     * @param bool $numeric 纯数字
     * @return string
     */
    public static function random($length, $numeric = false)
    {
        $output = '';
        while (strlen($output) < $length) {
            if ($numeric) {
                $output .= mt_rand(0, 9);
            } else {
                $output .= sha1(uniqid());
            }
        }
        return substr($output, 0, $length);
    }

    /**
     * 判断一个数是否介于一个区间或将一个数转换为此区间的数.
     * boolean 判断输入参数是否介于 $downline 和 $upline 之间
     * number 将输入参数转换为介于  $downline 和 $upline 之间的整数
     *
     * @param string $num 输入参数
     * @param int $downline 参数下限
     * @param int $upline 参数上限
     * @param bool $returnNear 对输入参数是判断还是返回
     * @return bool | number
     */
    public static function limit($num, $downline, $upline, $returnNear = true)
    {
        $num = intval($num);
        $downline = intval($downline);
        $upline = intval($upline);
        if ($num < $downline) {
            return empty($returnNear) ? false : $downline;
        } elseif ($num > $upline) {
            return empty($returnNear) ? false : $upline;
        } else {
            return empty($returnNear) ? true : $num;
        }
    }
}