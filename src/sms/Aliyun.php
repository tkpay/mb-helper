<?php

namespace mb\helper\sms;


use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\Regions\EndpointConfig;
use mb\helper\Sms;

class Aliyun extends Sms
{

    private $config = null;
    /**
     * @var DefaultAcsClient
     */
    private $client = null;

    /**
     * Sms constructor.
     *
     * @param $config
     * key      - AppKey
     * secret   - AppSecret
     * signature- 签名
     */
    public function __construct($config)
    {
        $this->config = $config;
        $product = "Dysmsapi";
        $domain = "dysmsapi.aliyuncs.com";
        $region = "cn-hangzhou";
        $endPointName = "cn-hangzhou";
        if (!defined('ENABLE_HTTP_PROXY')) {
            define('ENABLE_HTTP_PROXY', false);
        }
        EndpointConfig::load();
        $profile = DefaultProfile::getProfile($region, $this->config['key'], $this->config['secret']);
        DefaultProfile::addEndpoint($endPointName, $region, $product, $domain);

        $this->client = new DefaultAcsClient($profile);
    }

    public function send($phone, $tpl, $pars = array())
    {
        $request = new SendSmsRequest();
        $request->setPhoneNumbers($phone);
        $request->setSignName($this->config['signature']);
        $request->setTemplateCode($tpl);
        $request->setTemplateParam(json_encode($pars, JSON_UNESCAPED_UNICODE));
        $resp = $this->client->getAcsResponse($request);

        if (is_object($resp) && $resp->Code == 'OK') {
            return true;
        }

        return error(-1, $resp->Message);
    }

    public function test()
    {
        $config = array();
        $config['key'] = '23360498';
        $config['secret'] = '2971baf1489c7645e316671a8ccbff6e';
        $config['signature'] = '大树桩TEAM';
        $sms = new Aliyun($config);
        $pars = [];
        $pars['product'] = '大树桩';
        $pars['code'] = '0482';
        $sms->send('15535109820', 'SMS_8955595f', $pars);
        exit;
    }
}

