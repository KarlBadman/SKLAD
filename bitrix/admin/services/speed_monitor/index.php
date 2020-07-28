<?
use Bitrix\Main\Application;
require "../templates/header.php";
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>
    <div class="header clearfix">
        <h3 class="text-muted">Данные по скорости сайта (по Битриксу)</h3>
    </div>
<?
$APPLICATION->SetPageProperty('title', 'Монитор данных по PSI');
require $_SERVER["DOCUMENT_ROOT"] . "/local/cron/xtraspeedmonitortableorm.php";

    $ignored_avg = array(
        'id',
        'domain',
        'type',
        'response',
        'created_at',
    );
    $grouping_variants = array(
        'by_hours'  => 'по часам',
        'by_days'   => 'по дням',
        'by_weeks'  => 'по неделям',
        'by_month'  => 'по месяцам'
    );
    $page_types = array(
        'home' => 'Главная',
        'product' => 'Страница товара',
    );
    $defaults = array('https://www.dsklad.ru', 'https://floberis.ru');
    $paint_colors = array(
        "green"     => 'rgb(75, 192, 192)',
        "blue"      => 'rgb(54, 162, 235)',
        "red"       => 'rgb(255, 99, 132)',
    );
    $colors = array(
        "https://www.dsklad.ru" => '#2c6bf6',
        "https://www.divan.ru" => '#000000',
        "https://hoff.ru" => '#e60000',
        "https://www.cosmorelax.ru" => '#f1e9dc',
        "https://www.ikea.com/ru/ru" => '#fcd500',
        "https://floberis.ru" => '#e14499',
        "https://lifemebel.ru" => '#3bb038',
        "https://dg-home.ru" => '#2fa07b',
        "https://www.dsklad.ru/catalog/stylia/stul_eames_style_dsw/" => '#2c6bf6',
        "http://www.prokuhni.com" => '#ff8520',
    );

    $paint_parameters_grouping = array(
        'Первая отрисовка контента (FCP)' => array(
                'LE_FCP_H',
                'LE_FCP_M',
                'LE_FCP_S'
        ),
        'Первая задержка ввода (FID)' => array(
                'LE_FID_H',
                'LE_FID_M',
                'LE_FID_S',
        ),
        'Origin Summary Первая отрисовка контента (FCP)' => array(
                'OE_FCP_H',
                'OE_FCP_M',
                'OE_FCP_S',
        ),
        'Origin Summary Первая задержка ввода (FID)' => array(
                'OE_FID_H',
                'OE_FID_M',
                'OE_FID_S',
        ),
    );

    $detail_chartlist_excluding = array(
        'audits_FCP3G_score',
        'audits_BT_score',
        'audits_SI_score',
        'audits_SI_time',
        'audits_MWB_score',
        'audits_FCP_score',
        'audits_DS',
        'audits_FMP_score',
        'audits_TTF_score',
        'audits_interactive_score',
        'timing', //проверить корректность данных
    );

    $request = Application::getInstance()->getContext()->getRequest();
    $selected_grouping = $request->getPost('grouping');
    $selected_grouping = (!empty($selected_grouping)) ? $selected_grouping : 'by_hours';
    $paint_domain = $request->getPost('paint_domain');
    $paint_domain = (!empty($paint_domain)) ? $paint_domain : $defaults[0];
    $paint_parameter = $request->getPost('paint_parameter');
    $paint_parameter = (!empty($paint_parameter)) ? $paint_parameter : 'Первая отрисовка контента (FCP)';
    $audit_domain = $request->getPost('audit_domain');
    $audit_domain = (!empty($audit_domain)) ? $audit_domain : 'all';
    $audit_parameter = $request->getPost('audit_parameter');
    $audit_parameter = (!empty($audit_parameter)) ? $audit_parameter : 'speed_index';
    $startdate = $request->getPost('startdate');
    $startdate = (!empty($startdate)) ? $startdate : Date('Y-m-d', strtotime("-6 days"));
    $enddate = $request->getPost('enddate');
    $enddate = (!empty($enddate)) ? $enddate  : Date('Y-m-d');
    $page_type = $request->getPost('page_type');
    $page_type = (!empty($page_type)) ? $page_type : 'home';
    $show_full_chartlist = $request->getPost('getDetailChartlist');
    $show_full_chartlist = (!empty($show_full_chartlist)) ? true : false;


