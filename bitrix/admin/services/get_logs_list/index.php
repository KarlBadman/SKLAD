<?
use Bitrix\Main\Application;
require "../templates/header.php";
?>
    <div class="header clearfix">
        <h3 class="text-muted">Лог данные из БД (xtra_logs)</h3>
    </div>
<?
    $APPLICATION->SetPageProperty('title', 'Лог данные из БД (xtra_logs)');;

    $request = Application::getInstance()->getContext()->getRequest();
    $remove_id = (int)$request->getQuery('remove_id');

    $log_group = $request->getPost('logGroup');
    $log_group = empty($log_group) ? 'frontend': $log_group;

    $show_formatted_output = $request->getPost('show_formatted_output');
    $show_formatted_output = (!empty($show_formatted_output)) ? true : false;

    $show_short_logs = $request->getPost('show_short_logs');
    $show_short_logs = ($log_group == 'frontend' && !empty($show_short_logs)) ? true : false;
    require $_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/include/xtralogtableorm.php";

    if (!empty($remove_id)){
        XtraLogTable::delete($remove_id);
    }

    $filter = ($log_group == 'frontend') ? array('=exception_type' => 'js_error_handler') : array('!exception_type' => 'js_error_handler');
    if($show_short_logs){
        $filter['>count'] = '1';
    }

    $all_logs = XtraLogTable::getList([
        'select' => ['*'],
        'filter'  => $filter,
        'order' => array('id' => 'DESC')
    ])->fetchAll();
?>
    <div class="alert alert-info " role="alert">
        <h4 class="alert-heading">Описание</h4>
        <p>Ниже приводится таблица логов. Поля:</p>
        <ul>
            <li>ТИП - тип сущности (напр. проблема в user)</li>
            <li>ТИП_ID - идентификатор сущности (id у кого проблема)</li>
            <li>Где проблема - тип исключения (где проблема, напр. order)</li>
            <li>В чём проблема - сущность исключения (что собственно произошло, напр. dont_have_enoth_money)</li>
            <li>INFO - НЕОБЯЗАТЕЛЬНО текстовое поле (сюда пишутся огромные куски массивов или пр.)</li>
            <li>created_at - Первая дата фиксирования проблемы</li>
        </ul>
        <hr>
        <p class="mb-0">Подробности в <a target="blank" href="http://192.168.1.21/index.php/Логирование_в_БД">вики</a></p>
    </div>

    <form method="post" id="changeLogSelection">
        <div class="container-fluid border mt-3 mb-3">
            <div class="form-group pt-1">
            <label for="logGroup" class="col-form-label">По типу логов:</label>
                <select class="custom-select w-25" id="logGroup" name="logGroup" onchange="$('#changeLogSelection').submit()">
                    <option value="backend" <? if($log_group == 'backend'){?>selected<? }?>>backend</option>
                    <option value="frontend" <? if($log_group == 'frontend'){?>selected<? }?>>frontend</option>
                </select>
                <div class="form-check m-2 float-right">
                    <input <? if($log_group == 'backend'){?>disabled<? }?> <? if($show_formatted_output){ echo 'checked';}?> type="checkbox" class="form-check-input" id="show_formatted_output" name="show_formatted_output" onchange="$('#changeLogSelection').submit()">
                    <label class="form-check-label" for="show_formatted_output">Форматированный вывод</label>
                </div>
                <div class="form-check m-2 float-right">
                    <input <? if($log_group == 'backend'){?>disabled<? }?> <? if($show_short_logs){ echo 'checked';}?> type="checkbox" class="form-check-input" id="show_short_logs" name="show_short_logs" onchange="$('#changeLogSelection').submit()">
                    <label class="form-check-label" for="show_short_logs">Только повторяющиеся на фронте</label>
                </div>
            </div>
        </div>

    </form>

        <table class="table table-striped table-bordered table-hover ">
            <thead >
                <tr>
                    <th>ID</th>
                    <th>ТИП</th>
                    <th>ТИП_ID</th>
                    <th>Где проблема</th>
                    <th>В чём проблема</th>
                    <th>INFO</th>
                    <th>created_at</th>
                    <th>Кол-во</th>
                    <th>updated_at</th>
                    <th>удалить?</th>
                </tr>
            </thead>
            <tbody>
            <? foreach ($all_logs as $fetch){ ?>
                    <tr>
                        <? foreach($fetch as $key => $value){
                            if($key=="extra_info" && !empty($value)){
                                $value = str_replace('#', '<br>#',$value);
                                if($show_formatted_output){
                                    $fullinfo = preg_split('/ \| /', $value);
                                    $json_info =  json_decode($fullinfo[0], true);
                                    if(!empty($json_info)) {
                                        $value = '<dl>'
                                            . '<dt class="float-left">filename: </dt><dd class="w-25" style="white-space: nowrap; overflow: hidden;text-overflow: ellipsis;"><a href="' . $json_info['filename'] . '">' . $json_info['filename'] . '</a></dd>'
                                            . '<dt class="float-left">lineno: </dt><dd class="alert-warning">' . $json_info['lineno'] . '</dd>'
                                            . '<dt class="float-left">message: </dt><dd class="alert-danger">' . $json_info['message'] . '</dl></dl>';
                                    } else {
                                        $value = '<p>Нечего выводить - Пустой объект консольной ошибки на клиенте</p>';
                                    }
                                    $client_info = preg_split('/,/', $fullinfo[1]);
                                    foreach($client_info as $line){
                                        $value .= '<p>' . $line . '</p>';
                                    }
                                }

                                $value = '<a class="collapsed" data-toggle="collapse" href="#collapseExample'. $fetch['id'] .'" role="button" aria-expanded="false" aria-controls="collapseExample'. $fetch['id'] .'">развернуть</a>
                                <div class="collapse" id="collapseExample'. $fetch['id'] .'">
                                  <div class="card card-body">' . $value . '</div>
                                </div>';
                            }
                            echo "<td>" . $value . "</td>";
                         }?>
                        <td><a href="?remove_id=<?=$fetch['id'];?>">X</a></td>
                    </tr>
             <? }
             if(empty($key)){?>
                 <tr>
                     <td class="alert alert-success" colspan="9">Хорошо, что тут пусто, это значит что ослеживаемых проблем не обнаружено.</td>
                </tr>
             <? }?>
            </tbody>
        </table>

    <link href="https://cdn.jsdelivr.net/npm/vanilla-datatables@latest/dist/vanilla-dataTables.min.css" rel="stylesheet" type="text/css">
    <script src="https://cdn.jsdelivr.net/npm/vanilla-datatables@latest/dist/vanilla-dataTables.min.js" type="text/javascript"></script>

    <script>
        var table = new DataTable(".table",{'perPage':20});
    </script>
<?require "../templates/footer.php"?>