<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
\Bitrix\Main\Page\Asset::getInstance()->addJs('/local/templates/order/js/jquery.inputmask.min.js');
\Bitrix\Main\Page\Asset::getInstance()->addJs('/local/templates/order/js/parsley.min.js');
\Bitrix\Main\Page\Asset::getInstance()->addJs('/local/templates/order/js/parsley-ru.js');


use \Bitrix\Main\Web;
use \Bitrix\Main\UserTable;
use \Bitrix\Main\Mail\Internal\EventMessageTable;

class UserAutologinFormComponent extends \CBitrixComponent
{
    public function executeComponent()
    {
        if ($this->request->isAjaxRequest()) {
            $isEmailError = false;

            try {
                $GLOBALS['APPLICATION']->RestartBuffer();
                while (ob_get_level()) {
                    ob_end_clean();
                }

                $email = trim(strtolower($this->request->get('email')));
                if (!preg_match('/^[-._+А-Яа-яA-Za-z0-9]*@(?:[A-zА-яА-Яа-яA-Za-z0-9][-А-Яа-яA-Za-z0-9]*\.)+[А-Яа-яA-Za-z]{2,6}$/u', $email)) {
                    $isEmailError = true;
                    throw new \Exception('Некорректный E-Mail: ['.$email.']');
                }

                $dbUser = UserTable::getList([
                    'filter' => [
                        'EMAIL' => $email
                    ],
                    'select' => ['ID']
                ]);

                if (empty($dbUser->getSelectedRowsCount())) {
                    $user = new \CUser;
                    $password = randString(
                        7,
                        [
                            'abcdefghijklnmopqrstuvwxyz',
                            'ABCDEFGHIJKLNMOPQRSTUVWXYZ',
                            '0123456789',
                            ',.<>/?;:"[]{}|`~!@#$%^&*()-_+='
                        ]
                    );
                    $userFields = Array(
                        'EMAIL' => $email,
                        'LOGIN' => $email,
                        'PASSWORD' => $password,
                        'CONFIRM_PASSWORD' => $password
                    );
                    $userId = $user->Add($userFields);
                    if ($userId === false) {
                        throw new \Exception(
                            print_r(
                                [
                                    'Ошибка создания нового пользователя: ['.$email.']',
                                    $user->LAST_ERROR
                                ],
                                true
                            )
                        );
                    }
                }

                $eventParams = [
                    'EMAIL' => $email,
                ];

                $template = EventMessageTable::getRow([
                    'filter' => [
                        '=ACTIVE' => 'Y',
                        '=EVENT_NAME' => 'USER_AUTOLOGIN',
                        '=LID' => SITE_ID,
                        '=LANGUAGE_ID' => LANGUAGE_ID
                    ],
                    'select' => ['ID']
                ]);

                if (!empty($template)) {
                    //Старый метод отправки, из-за ограничений модуля автологина
                    $result = \CEvent::Send(
                        'USER_AUTOLOGIN',
                        SITE_ID,
                        $eventParams,
                        'N',
                        $template['ID'],
                        '',
                        LANGUAGE_ID
                    );

                    if (empty($result)) {
                        throw new \Exception(
                            print_r(
                                [
                                    'Не удалось создать событие на отправку письма',
                                    $eventParams,
                                    $template
                                ],
                                true
                            )
                        );
                    }
                } else {
                    throw new \Exception('Нет почтового шаблона: USER_AUTOLOGIN('.$email.', '.SITE_ID.', '.LANGUAGE_ID.')');
                }

                echo Web\Json::encode([
                    'status' => 'ok',
                    'email' => $email
                ]);
            } catch (\Exception $e) {
                if ($isEmailError) {
                    echo Web\Json::encode(['status' => 'error_email']);
                } else {
                    echo Web\Json::encode(['status' => 'error']);
                }

                \Bitrix\Main\Diag\Debug::writeToFile(
                    [
                        'Дата' => date('d.m.Y H:i:s').substr((string)microtime(), 1, 8),
                        'Код ошибки' => $e->getCode(),
                        'Сообщение' => $e->getMessage(),
                        'POST' => $this->request->getPostList()->toArray(),
                        'Страница' => $_SERVER['REQUEST_URI'],
                    ],
                    'UserAutologinFormComponent AJAX',
                    'bitrix/user_autologin_form_log.txt'
                );

                die();
            }

            die();
        }

        $this->includeComponentTemplate();
    }
}