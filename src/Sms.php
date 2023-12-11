<?php
namespace mb\helper;


use mb\helper\sms\Aliyun;

abstract class Sms
{
    /**
     * @param $config
     * @return Sms
     */
    public static function create($config)
    {
        if ($config['type'] == 'aliyun') {
            return new Aliyun($config);
        }

        return error(-1, '不支持的类型');
    }
    
    abstract public function send($phone, $tpl, $pars = []);
    abstract public function test();
}
