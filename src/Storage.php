<?php

namespace mb\helper;

use mb\helper\storage\Aliyun;
use mb\helper\storage\Qiniu;
use mb\helper\storage\File;
use think\Exception;

abstract class Storage
{
    /**
     * @param $config
     * @return Storage
     */
    public static function create($config)
    {
        switch ($config['type']) {
            case 'qiniu':
                return new Qiniu($config);
            case 'aliyun-oss':
                return new Aliyun($config);
            case 'file':
                return new File($config);
            default:
                trigger_error('不支持的类型');
        }
    }

    /**
     * 获取访问域名前缀
     * @return string
     */
    abstract public function domainUrl();

    /**
     * @param $path string 存放路径
     * @param $file string 文件路径
     * @return array|Exception [filename,url] 结构
     */
    abstract public function put($path, $file);

    /**
     * @param $path string 存放路径
     * @param $content mixed 文件内容
     * @return array|Exception [filename,url] 结构
     */
    abstract public function putContent($path, $content);

    /**
     * @param $path string 存放路径
     * @param $isFull bool 是否包含访问域名的完整路径
     * @return true|Exception [filename,url] 结构
     */
    abstract public function delete($path, $isFull);

    /**
     * @param $path string 存放路径
     * @param $url string 网络连接地址
     * @return array|Exception [filename,url] 结构
     */
    abstract public function fetch($path, $url);

    /**
     * 下载指定文件
     * @param $path
     * @return mixed
     */
    abstract public function download($path);
}