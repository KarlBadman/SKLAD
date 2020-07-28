<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use \Bitrix\Main\Web\Json;
use \Bitrix\Main\Loader;
use \Dsklad\Config;

/**
 * Компонент для подтверждения номера телефона при оформлении заказа
 * Параметры:
 *      PAYMENTS_SELECTOR - css-селектор кнопок выбора способа оплаты
 *      PAYMENT_CONFIRM_SELECTOR - css-селектор кнопки способа оплаты, при котором нужно подтверждать телефон
 *      PHONE_INPUT_SELECTOR - css-селектор поля с номером телефона
 *      WAIT_TIME - время до повторной отправки
 *      LENGTH - длина кода
 *      TRIES - количество попыток
 *
 * Class SaleConfirmPhoneComponent
 */
class SaleConfirmPhoneComponent extends \CBitrixComponent
{
    private $validActions = [
        'send',  //инициализация нового кода и отправка SMS
        'validate',  //запрос текущего состояния формы подтверждения
        'check'  //проверка кода
    ];
    private $haveErrors = false;  //наличие ошибок в параметрах компонента

    /**
     * @param $arParams
     * @return array
     */
    public function onPrepareComponentParams($arParams)
    {
        $arParams = parent::onPrepareComponentParams($arParams);

        $arParams['WAIT_TIME'] = (int)$arParams['WAIT_TIME'];
        if (empty($arParams['WAIT_TIME'])) {
            $arParams['WAIT_TIME'] = 240;
        }

        $arParams['TRIES'] = (int)$arParams['TRIES'];
        if (empty($arParams['TRIES'])) {
            $arParams['TRIES'] = 3;
        }

        $arParams['LENGTH'] = (int)$arParams['LENGTH'];
        if (empty($arParams['LENGTH'])) {
            $arParams['LENGTH'] = 4;
        }

        $arParams['PAYMENTS_SELECTOR'] = trim($arParams['PAYMENTS_SELECTOR']);
        $arParams['PAYMENT_CONFIRM_SELECTOR'] = trim($arParams['PAYMENT_CONFIRM_SELECTOR']);
        $arParams['PHONE_INPUT_SELECTOR'] = trim($arParams['PHONE_INPUT_SELECTOR']);

        if (
            !$this->request->isAjaxRequest()
            && (
                empty($arParams['PAYMENTS_SELECTOR'])
                || empty($arParams['PAYMENT_CONFIRM_SELECTOR'])
                || empty($arParams['PHONE_INPUT_SELECTOR'])
            )
        ) {
            $this->__ShowError($this->__name.' - Не указаны селекторы элементов!');
            $this->haveErrors = true;
        }

        return $arParams;
    }

    /**
     * @return mixed|void
     */
    public function executeComponent()
    {
        if ($this->request->isAjaxRequest()) {
            $this->ajaxController();
        } else if (!$this->haveErrors) {
            $this->includeComponentTemplate();
        }
    }

    private function ajaxController()
    {
        $json = [];

        try {
            $action = trim($this->request->get('action'));

            if (!empty($action) && in_array($action, $this->validActions)) {
                if (!$this->request->isPost()) {
                    throw new \Exception('Only POST method is allowed!');
                }

                switch ($action) {
                    case 'send':
                        $json = $this->sendAction();
                        break;
                    case 'validate':
                        $json = $this->validateAction();
                        break;
                    case 'check':
                        $json = $this->checkAction();
                        break;
                }
            }
        } catch (\Exception $e) {
            $json = [
                'status' => 'error',
                'component' => 'prmedia:sale.confirm.phone',
                'message' => $e->getMessage()
            ];
        }

        if (!empty($json)) {
            header('Content-Type: application/json');
            echo Json::encode($json);
            die();
        }
    }

    /**
     * Инициализация нового кода и отправка SMS
     * @return array
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     * @throws \Exception
     */
    private function sendAction()
    {
        $phone = self::sanitizePhone($this->request->get('phone'));
        if(empty($phone) || strlen($phone) != 11){
            return [
                'status' => 'error',
                'component' => 'prmedia:sale.confirm.phone',
                'message' => 'phone length is wrong'
            ];
            die;
        }

        $isNew = false; 
        if (empty($_SESSION['CONFIRM_PHONE']['PHONE'])) {
            if (empty($_SESSION['CONFIRM_PHONE']['TIME'])) {
                $_SESSION['CONFIRM_PHONE'] = [
                    'TIME' => time(),
                    'CODE' => $this->generateCode(),
                    'PHONE' => $phone,
                    'TRY' => 0,
                    'CONFIRMED' => false
                ];
                
                $isNew = true;
            }
        } else {
            if ($this->isWaitTimeOver()) {
                $_SESSION['CONFIRM_PHONE'] = [
                    'TIME' => time(),
                    'CODE' => $this->generateCode(),
                    'PHONE' => $phone,
                    'TRY' => 0,
                    'CONFIRMED' => false
                ];

                $isNew = true;
            }
        }

        if ($isNew && Loader::includeModule('sms96ru.sms')) {
            $smsOb = new \Sms96ru\Sms\Sender();
            $resp = $smsOb->sendSms(
                $phone,
                str_replace(
                    '#CODE#',
                    $_SESSION['CONFIRM_PHONE']['CODE'],
                    Config::getOption('UF_CONF_PHONE_MESS')
                )
            );
            if (!empty($resp->error)) {
                throw new \Exception($resp->error);
            }
        }

        $type = 'success';  //Новый код сформирован либо не вышло время
        if (!$this->isPhoneActual($phone)) {
            $type = 'another_phone';
        } else if ($this->isTriesOver()) {
            $type = 'tries_over';
        }

        return [
            'status' => 'ok',
            'time' => $_SESSION['CONFIRM_PHONE']['TIME'],
            'type' => $type,
        ];
    }

