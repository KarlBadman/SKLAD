<?php
/**
 * Created by PhpStorm.
 * User: a.kobetskoy
 * Date: 22.06.2018
 * Time: 15:33
 */

abstract class botTools
{
    /**
     * Creates new application instance.
     */
    protected function __construct()
    {

    }

    /**
     * чистим переменные
     * @param string $value
     * @param string $type
     * @return array
     */
    public static function sanitizer($value, $type = 'string') {
        $value = htmlspecialcharsbx($value);
        $value = trim(preg_replace('!\s+!', ' ', $value));
        if ($type == 'int')
            $value = (int)$value;
        return $value;
    }

    /**
     * @param $message
     * @param array $extra
     * @param bool $status
     */
    public static function sendMessage($message, $status = true, $extra = array()) {
        $output = array_merge(array('success' => $status, 'message' => $message), $extra);
        die(json_encode($output));
    }

    /**
     * @param $error_code
     * @param $message
     * @return string
     */
    public static function throwError($error_code, $message = 'nothing to do here', $result = 'no result') {
        die(json_encode(
            array(
                'success' => false,
                'error_code' => $error_code,
                'message' => $message,
                'result' => $result
            )
        ));
    }
}
