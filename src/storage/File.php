<?php

namespace mb\helper\storage;

use mb\helper\Net;
use mb\helper\Storage;
use mb\helper\File as FileApi;

class File extends Storage
{
    private $config = [
        'urlPrefix' => '',
        'path' => ''
    ];

    /**
     * @param $config
     *      urlPrefix url访问路径前置
     *      path      保存目录
     * @throws \think\Exception
     */
    public function __construct($config)
    {
        if (empty($config['path'])) {
            throw error(-1, '指定的存储参数无效');
        }
        $this->config = array_merge($this->config, $config);
    }

    /**
     * 获取访问域名前缀
     * @return string
     */
    public function domainUrl()
    {
        $urlPrefix = $this->config['urlPrefix'];
        return $urlPrefix;
    }

    public function fetch($path, $url)
    {
        $desPath = $this->config['path'] . $path;
        FileApi::mkdirs(dirname($desPath));
        $res = Net::httpDownload($url, $desPath);
        if (is_error($res)) {
            return $res;
        }
        return true;
    }

    public function put($path, $file)
    {
        if (!is_file($file)) {
            return error(-2, '指定的文件不存在');
        }
        $desPath = $this->config['path'] . $path;
        FileApi::mkdirs(dirname($desPath));
        if (copy($file, $desPath)) {
            $return = [
                'filename' => $path,
                'url' => $this->domainUrl() . $path
            ];

            return $return;
        } else {
            return error(-1, '文件保存失败');
        }
    }

    public function putContent($path, $content)
    {
        if (empty($content)) {
            return error(-2, '没有指定上传的内容');
        }
        $desPath = $this->config['path'] . $path;
        FileApi::mkdirs(dirname($desPath));
        if (file_put_contents($desPath, $content) !== false) {
            $return = [
                'filename' => $path,
                'url' => $this->domainUrl() . $path
            ];

            return $return;
        } else {
            return error(-1, '文件保存失败');
        }
    }

    public function delete($path, $isFull)
    {
        if ($isFull) {
            $path = substr($path, strlen($this->domainUrl()));
        }
        $desPath = $this->config['path'] . $path;
        if (@unlink($desPath)) {
            return true;
        }

        return error(-1, '删除文件失败');
    }
}
