<?
    require "../templates/header.php";    
    require "planfix.php";
    
    // Set page properties
    $APPLICATION->SetPageProperty('title', 'Интеграция с PLANFIX');
    $PlanFixClient = new PlanFixTaskList();
    $taskFilter = []; $statusFilter = [];

    if ($_REQUEST['onuser'])
        $taskFilter['user']['id'] = intVal($_REQUEST['onuser']);

    if ($_REQUEST['onproject'])
        $taskFilter['project']['id'] = intVal($_REQUEST['onproject']);
        
    if ($_REQUEST['onprocess'])
        $statusFilter['taskStatusSet']['id'] = intVal($_REQUEST['onprocess']);
        
    if ($_REQUEST['onstatus'])
        $taskFilter['status'] = intVal($_REQUEST['onstatus']);

    $taskFilter['target'] = 'all'; // все задачи
    $taskFilter['project']['withSubprojects'] = '1'; // включая задачи подпроектов

    $users = $PlanFixClient->GetUsers();
    $contacts = $PlanFixClient->GetContacts();

    $projects = $PlanFixClient->GetProjects();
    $taskList = $PlanFixClient->GetTaskList($taskFilter);
    if (!empty($taskList['task']) && !isset($taskList['task'][0])) {
        $tmpTaskList = $taskList['task']; 
        unset($taskList['task']);
        $taskList['task'][0] = $tmpTaskList;
    }
    
    $taskStatusListProcess = $PlanFixClient->GetTaskStatusList();
    if ($statusFilter['taskStatusSet']['id'])
        $taskStatusListByprocess = $PlanFixClient->GetTaskStatusListByProces($statusFilter);
    $userGroupsList = $PlanFixClient->GetUserGroupsList();

    echo "<pre>";
    //print_r($taskStatusListByprocess);
    echo "</pre>";
    //die();

?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-9">
            <div class="header clearfix">
                <h3 class="text-muted">PlanFix список задач для распечатки</h3>
            </div>
            <div class="alert alert-info">
                <h4 class="text-center">Здравствуй юный падаван! <br> С намереньем благим, печати карточек задач, пришел сюда ты!</h4>
                <p>Действуй тогда не боясь, Джедаев великая сила поможет тебе</p>
                <form action="/bitrix/admin/services/planfix/">
                    <ul>
                        <?if (!empty($projects['project'])) : ?>
                            <li>
                                Здесь фильтр по проектам выбрать можешь ты
                                <select name="onproject" onchange="(function () { $('form').submit(); })();">
                                    <option value="">---------</option>
                                    <?foreach ($projects['project'] as $project) : ?>
                                        <option <?if ($_REQUEST['onproject'] == $project['id']) : ?>selected<?endif;?> value="<?=$project['id']?>"><?=$project['title']?></option>
                                    <?endforeach;?>
                                </select>
                            </li>
                        <?endif;?>
                        <?if (!empty($users['user'])) : ?>
                            <li>
                                Известных мне Джедаев это список, используй его
                                <select name="onuser" onchange="(function () { $('form').submit(); })();">
                                    <option value="">----------</option>
                                    <?foreach ($users['user'] as $user) : ?>
                                        <option <?if ($_REQUEST['onuser'] == $user['id']) : ?>selected<?endif;?> value="<?=$user['id']?>"><?=$user['lastName']?> <?=$user['name']?> - <?=$user['email']?></option>
                                    <?endforeach;?>
                                </select>
                            </li>
                        <?endif;?>
                        <?if (!empty($taskStatusListProcess['taskStatusSet']) && is_array($taskStatusListProcess['taskStatusSet'])) : ?>
                            <li>
                                Процессов доступных это список, выбери нужный
                                <select name="onprocess" onchange="(function () { $('form').submit(); })();">
                                    <option value="">----------</option>
                                    <?foreach ($taskStatusListProcess['taskStatusSet'] as $process) : ?>
                                        <option <?if ($_REQUEST['onprocess'] == $process['id']) : ?>selected<?endif;?> value="<?=$process['id']?>"><?=$process['name']?></option>
                                    <?endforeach;?>
                                </select>
                            </li>
                            <?if (!empty($taskStatusListByprocess['taskStatus']) && is_array($taskStatusListByprocess['taskStatus'])) : ?>
                                <li>
                                    Процессов статусы что подходят тебе
                                    <select name="onstatus" onchange="(function () { $('form').submit(); })();">
                                        <option value="">----------</option>
                                        <?foreach ($taskStatusListByprocess['taskStatus'] as $status) : ?>
                                            <option <?if ($_REQUEST['onstatus'] == $status['id']) : ?>selected<?endif;?> value="<?=$status['id']?>"><?=$status['name']?></option>
                                        <?endforeach;?>
                                    </select>
                                </li>
                            <?endif;?>
                        <?endif;?>
                        <li>Внимателен будь, юный падаван, в этом месте великая сила заключена! <a href="#" class="btn btn-default print-btn">Печати кнопка</a></li>
                        <li>Сомнения коли твой праведный путь затрудняют, обо всем распроси <a href="mailto:svs@ooott.ru">Квай-Гон Джина</a> он все пояснит и раскажет тебе</li>
                    </ul>
                </form>
            </div>
            
            <div class="print-container">
                    
                <!-- TASK -->
                <?if (!empty($taskList['task']) && is_array($taskList['task'])) : ?>
                    
                    <?foreach ($taskList['task'] as $index => $task) : ?>
                    
                        <!-- Print item -->
                        <div class="print-item">
                            <div class="print-item-title"><?=TruncateText($task['title'], 68);?></div>
                            <table class="print-item-content">
                                <tr>
                                    <td class="even"><?=TruncateText($task['project']['title'], 15)?></td>
                                    <td class="even">#<?=$task['general']?></td>
                                </tr>
                                <tr class="odd">
                                    <td class="even"></td>
                                    <?
                                    switch ($task['durationUnit']) {

                                        case '':
                                            $durationUnit = '-';
                                            break;
                                        case '0':
                                            $durationUnit = 'м';
                                            break;
                                        case '1':
                                            $durationUnit = 'ч';
                                            break;
                                        case '2':
                                            $durationUnit = 'д';
                                            break;
                                    }
                                    ?>
                                    <td class="even"><?=$task['duration'].$durationUnit?></td>
                                </tr>
                            </table>
                        </div>
                    <?endforeach;?>
                <?endif?>
            </div>
        </div>
    </div>
</div>

<?require "../templates/footer.php";?>