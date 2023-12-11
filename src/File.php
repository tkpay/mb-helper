<?php

namespace mb\helper;

class File
{
    public static function tree($path, $ignores = array())
    {
        $path = rtrim($path, '/');
        if (empty($ignores['base'])) {
            $ignores['base'] = $path;
        }
        $files = array();
        $ds = glob($path . '/*');
        if (is_array($ds)) {
            foreach ($ds as $entry) {
                $isIgnore = false;
                $local = substr($entry, strlen($ignores['base']));
                foreach ($ignores as $key => $ig) {
                    if (is_int($key) && preg_match($ig, $local)) {
                        $isIgnore = true;
                        break;
                    }
                }
                if (!$isIgnore) {
                    if (is_file($entry)) {
                        $files[] = $entry;
                    }
                    if (is_dir($entry)) {
                        $rs = self::tree($entry, $ignores);
                        foreach ($rs as $f) {
                            $files[] = $f;
                        }
                    }
                }
            }
        }

        return $files;
    }

    public static function copyRecurse($src, $des, $verbose = false)
    {
        $srcFiles = self::tree($src);
        foreach ($srcFiles as $srcFile) {
            $desFile = str_replace($src, $des, $srcFile);
            if ($verbose) {
                echo "{$srcFile}\n\t->{$desFile}\n";
            }
            self::mkdirs(dirname($desFile));
            copy($srcFile, $desFile);
            @chmod($desFile, 0644);
        }
    }

    public static function move($src, $des)
    {
        self::mkdirs(dirname($des));
        if (is_uploaded_file($src)) {
            move_uploaded_file($src, $des);
        } else {
            rename($src, $des);
        }
        @chmod($des, 0644);

        return is_file($des);
    }

    public static function mkdirs($path)
    {
        if (!is_dir($path)) {
            self::mkdirs(dirname($path));
            mkdir($path);
        }

        return is_dir($path);
    }

    public static function rmdirs($path, $clean = false)
    {
        if (!is_dir($path)) {
            return false;
        }
        $files = glob($path . '/*');
        if ($files) {
            foreach ($files as $file) {
                is_dir($file) ? self::rmdirs($file) : @unlink($file);
            }
        }

        return $clean ? true : @rmdir($path);
    }
}