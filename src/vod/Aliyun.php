<?php

namespace mb\helper\vod;


use Aliyun\Core\DefaultAcsClient;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\Regions\EndpointConfig;
use mb\helper\Vod;
use vod\Request\V20170321\CreateUploadVideoRequest;
use vod\Request\V20170321\DeleteVideoRequest;
use vod\Request\V20170321\GetPlayInfoRequest;
use vod\Request\V20170321\RefreshUploadVideoRequest;

class Aliyun extends Vod
{
    private $config = [
        'access' => '',
        'secret' => ''
    ];

    private $client;

    /**
     * VideoApi constructor.
     *
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
        $regionId = 'cn-shanghai';
        $profile = DefaultProfile::getProfile($regionId, $this->config['access'], $this->config['secret']);
        if (!defined('ENABLE_HTTP_PROXY')) {
            define('ENABLE_HTTP_PROXY', false);
        }
        EndpointConfig::load();
        $this->client = new DefaultAcsClient($profile);
    }

    /**
     * $videoInfo
     *      ['videoId']  指定videoId
     *      ['title']  标题
     *      ['filename'] 文件名
     *      ['category'] 分类, 可选
     *      ['transform'] 转码模版, 可选
     *      ['description'] 描述, 可选
     *      ['cover'] 封面, 可选
     *      ['tags']  标签,可选
     *
     * @param $videoInfo
     * @return mixed|\SimpleXMLElement
     */
    public function createUploader($videoInfo)
    {
        if (!empty($videoInfo['videoId'])) {
            $request = new RefreshUploadVideoRequest();
            $request->setVideoId($videoInfo['videoId']);
            $request->setAcceptFormat('JSON');
            $resp = $this->client->getAcsResponse($request);
            if (is_object($resp) && $resp->UploadAddress) {
                return [
                    'uploadAddress' => $resp->UploadAddress,
                    'uploadAuth' => $resp->UploadAuth,
                    'videoId' => $videoInfo['VideoId'],
                ];
            }
        } else {
            $request = new CreateUploadVideoRequest();
            $request->setTitle($videoInfo['title']);
            $request->setFileName($videoInfo['filename']);
            if (!empty($videoInfo['description'])) {
                $request->setDescription($videoInfo['description']);
            }
            if (!empty($videoInfo['cover'])) {
                $request->setCoverURL($videoInfo['cover']);
            }
            if (!empty($videoInfo['tags'])) {
                $request->setTags($videoInfo['tags']);
            }
            if (!empty($videoInfo['category'])) {
                $request->setCateId($videoInfo['category']);
            }
            if (!empty($videoInfo['transform'])) {
                $request->setTemplateGroupId($videoInfo['transform']);
            }
            $request->setAcceptFormat('JSON');

            $resp = $this->client->getAcsResponse($request);
            if (is_object($resp) && $resp->UploadAddress) {
                return [
                    'uploadAddress' => $resp->UploadAddress,
                    'uploadAuth' => $resp->UploadAuth,
                    'videoId' => $resp->VideoId,
                ];
            }
        }

        return error(-1, '创建上传服务失败');
    }

    public function createPlayer($videoId)
    {
        $request = new GetPlayInfoRequest();
        $request->setVideoId($videoId);
        $request->setAuthTimeout(3600 * 2);
        $request->setAcceptFormat('JSON');

        $resp = $this->client->getAcsResponse($request);
        if (is_object($resp) && $resp->PlayInfoList) {
            $ret = [];
            foreach ($resp->PlayInfoList->PlayInfo as $info) {
                $ret[strtolower($info->Definition)] = $info->PlayURL;
                $ret['duration'] = floatval($info->Duration);
            }

            return $ret;
        }

        return error(-1, '创建播放连接失败');
    }

    public function delete($videoId)
    {
        $request = new DeleteVideoRequest();
        $request->setVideoIds($videoId);
        $request->setAcceptFormat('JSON');

        $resp = $this->client->getAcsResponse($request);
        if (is_object($resp) && $resp->RequestId) {
            return true;
        }

        return error(-1, '删除媒体失败');
    }
}
