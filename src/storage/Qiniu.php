<?php

namespace mb\helper\storage;

use mb\helper\Storage;
use Qiniu\Auth;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;

class Qiniu extends Storage
{
    private $config = [
        'scheme' => 'http',
        'key' => '',
        'secret' => '',
        'host' => '',
        'bucket' => '',
        'prefix' => ''
    ];
    /**
     * @var Auth
     */
    private $auth = null;

    /**
     * @param $config
     *      scheme  访问协议
     *      key     应用ID
     *      secret  访问密钥
     *      bucket  存储bucket
     *      host    访问域名
     * @throws \think\Exception
     */
    public function __construct($config)
    {
        if (empty($config['key']) || empty($config['secret']) || empty($config['host']) || empty($config['bucket'])) {
            throw error(-1, '指定的存储参数无效');
        }
        $this->config = array_merge($this->config, $config);
        $this->auth = new Auth($this->config['key'], $this->config['secret']);
    }

    public function domainUrl()
    {
        $urlPrefix = "{$this->config['scheme']}://{$this->config['host']}/{$this->config['prefix']}";
        return $urlPrefix;
    }

    public function put($path, $file)
    {
        if (!is_file($file)) {
            return error(-2, '指定的文件不存在');
        }
        $policy = null;
        $token = $this->auth->uploadToken($this->config['bucket'], null, 3600, $policy);
        $uploadMgr = new UploadManager();
        $fullPath = $this->config['prefix'] . $path;
        list($ret, $error) = $uploadMgr->putFile($token, $fullPath, $file);
        if (empty($error)) {
            $returnPath = substr($ret['key'], strlen($this->config['prefix']));
            $return = [
                'filename' => $returnPath,
                'url' => $this->domainUrl() . $returnPath
            ];

            return $return;
        } else {
            return error(-1, $error->message());
        }
    }

    public function putContent($path, $content)
    {
        if (empty($content)) {
            return error(-2, '没有指定上传的内容');
        }
        $policy = null;
        $token = $this->auth->uploadToken($this->config['bucket'], null, 3600, $policy);
        $uploadMgr = new UploadManager();
        $fullPath = $this->config['prefix'] . $path;
        list($ret, $error) = $uploadMgr->put($token, $fullPath, $content);
        if (empty($error)) {
            $returnPath = substr($ret['key'], strlen($this->config['prefix']));
            $return = [
                'filename' => $returnPath,
                'url' => $this->domainUrl() . $returnPath
            ];

            return $return;
        } else {
            return error(-1, $error->message());
        }
    }

    public function delete($path, $isFull = false)
    {
        if ($isFull) {
            $path = substr($path, strlen($this->domainUrl()));
        }
        $fullPath = $this->config['prefix'] . $path;
        $mgr = new BucketManager($this->auth);
        $error = $mgr->delete($this->config['bucket'], $fullPath);
        if (empty($error)) {
            return true;
        } else {
            return error(-1, $error->message());
        }
    }

    public function fetch($path, $url)
    {
        $mgr = new BucketManager($this->auth);
        $fullPath = $this->config['prefix'] . $path;
        list($ret, $error) = $mgr->fetch($url, $this->config['bucket'], $fullPath);
        if (empty($error)) {
            $returnPath = substr($ret['key'], strlen($this->config['prefix']));
            $return = [
                'filename' => $returnPath,
                'url' => $this->domainUrl() . $returnPath
            ];

            return $return;
        } else {
            return error(-1, $error->meesage());
        }
    }
}