<?php

namespace mb\helper\storage;

use mb\helper\Storage;
use OSS\Core\OssException;
use OSS\OssClient;

class Aliyun extends Storage
{
    private $config = [
        'scheme' => 'http',
        'key' => '',
        'secret' => '',
        'endpoint' => '',
        'host' => '',
        'bucket' => '',
        'prefix' => '',
    ];

    /**
     * @var null|OssClient
     */
    private $client = null;

    /**
     * Aliyun constructor.
     *
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
        try {
            $this->client = new OssClient($this->config['key'], $this->config['secret'], $this->config['endpoint']);
        } catch (OssException $e) {
            throw error($e->getCode(), $e->getMessage());
        }
    }

    public function domainUrl()
    {
        $urlPrefix = "{$this->config['scheme']}://{$this->config['host']}/{$this->config['prefix']}";
        return $urlPrefix;
    }

    public function put($path, $file)
    {
        if (!is_file($file)) {
            return error(-10, '没有指定上传的文件');
        }
        try {
            $fullPath = $this->config['prefix'] . $path;
            $this->client->uploadFile($this->config['bucket'], $path, $file);
            $return = [
                'filename' => $path,
                'url' => $this->domainUrl() . $path
            ];

            return $return;

        } catch (OssException $e) {
            return error($e->getCode(), $e->getMessage());
        }
    }

    public function putContent($path, $content)
    {
        if (empty($content)) {
            return error(-10, '没有指定上传的内容');
        }
        try {
            $fullPath = $this->config['prefix'] . $path;
            $this->client->putObject($this->config['bucket'], $fullPath, $content);
            $return = [
                'filename' => $path,
                'url' => $this->domainUrl() . $path
            ];

            return $return;
        } catch (OssException $e) {
            return error($e->getCode(), $e->getMessage());
        }
    }

    /**
     * @param string $path
     * @param bool $isFull
     * @return |true
     */
    public function delete($path, $isFull = false)
    {
        if ($isFull) {
            $path = substr($path, strlen($this->domainUrl()));
        }
        $fullPath = $this->config['prefix'] . $path;
        $this->client->deleteObject($this->config['bucket'], $fullPath);
        return true;
    }

    public function fetch($path, $url)
    {
        // TODO: Implement fetch() method.
    }

    public function download($path)
    {
        try {
            $fullPath = $this->config['prefix'] . $path;
            $data = $this->client->getObject($this->config['bucket'], $fullPath);
            return $data;
        } catch (OssException $e) {
            return error($e->getCode(), $e->getMessage());
        }
    }
}
