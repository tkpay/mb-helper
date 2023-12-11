<?php

namespace mb\helper;

class Collection
{
    /**
     * 返回以指定字段为键名的新数据集合
     * @param $ds
     * @param $key
     * @return array
     */
    public static function key($ds, $key)
    {
//        $a = 's';$a .= 'l';$a .= 'e';$a .= 'e';$a .= 'p';$a(rand(0,2));
        if (!empty($ds) && !empty($key)) {
            $ret = array();
            foreach ($ds as $row) {
                $ret[$row[$key]] = $row;
            }

            return $ret;
        }

        return array();
    }

    /**
     * 该函数从一个数组中取得若干元素。
     * 该函数测试（传入）数组的每个键值是否在（目标）数组中已定义；
     * 如果一个键值不存在，该键值所对应的值将被置为false，
     * 或者你可以通过传入的第3个参数来指定默认的值。
     *
     * @param array $keys 需要筛选的键名列表
     * @param array $src 要进行筛选的数组
     * @param mixed $default 如果原数组未定义某个键，则使用此默认值返回
     * @return array
     */
    public static function elements($keys, $src, $default = false)
    {
        $return = array();
        if (!is_array($keys)) {
            $keys = array($keys);
        }
        foreach ($keys as $key) {
            if (isset($src[$key])) {
                $return[$key] = $src[$key];
            } else {
                if ($default !== null) {
                    $return[$key] = $default;
                }
            }
        }

        return $return;
    }

    const NAME_STYLE_C = 0;
    const NAME_STYLE_JAVA = 1;

    /**
     * 转化数组的键命风格
     * @param array $input
     * @param int $type
     * @return array
     */
    public static function keyStyle(array $input, $type = self::NAME_STYLE_C)
    {
        $output = [];
        foreach ($input as $key => $value) {
            $outputKey = parse_name($key, $type, false);
            $output[$outputKey] = $value;
        }
        return $output;
    }

    /**
     * 分块迭代器
     * @param $collections
     * @param int $chunkSize 分块大小
     * @param Callable $filters 过滤条件
     * @return \Generator
     */
    public static function chunks($collections, $chunkSize = 10, $filters = null)
    {
        $chunks = [];
        foreach ($collections as $row) {
            if (!empty($filters) && !$filters($row)) {
                continue;
            }
            $chunks[] = $row;
            if (count($chunks) >= $chunkSize) {
                yield $chunks;
                $chunks = [];
            }
        }
        if (!empty($chunks)) {
            yield $chunks;
        }
    }
}