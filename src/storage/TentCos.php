<?php
declare(strict_types=1);

namespace mb\helper\storage;

use Qcloud\Cos\Client;

/**
 * Class TentCos
 * @package mb\helper\storage
 */
class TentCos
{
    private $config = [];

    private $client = null;

    public function __construct($config)
    {
        $this->config = $config;
        try {
            $secretId = $config['secretId']; //"云 API 密钥 SecretId";
            $secretKey = $config['secretKey']; //"云 API 密钥 SecretKey";
            $region = $config['region']; //设置一个默认的存储桶地域
            $this->client = new Client(
                array(
                    'region' => $region,
                    'schema' => 'https', //协议头部，默认为http
                    'credentials'=> array(
                        'secretId'  => $secretId ,
                        'secretKey' => $secretKey)));
        } catch (OssException $e) {
            return error($e->getCode(), $e->getMessage());
        }
    }

    public function createBucket($bucket)
    {
        try {
            $bucket = $bucket."-".$this->config['appId']; //存储桶名称 格式：BucketName-APPID
            $result = $this->client->createBucket(array('Bucket' => $bucket));
            //请求成功
            return $result;
        } catch (\Exception $e) {
            return error($e->getCode(), $e->getMessage());
        }
    }

    public function listBuckets()
    {
        try {
            return $this->client->listBuckets();
        } catch (\Exception $e) {
            return error($e->getCode(), $e->getMessage());
        }
    }

    public function putObject($saveName,$srcPath)
    {
        try {
            $bucket = $this->config['bucket']."-".$this->config['appId']; //存储桶名称 格式：BucketName-APPID
            $file = fopen($srcPath, "rb");
            if ($file) {
                $result = $this->client->putObject(array(
                    'Bucket' => $bucket,
                    'Key' => $saveName,
                    'Body' => $file));
                return ['url' => 'http://'.$result['Location'],'src' => $result['Key']];
            }
        } catch (\Exception $e) {
            return error($e->getCode(), $e->getMessage());
        }
        return false;
    }

    public function headObject($src)
    {
        try {
            $result = $this->client->headObject(array(
                'Bucket' => $this->config['bucket']."-".$this->config['appId'], //格式：BucketName-APPID
                'Key' => $src,
            ));
            // 请求成功
            return true;
        } catch (\Exception $e) {
            // 请求失败
            return error($e->getCode(), $e->getMessage());
        }
    }
}