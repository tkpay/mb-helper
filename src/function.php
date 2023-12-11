<?php
/**
 * @param array|null|\Throwable $data
 * @return \think\response\Json
 * @throws Error
 */
function payload($data = null)
{
    $outputData = [
        'errCode' => 0,
        'errMsg' => 'ok',
    ];
    if (!empty($data)) {
        if (is_error($data)) {
            $outputData = [
                'errCode' => $data->getCode(),
                'errMsg' => $data->getMessage()
            ];
        } elseif (is_array($data)) {
            $outputData += $data;
        } else {
            throw error(-10, '不支持的数据类型');
        }

    }
    return json($outputData);
}

/**
 * 构造错误数组
 *
 * @param $errCode
 * @param string $errMsg
 * @return Error
 */
function error($errCode, $errMsg = '')
{
    return new Error($errMsg, $errCode);
}

/**
 * 检测返回值是否产生错误
 *
 * 产生错误则返回true，否则返回false
 *
 * @param mixed $exception
 * @return boolean
 */
function is_error($exception)
{
    return $exception instanceof Throwable;
}