    /**
     * Запрос текущего состояния формы подтверждения
     * @return array
     */
    private function validateAction()
    {
        $phone = self::sanitizePhone($this->request->get('phone'));

        $type = 'button';  //отобразить только кнопку в поле телефона

        global $USER;
        if (
            ($this->isPhoneActual($phone) && $_SESSION['CONFIRM_PHONE']['CONFIRMED'])
            || ($USER->IsAuthorized() && self::isPhoneConfirmedForUser($USER->GetID(), $phone))
        ) {
            $type = 'confirmed';  //телефон уже подтверждён
        } else if (!empty($_SESSION['CONFIRM_PHONE']['TIME'])) {
            if (!$this->isWaitTimeOver()) {
                if ($this->isTriesOver()) {
                    $type = 'wait_tries';
                } else if (!$this->isPhoneActual($phone)) {
                    $type = 'wait_phone';
                } else {
                    $type = 'full';  //отобразить полную форму ввода кода
                }
            }
        }

        return [
            'status' => 'ok',
            'type' => $type,
        ];
    }

    /**
     * Проверка кода
     * @return array
     * @throws \Exception
     */
    private function checkAction()
    {
        $phone = self::sanitizePhone($this->request->get('phone'));
        $code = trim($this->request->get('code'));

        $type = 'success';
        if (!empty($_SESSION['CONFIRM_PHONE']['TIME'])) {
            if (!$this->isWaitTimeOver()) {
                if ($this->isTriesOver()) {
                    $type = 'tries_over';
                } else if (!$this->isPhoneActual($phone)) {
                    $type = 'another_phone';
                } else if ($code != $_SESSION['CONFIRM_PHONE']['CODE']) {
                    $type = 'wrong_code';
                    $_SESSION['CONFIRM_PHONE']['TRY']++;

                    if ($this->isTriesOver()) {
                        $type = 'tries_over';
                    }
                }
            }
        } else {
            $type = 'not_send';
        }

        if ($type == 'success') {
            $_SESSION['CONFIRM_PHONE']['CONFIRMED'] = true;

            global $USER;
            if ($USER->IsAuthorized()) {
                self::savePhoneForUser($USER->GetID(), $phone);
            }
        }

        return [
            'status' => 'ok',
            'type' => $type,
        ];
    }

    /**
     * Очистка номера телефона от лишних символов
     * @param $phone
     * @return null|string|string[]
     */
    private static function sanitizePhone($phone)
    {
        return preg_replace('/\D/', '', $phone);
    }

    /**
     * Закончилось время ожидания ввода кода
     * @return bool
     */
    private function isWaitTimeOver()
    {
        $timeStart = \DateTime::createFromFormat('U', $_SESSION['CONFIRM_PHONE']['TIME']);
        $timeNow = new \DateTime();
        $timeDiffInSec = $timeStart->diff($timeNow)->i * 60 + $timeStart->diff($timeNow)->s;
        if ($timeDiffInSec >= $this->arParams['WAIT_TIME']) {
            return true;
        }

        return false;
    }

    /**
     * Телефон не изменился
     * @param $phone
     * @return bool
     */
    private function isPhoneActual($phone)
    {
        return ($phone == $_SESSION['CONFIRM_PHONE']['PHONE']);
    }

    /**
     * Закончились попытки ввода кода
     * @return bool
     */
    private function isTriesOver()
    {
        return ($_SESSION['CONFIRM_PHONE']['TRY'] >= $this->arParams['TRIES']);
    }

    /**
     * Генерация кода
     * @return string
     */
    private function generateCode()
    {
        $length = $this->arParams['LENGTH'];
        $mask = '%1$0'.$length.'d';
        return sprintf($mask, rand(1, pow(10, $length) - 1));
    }

    /**
     * Телефонный номер сохранён как "подтверждённый" для этого пользователя
     * @param $userId
     * @param $phone
     * @return bool
     */
    private static function isPhoneConfirmedForUser($userId, $phone)
    {
        $db = \CUser::GetList(
            ($by = 'ID'),
            ($order = 'ASC'),
            [
                '=ID' => $userId,
                '!UF_CONFIRMED_PHONE' => false,
                '=UF_CONFIRMED_PHONE' => $phone
            ],
            [
                'SELECT' => [
                    'ID'
                ]
            ]
        );

        if ($db->SelectedRowsCount() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Сохраняем телефонный номер как "подтверждённый" для этого пользователя
     * @param $userId
     * @param $phone
     * @throws \Exception
     */
    private static function savePhoneForUser($userId, $phone)
    {
        $user = new \CUser;

        $user->Update(
            $userId,
            [
                'UF_CONFIRMED_PHONE' => $phone
            ]
        );

        if (!empty($user->LAST_ERROR)) {
            throw new \Exception($user->LAST_ERROR);
        }
    }
}