$filter = array();
    if($selected_grouping == 'by_hours'){
        $filter = array(
            ">=created_at" => new \Bitrix\Main\Type\DateTime(
                date($startdate . ' 00:00:00'),
                'Y-m-d H:i:s'
            ),
            "<=created_at" => new \Bitrix\Main\Type\DateTime(
                date($enddate . ' 23:59:59'),
                'Y-m-d H:i:s'
            ),
        );
    }
    $filter['type'] = $page_type;
    $all_data = XtraSpeedMonitorTable::getList(array(
        'select' =>  array('*'),
        'filter' => $filter,
        'order' => array('id' => 'ASC'),
    ))->fetchAll();

    $structure = XtraSpeedMonitorTable::getMap();
    $data = $data_by_url = array();
    foreach ($all_data as $key => &$entry) {
        switch ($selected_grouping){
            case 'by_days':
                $entry['created_at'] = $entry['created_at']->format('Y-m-d');
                $switched_key = $entry['created_at'];
                break;
            case 'by_weeks':
                $entry['created_at'] = $entry['created_at']->format('Y-m W');
                $switched_key = $entry['created_at'];
                break;
            case 'by_month':
                $entry['created_at'] = $entry['created_at']->format('Y-m');
                $switched_key = $entry['created_at'];
                break;
            default:
                $entry['created_at'] = $entry['created_at']->format('Y-m-d H:00');
                $switched_key = $key;
        }
        if (!empty($data_by_url[$entry['domain']][$switched_key])) {
            foreach($data_by_url[$entry['domain']][$switched_key] as $single_key=>$single_parameter){
                if(!in_array($single_key, $ignored_avg)){
                    $data_by_url[$entry['domain']][$switched_key][$single_key] = ($entry[$single_key] + $single_parameter)/2;
                }
            }
        } else {
            $data_by_url[$entry['domain']][$switched_key] = $entry;
        }
        $labels[] = $entry['created_at'];
    }

    $labels = json_encode(array_values(array_unique($labels))); ?>

    <div class="alert alert-info " role="alert" style="word-break: break-all;">
        <h4 class="alert-heading" data-toggle="collapse" data-target="#collapseDesc" aria-expanded="false" aria-controls="collapseDesc">Описание &#9650;</h4>
        <div id="collapseDesc" class="collapse">
            <p>Ниже приводятся данные по Оценке скорости загрузки, основанные на данных, полученных методом имитации загрузки сайта с помощью инструмента Lighthouse.</p>
            <p>Подробности по каждой измеряемой метрике смотри в lighthouse, google page speed insight или ищи тут <a href="https://developers.google.com/web/tools/lighthouse/audits/critical-request-chains">https://developers.google.com/web/tools/lighthouse/audits/critical-request-chains</a></p>
            <ul>
                <li>По умолчанию показатель скорости отрисовки -Метрики загрузки страниц конечными пользователями.</li>
                <li>Origin summary -Метрики совокупного пользовательского опыта загрузки страниц источника.</li>
                <li>mainthread-work-breakdown - <a href="https://developers.google.com/web/updates/2018/05/lighthouse">https://developers.google.com/web/updates/2018/05/lighthouse</a></li>
                <li><a href="https://siteclinic.ru/blog/technical-aspects/obnovlenie-pagespeed-insights/">https://siteclinic.ru/blog/technical-aspects/obnovlenie-pagespeed-insights/</a></li>
            </ul>
        </div>
    </div>
    <form method="post" id="changeGroup" class="form-inline">
        <div class="container-fluid border mt-3 mb-3">
            <div class="form-group pt-1">
                <label>Дата начального периода</label>
                <input type="date" <? if($selected_grouping != 'by_hours'){ echo 'disabled';} ?> name="startdate" max="2050-12-31" min="2019-04-11" class="form-control ml-2" value="<?=$startdate?>"  onchange="$('#changeGroup').submit()">
                <label class="ml-2">Дата конечного периода</label>
                <input type="date" <? if($selected_grouping != 'by_hours'){ echo 'disabled';} ?> name="enddate" min="2019-04-11" max="2050-12-31" class="form-control ml-2" value="<?=$enddate?>"  onchange="$('#changeGroup').submit()">
                <label for="page_type" class="my-1 ml-3 mr-2 col-form-label">Тип страницы:</label>
                <select  class="custom-select my-1 " id="page_type" name="page_type" onchange="$('#changeGroup').submit()">
                    <? foreach($page_types  as $key=>$value){?>
                        <option value="<?=$key?>" <? if($key == $page_type){?>selected<? }?>><?=$value?></option>
                    <? } ?>
                </select>

                <label for="grouping" class="my-1 ml-3 mr-2 col-form-label">Тип группировки:</label>
                <select  class="custom-select my-1 " id="grouping" name="grouping" onchange="$('#changeGroup').submit()">
                    <? foreach($grouping_variants as $key=>$value){?>
                        <option value="<?=$key?>" <? if($key == $selected_grouping){?>selected<? }?>><?=$value?></option>
                    <? } ?>
                </select>
            </div>
        </div>

        <div class=" float-right border "  style="width:100%">
            <div class="form-row mt-3 ml-2">
                <label for="audit_parameter" class="my-1 mr-2 col-form-label">Параметр:</label>
                <select  class="custom-select my-1 mr-sm-2" id="audit_parameter" name="audit_parameter" onchange="$('#changeGroup').submit()">
                    <? foreach($structure as $key=>$value) {
                        if(substr_count($key, 'audits_') > 0 || $key == 'timing' || $key == 'speed_index') { ?>
                            <option value="<?=$key?>" <? if($key == $audit_parameter){?>selected<? }?>><?=$value['title']?></option>
                        <? }
                    }?>
                </select>
                <label for="audit_domain" class="my-1 mr-2 col-form-label">Домен:</label>
                <select  class="custom-select my-1 mr-sm-2" id="audit_domain" name="audit_domain" onchange="$('#changeGroup').submit()">
                    <option value="all" <? if($audit_domain == 'all'){?>selected<? }?>>Все</option>
                    <? foreach(array_keys($data_by_url) as $domain){?>
                        <option value="<?=$domain?>" <? if($domain == $audit_domain){?>selected<? }?>><?=$domain?></option>
                    <? } ?>
                </select>
            </div>
            <?
            $data = array();
            if($audit_domain != 'all'){
                $data_by_url = array_intersect_key($data_by_url, array_flip(array($audit_domain)));
            }
            $domain_colors = array();
            foreach ($data_by_url as $key => $values) {
                $domain_colors[$key] = $colors[$key];
                $param_array = array(
                    'label' => $key,
                    'backgroundColor' => $domain_colors[$key],
                    'borderColor' => $domain_colors[$key],
                    'data' =>  array_values(array_map(function($item) use ($audit_parameter) {
                        if($audit_parameter == 'speed_index'){
                            $score = round($item[$audit_parameter] * 100);
                        } else {
                            $score = $item[$audit_parameter];
                        }
                        return array('x'=>$item['created_at'], 'y'=>$score);
                    }, $values)),
                    'fill' => false,
                    'borderDash' => [5, 5],
                );
                if(in_array($key, $defaults)){
                    unset($param_array['borderDash']);
                }
                $data[] = $param_array;
            }
            $data = json_encode($data);
            $chartName = $structure[$audit_parameter]['title'] . ' ' .$grouping_variants[$selected_grouping];
            ?>
            <canvas id="auditChart"></canvas>
            <script>
                var ctx = document.getElementById("auditChart").getContext('2d');
                var myChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: <?=$labels?>,
                        datasets: <?=$data?>
                    },
                    options: {
                        responsive: true,
                        title: {
                            display: true,
                            text: '<?=$chartName?>'
                        },
                        tooltips: {
                            mode: 'index',
                            intersect: false,
                        },
                        hover: {
                            mode: 'nearest',
                            intersect: true
                        },
                        scales: {

                            yAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: '<?= $structure[$audit_parameter]['title'] ?>'
                                },
                            }]
                        }
                    }
                });
            </script>
        </div>

        <div class="form-check m-3">
            <input <? if($show_full_chartlist){ echo 'checked';}?> type="checkbox" class="form-check-input" id="getDetailChartlist" name="getDetailChartlist" onchange="$('#changeGroup').submit()">
            <label class="form-check-label" for="getDetailChartlist">Получить все сводные графики за период на одном полотне</label>
        </div>

        <? if($show_full_chartlist && $audit_domain != 'all') { ?>
        <div class="float-left border " style="width:100%">
            <? foreach($structure as $audit_parameter=>$svalue) {
                $data = array();
                if(!in_array($audit_parameter, $detail_chartlist_excluding) && (substr_count($audit_parameter, 'audits_') > 0 || $audit_parameter == 'timing')) { ?>
                    <?
                    $domain_colors = array();
                    foreach ($data_by_url as $key => $values) {
                        $domain_colors[$key] = $colors[$key];
                        $param_array = array(
                            'label' => $key,
                            'backgroundColor' => $domain_colors[$key],
                            'borderColor' => $domain_colors[$key],
                            'data' =>  array_values(array_map(function($item) use ($audit_parameter) {
                                if($audit_parameter == 'speed_index'){
                                    $score = round($item[$audit_parameter] * 100);
                                } else {
                                    $score = $item[$audit_parameter];
                                }
                                return array('x'=>$item['created_at'], 'y'=>$score);
                            }, $values)),
                            'fill' => false,
                            'borderDash' => [5, 5],
                        );
                        if(in_array($key, $defaults)){
                            unset($param_array['borderDash']);
                        }
                        $data[] = $param_array;
                    }
                    $data = json_encode($data);
                    $chartName = $structure[$audit_parameter]['title'] . ' ' .$grouping_variants[$selected_grouping];
                    ?>
                <div class="float-left border " style="width:33%">
                    <canvas id="<?=$audit_parameter?>"></canvas>
                    <script>
                        var ctx = document.getElementById("<?=$audit_parameter?>").getContext('2d');
                        var myChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: <?=$labels?>,
                                datasets: <?=$data?>
                            },
                            options: {
                                responsive: true,
                                title: {
                                    display: true,
                                    text: '<?=$chartName?>'
                                },
                                tooltips: {
                                    mode: 'index',
                                    intersect: false,
                                },
                                hover: {
                                    mode: 'nearest',
                                    intersect: true
                                },
                                scales: {

                                    yAxes: [{
                                        display: true,
                                        scaleLabel: {
                                            display: true,
                                            labelString: '<?= $structure[$audit_parameter]['title'] ?>'
                                        },
                                    }]
                                }
                            }
                        });
                    </script>
                </div>

                <? }
            }?>
        </div>
        <? }?>

        <div class="float-left border " style="width:100%">
            <div class="form-row  mt-3 ml-2">
                <label for="paint_parameter" class="my-1 mr-2 col-form-label">Параметр:</label>
                <select  class="custom-select my-1 mr-sm-2" id="paint_parameter" name="paint_parameter" onchange="$('#changeGroup').submit()">
                    <? foreach(array_keys($paint_parameters_grouping) as $paint_key) {?>
                        <option value="<?=$paint_key?>" <? if($paint_key == $paint_parameter){?>selected<? }?>><?=$paint_key?></option>
                    <? } ?>
                </select>
                <label for="paint_domain" class="my-1 mr-2 col-form-label">Домен:</label>
                <select  class="custom-select my-1 mr-sm-2" id="paint_domain" name="paint_domain" onchange="$('#changeGroup').submit()">
                    <? foreach(array_keys($data_by_url) as $domain){?>
                        <option value="<?=$domain?>" <? if($domain == $paint_domain){?>selected<? }?>><?=$domain?></option>
                    <? } ?>
                </select>
            </div>
            <?
            $data = $param_array = array();
            $data_by_url_paint = array_intersect_key($data_by_url, array_flip(array($paint_domain)));
            foreach ($data_by_url_paint as $key => $values) {
                $i = 0;
                foreach($paint_parameters_grouping[$paint_parameter] as $sub_key => $sub_parameter){
                    $param_array[] = array(
                        'label' => $structure[$sub_parameter]['title'],
                        'backgroundColor' => array_values($paint_colors)[$i],
                        'stack' => 'Stack 0',
                        'data' =>  array_values(array_map(function($item) use ($sub_parameter) { return array('x'=>$item['created_at'], 'y'=>$item[$sub_parameter]);} , $values)),
                    );
                    $i++;
                }
                $data = $param_array;
            }
            $data = json_encode($data);
            $chartName = $structure[$paint_parameter]['title'] . ' ' .$grouping_variants[$selected_grouping];
            ?>
            <canvas id="paintChart"></canvas>
            <script>
                var ctx = document.getElementById("paintChart").getContext('2d');
                var myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: <?=$labels?>,
                        datasets: <?=$data?>
                    },
                    options: {
                        responsive: true,
                        title: {
                            display: true,
                            text: '<?=$chartName?>'
                        },
                        tooltips: {
                            mode: 'index',
                            intersect: false,
                        },
                        hover: {
                            mode: 'nearest',
                            intersect: true
                        },
                        scales: {

                            yAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Показатель скорости отрисовки'
                                },
                            }]
                        }
                    }
                });
            </script>
        </div>
    </form>
<?require "../templates/footer.php"?>