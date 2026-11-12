<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

namespace AcyCheckerCmsServices;

use Joomla\CMS\Http\HttpFactory;

class File
{
    public static function fileGetContent($url, $timeout = 10)
    {
        ob_start();
        // use the Joomla way first
        $data = '';
        $http = HttpFactory::getHttp();
        try {
            $response = $http->get($url, [], $timeout);
        } catch (\RuntimeException $e) {
            $response = null;
        }

        if ($response !== null && $response->code === 200) {
            $data = $response->body;
        }

        if (empty($data) && function_exists('curl_exec') && filter_var($url, FILTER_VALIDATE_URL)) {
            $conn = curl_init($url);
            curl_setopt($conn, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($conn, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($conn, CURLOPT_RETURNTRANSFER, 1);
            if (!empty($timeout)) {
                curl_setopt($conn, CURLOPT_TIMEOUT, $timeout);
                curl_setopt($conn, CURLOPT_CONNECTTIMEOUT, $timeout);
            }

            $data = curl_exec($conn);
            if ($data === false) {
                echo curl_error($conn);
            }
        }

        if (empty($data) && function_exists('file_get_contents')) {
            if (!empty($timeout)) {
                ini_set('default_socket_timeout', $timeout);
            }
            $streamContext = stream_context_create(['ssl' => ['verify_peer' => false, 'verify_peer_name' => false]]);
            $data = file_get_contents($url, false, $streamContext);
        }

        if (empty($data) && function_exists('fopen') && function_exists('stream_get_contents')) {
            $handle = fopen($url, "r");
            if (!empty($timeout)) {
                stream_set_timeout($handle, $timeout);
            }
            $data = stream_get_contents($handle);
        }
        $warnings = ob_get_clean();

        if (Security::isDebug()) {
            echo $warnings;
        }

        return $data;
    }

    public static function getFolders($path, $filter = '.', $recurse = false, $full = false, $exclude = ['.svn', 'CVS', '.DS_Store', '__MACOSX'], $excludefilter = ['^\..*'])
    {
        $path = self::cleanPath($path);

        if (!is_dir($path)) {
            Message::enqueueMessage(Language::translationSprintf('ACYC_IS_NOT_A_FOLDER', $path), 'error');

            return [];
        }

        if (count($excludefilter)) {
            $excludefilter_string = '/('.implode('|', $excludefilter).')/';
        } else {
            $excludefilter_string = '';
        }

        $arr = self::getItems($path, $filter, $recurse, $full, $exclude, $excludefilter_string, false);
        asort($arr);

        return array_values($arr);
    }

    public static function getItems($path, $filter, $recurse, $full, $exclude, $excludefilter_string, $findfiles)
    {
        $arr = [];

        if (!($handle = @opendir($path))) {
            return $arr;
        }

        while (($file = readdir($handle)) !== false) {
            if ($file == '.' || $file == '..' || in_array($file, $exclude) || (!empty($excludefilter_string) && preg_match(
                        $excludefilter_string,
                        $file
                    ))) {
                continue;
            }
            $fullpath = $path.'/'.$file;

            $isDir = is_dir($fullpath);

            if (($isDir xor $findfiles) && preg_match("/$filter/", $file)) {
                if ($full) {
                    $arr[] = $fullpath;
                } else {
                    $arr[] = $file;
                }
            }

            if ($isDir && $recurse) {
                if (is_int($recurse)) {
                    $arr = array_merge(
                        $arr,
                        File::getItems(
                            $fullpath,
                            $filter,
                            $recurse - 1,
                            $full,
                            $exclude,
                            $excludefilter_string,
                            $findfiles
                        )
                    );
                } else {
                    $arr = array_merge(
                        $arr,
                        File::getItems(
                            $fullpath,
                            $filter,
                            $recurse,
                            $full,
                            $exclude,
                            $excludefilter_string,
                            $findfiles
                        )
                    );
                }
            }
        }

        closedir($handle);

        return $arr;
    }

    public static function cleanPath($path, $ds = DIRECTORY_SEPARATOR)
    {
        $path = trim($path);

        if (empty($path)) {
            $path = ACYC_ROOT;
        } elseif (($ds == '\\') && substr($path, 0, 2) == '\\\\') {
            $path = "\\".preg_replace('#[/\\\\]+#', $ds, $path);
        } else {
            $path = preg_replace('#[/\\\\]+#', $ds, $path);
        }

        return $path;
    }

    public static function getFiles(
        $path,
        $filter = '.',
        $recurse = false,
        $full = false,
        $exclude = ['.svn', 'CVS', '.DS_Store', '__MACOSX'],
        $excludefilter = [
            '^\..*',
            '.*~',
        ],
        $naturalSort = false
    ) {
        $path = self::cleanPath($path);

        if (!is_dir($path)) {
            Message::enqueueMessage(Language::translationSprintf('ACYC_IS_NOT_A_FOLDER', $path), 'error');

            return false;
        }

        if (count($excludefilter)) {
            $excludefilter_string = '/('.implode('|', $excludefilter).')/';
        } else {
            $excludefilter_string = '';
        }

        $arr = self::getItems($path, $filter, $recurse, $full, $exclude, $excludefilter_string, true);

        if ($naturalSort) {
            natsort($arr);
        } else {
            asort($arr);
        }

        return array_values($arr);
    }

    public static function writeFile($file, $buffer, $flags = 0)
    {
        if (!file_exists(dirname($file)) && self::createFolder(dirname($file)) == false) {
            return false;
        }
        $file = self::cleanPath($file);

        return is_int(file_put_contents($file, $buffer, $flags));
    }

    public static function createFolder($path = '', $mode = 0755)
    {
        $path = self::cleanPath($path);
        if (file_exists($path)) {
            return true;
        }

        $origmask = @umask(0);
        $ret = @mkdir($path, $mode, true);
        @umask($origmask);

        return $ret;
    }
}
