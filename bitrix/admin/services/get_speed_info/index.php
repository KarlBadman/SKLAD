<?
use Bitrix\Main\Application;
require "../templates/header.php";
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>
    <div class="header clearfix">
        <h3 class="text-muted">Данные по скорости сайта (по Битриксу)</h3>
    </div>
<?
    $APPLICATION->SetPageProperty('title', 'Данные по скорости сайта (по Битриксу)');;

    $request = Application::getInstance()->getContext()->getRequest();

    require $_SERVER["DOCUMENT_ROOT"] . "/local/cron/xtraspeedtableorm.php";
    $all_data = XtraSpeedTable::getList([
        'select' => ['*'],
        'order' => array('id' => 'ASC')
    ])->fetchAll();

    $red = "rgb(255, 99, 132)";
    $blue = "rgb(54, 162, 23)";

    $labels = array_column($all_data, 'created_at');
    $labels = array_map(function($item){return $item->format('Y-m-d H:i:s');}, $labels);
    $labels = json_encode($labels);

    $speed_time  = array_column($all_data, 'speed_time');

    $speed_time = json_encode($speed_time);

    $data = "[{
        label: 'Данные по скорости в сек',
                    backgroundColor: '" . $red . "',
                    borderColor: '" . $red . "',
                    data: " . $speed_time . ",
                    fill: false,
    }]";
    $chartName = 'Все данные';

?>
    <div class="alert alert-info " role="alert">
        <h4 class="alert-heading">Описание</h4>
        <p>Ниже приводятся данные по скорости, записанные из ответов сервера Битрикс, потому что динамика нигде не хранится.</p>
        <ul>
            <li>Скорость по времени - это среднее время ответа по расчету сервера битрикс за час</li>
            <li>Скорость в процентах - это средний показатель времени натянутый на шкалу от 0 до 2.5 секунд. Т.е. 100 = 2.5 секунды</li>
            <li>created_at - дата опроса сервера статистики</li>
        </ul>
        <hr>
        <p class="mb-0">Подробности в <a target="blank" href="http://192.168.1.21/index.php/Логирование_в_БД">вики</a></p>
    </div>
    <div class="container" >
        <canvas id="myChart"></canvas>
    </div>

    <script>
        var ctx = document.getElementById("myChart").getContext('2d');
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
                            labelString: 'Время'
                        },

                    }]
                }
            }
        });
    </script>



        <table class="table table-striped table-bordered table-hover ">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Скорость по времени</th>
                    <th>Скорость в процентах</th>
                    <th>created_at</th>
                </tr>
            </thead>
            <tbody>
            <? foreach ($all_data as $fetch){ ?>
                    <tr>
                        <? foreach($fetch as $key => $value){
                            echo "<td>" . $value . "</td>";
                         }?>
                    </tr>
             <? } ?>
            </tbody>
        </table>


<?require "../templates/footer.php"?>