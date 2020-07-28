<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (!function_exists('_isArr')) {

    /** Проверяет не пустой ли массив */
    function _isArr($ar)
    {
        if (is_array($ar) && !empty($ar)) {

            return true;
        }
    }
}

if (!function_exists('_obj2arr')) {

    /** Конвертирует объект в массив */
    function _obj2arr($object)
    {
        if (!is_object($object) && !is_array($object)) {

            return $object;
        }

        return array_map('_obj2arr', (array)$object);
    }

}

if (!function_exists('_arr')) {

    /** Возвращает массив */
    function _arr($arr, $mode = false)
    {
        if (!is_array($arr)) {

            if (is_object($arr)) {

                $arr = _obj2arr($arr);

            } else if ($mode && !empty($arr)) {

                $arr = [$arr];

            } else {

                $arr = [];
            }
        }

        return (array)$arr;
    }
}

if (!function_exists('_cache')) {

    /** Кэширует результат выполнения функции */
    function _cache(array $cache, callable $callback, array $params = [])
    {
        if ($cache['isNoCache'] || (defined('DEF_NO_CACHE') && DEF_NO_CACHE)) {

            $response = call_user_func(
                $callback,
                Array(
                    'params' => $params
                )
            );

        } else {

            $dir = preg_replace("/[^\w]+/", "_", $cache['dir']);
            $folder = !empty($cache['folder']) ? $cache['folder'] : 'cache_project';

            if (!empty($dir) && !empty($folder)) {

                $time = !empty($cache['time']) ? $cache['time'] : 36000000;
                $key = sha1(serialize(!empty($cache['key']) ? $cache['key'] : $params));

                $res = \Bitrix\Main\Data\Cache::createInstance();

                /** Сброс кэша */
                $clearCache = strtolower(
                    trim(
                        \Bitrix\Main\Context::getCurrent()->getRequest()->get('clear_cache')
                    )
                );

                if (!empty($clearCache)) {

                    if ($clearCache == 'all') {

                        $res->cleanDir("", $folder);

                    } else if ($clearCache == 'dir') {

                        $res->cleanDir($dir, $folder);

                    } else if ($clearCache == 'y') {

                        $res->clean($key, $dir, $folder);
                    }
                }

                if ($res->initCache($time, $key, $dir, $folder)) {

                    $resultCache = $res->getVars();

                    $response = $resultCache["response"];

                } else {

                    $response = call_user_func(
                        $callback,
                        ['params' => $params]
                    );

                    if (!empty($response)) {

                        if ($res->startDataCache($time, $key, $dir, array(), $folder)) {

                            $res->endDataCache(
                                ["response" => $response]
                            );
                        }
                    }
                }
            }
        }

        return $response;
    }
}

if (!function_exists('_log')) {

    /** Пишет лог в файл */
    function _log(array $dump, $method, $line, $dir = '/log/')
    {
        if (empty($method)) return;

        \Bitrix\Main\Diag\Debug::writeToFile(
            array_merge(
                array(
                    'date' => date('d.m.Y H:i:s'),
                ),
                $dump
            ),
            '[' . $line . '] ' . $method,
            \Bitrix\Main\IO\Path::normalize($dir . "/" . preg_replace("/[^\w]+/", "_", $method) . '_' . date('d_m_Y') . '.log')
        );
    }
}

if (!function_exists('_dump')) {

    /** Выводит dump  */
    function _dump()
    {
        $args = func_get_args();

        if (in_array('_show', $args, true) ||
            in_array((string)\Project\Config::getParam('user/devGroupId'), $GLOBALS['USER']->getUserGroupArray(), true)
        ) {

            $dumps = array();

            foreach ($args as $index => $data) {

                if (is_string($data) && preg_match("/^_/i", $data)) continue;

                if (in_array('_export', $args, true)) {

                    $dump = var_export($args, true);

                } else if (in_array('_dump', $args, true)) {

                    ob_start();

                    var_dump($data);

                    $dump = ob_get_clean();

                } else if (in_array('_json', $args, true)) {

                    $dump = $data;

                } else {

                    $dump = print_r($data, true);
                }

                $dumps["[" . $index . "] " . gettype($data)] = $dump;

                unset($dump);
            }

            if (!empty($dumps)) {

                $backtrace = debug_backtrace();
                $backtrace = $backtrace[0];

                $result = Array(
                    '>> FILE' => $backtrace['line'] . ": " . $backtrace['file'],
                    '>> DUMP' => $dumps
                );

                if (in_array('_json', $args, true)) {

                    echo \Bitrix\Main\Web\Json::encode($result);

                } else {

                    if (in_array('_escape', $args, true)) {

                        $result['>> DUMP'] = htmlspecialcharsEx($result['>> DUMP']);
                    }

                    $colors = Array(
                        '8ead66',
                        '5b98fd',
                        '945BED',
                        'FD5B73',
                        'fd7d5b'
                    );

                    $color = $colors[array_rand($colors, 1)];

                    $tpl = array();

                    foreach ($dumps as $key => $value) {

                        $value = print_r($value, true);

                        $tpl[] = "<div><b>" . $key . "</b><br/><pre class='_dbg-pre'>" . $value . "</pre></div>";
                    }

                    echo(
                        "<style>
	                  		._dbg{
	                        	background: #" . $color . " !important; padding: 5px !important; 
	                        	border-radius: 3px !important; margin: 5px; !important;
	                  		}
	                  		._dbg *{ font-family: Arial !important; font-size: 16px !important; color: #000000 !important; }
	                  		._dbg-pre b{ color: #FFFEDA !important }
	                  		._dbg-pre{
	                        	background: white !important; word-wrap: break-word !important; padding: 10px !important; border-radius: 3px !important;
	                        	font-family: Arial !important; font-size: 13px !important; line-height: 1.4 !important; color: #5C5C6C !important;
	                  		}
	              		</style>

                    	<div class='_dbg'>
                            <div>Вызов в файле: <b>" . $result['>> FILE'] . "</b></div><br/>
                            " . implode("", $tpl) . "
                        </div>"
                    );
                }
            }
        }
    }
}
?>