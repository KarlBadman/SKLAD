<?php
/* Place in /bitrix/php_interface/retailcrm */
class Logger
{
    private $logPath;
    private $files;
    
    public function __construct($logPath = '/bitrix/modules/intaro.retailcrm/log', $files = 3)
    {
        $this->logPath = $logPath;
        $this->files = $files;
    }
    
    public function write($dump, $file = 'info')
    {
        $rsSites = CSite::GetList($by, $sort, array('DEF' => 'Y'));
        $ar = $rsSites->Fetch();
        if (!is_dir($ar['ABS_DOC_ROOT'] . $this->logPath . '/')) {
            mkdir($ar['ABS_DOC_ROOT'] . $this->logPath . '/');
        }
        $file = $ar['ABS_DOC_ROOT'] . $this->logPath . '/' . $file . '.log';  

        $data['TIME'] = date('Y-m-d H:i:s');
        $data['DATA'] = $dump;
        
        $send_email = false;
        if ($dump['errors']) {
            foreach ($dump['errors'] as $error) {
                if (strpos($error, 'already exists') === false) {
                    $send_email = true;
                }
            }
        }
        
        if ($send_email) {
            $email = 'dev@ooott.ru';
            $siteUrl = $_SERVER['SERVER_NAME'];
            $subject = "[$siteUrl] Ошибка при работе модуля retailcrm";
            $message = $subject . "\n" . print_r($data, true);
            mail($email, $subject, $message);
        }

        $f = fopen($file, "a+");
        fwrite($f, print_r($data, true));
        fclose($f); 

        // if filesize more than 5 Mb rotate it
        if (filesize($file) > 5242880) {
            $this->rotate($file);
        } 
    }

    private function rotate($file)
    {
        $path = pathinfo($file);
        $rotate = implode('', array(
            $path['dirname'],
            '/',
            $path['filename'],
            '_',
            date('Y-m-d_H:i:s'),
            '.',
            $path['extension']
        ));

        copy($file, $rotate);
        $this->clean($file);

        $files = glob($path['dirname'] . '/' . $path['filename'] . "*" . ".log");

        if (0 === $this->files) {
            return;
        }

        if (count($files) > $this->files) {
            natsort($files);
            $files = array_reverse($files);
            foreach (array_slice($files, $this->files) as $log) {
                if (is_writable($log)) {
                    unlink($log);
                }
            }
        }
    }

    private function clean($file)
    {
        file_put_contents($file, '');
    }

}
