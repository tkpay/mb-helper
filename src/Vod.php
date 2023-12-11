<?php

namespace mb\helper;

use mb\helper\vod\Aliyun;

abstract class Vod
{
    /**
     * @param $config
     * @return Vod
     */
    public static function create($config)
    {
        if ($config['type'] == 'aliyun') {
            return new Aliyun($config);
        }

        return error(-1, '不支持的类型');
    }
    
    abstract public function createUploader($videoInfo);
    abstract public function createPlayer($videoId);
    abstract public function delete($videoId);
}

