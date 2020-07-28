<?
use Bitrix\Main\Application;
require "../templates/header.php";
?>
    <div class="header clearfix">
        <h3 class="text-muted">Генерация фидов</h3>
    </div>
<?
    $APPLICATION->SetPageProperty('title', 'Генерация фидов');

    $username = "feedgenarator";
    $password = "eDQDy5FerT3UH4IaIb7L8CUSXnjtbeg5";
    $scheme = ($_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
    $host = $scheme . $_SERVER['SERVER_NAME'] . "/i/";
    $request = Application::getInstance()->getContext()->getRequest();
    $feeds_types = $request->getPost('feeds_types');
    $feeds_answer = array();
    $checkstatuslink = $request->getPost('checkstatuslink');

    if(!empty($feeds_types)){ //перегенерация
        foreach($feeds_types as $feed_type){
            $params = explode('|', $feed_type);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $host . $params[0] . '.php');
            if(!empty($params[1])){
                    curl_setopt($ch, CURLOPT_POSTFIELDS, 'task=' . $params[1]);
            }
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            $res = curl_exec($ch);
            if($errno = curl_errno($ch) && !$res) {
                $error_message = curl_strerror($errno);
                $res = "cURL error ({$errno}):\n {$error_message}";
            }
            $feeds_answer[$feed_type] = $res;
            curl_close($ch);
        }
    }
    //типы фидов и их параметры
    $available_types = array(
        "google" => "Google",
        "google_reviews" => "Google отзывы",
        "google_criteo" => "CRITEO",
        "fb" => "Facebook",
        "trackad" => "Trackad",
        "mytarget" => "MyTarget",
        "yandex|vk" => "VK",
        "yandex" => "Яндекс",
        "ya_margin" => "Себестоимость в яндекс",
        "yandex|dynamic_ads" => "Яндекс динамик адс",
        "yandex|banners_24_35" => "Яндекс баннер 24х53",
        "yandex|banners_34_45" => "Яндекс баннер 34х45",
        "retail" => "Retail rocket",
    );
    $positions = array_keys($available_types);
    $column_coff = 4;
    $last_value = end($available_types);
/* #Слишком много ссылок. Курл долго ковыряется
    if(!empty($checkstatuslink)) {
        $deadUrls = array();
        $xml = simplexml_load_file($checkstatuslink) or die("Error: Cannot create object");
        $nodePath = $xml->channel;
        foreach($nodePath->item as $item) {
            $ns_dc = $item->children('http://base.google.com/ns/1.0');
            $url = explode('?', $ns_dc->link);
            $links[] = $url[0];
        }
        foreach($links as $url) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_POST, false);
            $response = curl_exec($ch);
            if (!curl_errno($ch)) {
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if($httpCode != 200) {
                    $deadUrls[$url] = $httpCode;
                }
            } else {
                echo "CURL NOT STARTED";
            }
            curl_close($ch);
        }
    }
*/
?>
<div class="media">
    <table class="table align-self-start" style="max-width:600px">
    <thead class="thead-dark">
        <tr>
            <th scope="col">Имя файла</th>
            <th scope="col">дата последнего обновления</th>
        </tr>
    </thead>
    <tbody>
        <?
            $files_path = $_SERVER["DOCUMENT_ROOT"] . '/i/';
            $files = scandir($files_path);
            foreach($files as $file){
                $splitted = (explode('.', $file));
                if(in_array($splitted[1], array('csv', 'xml'))){
                    $filetime = filemtime($files_path . $file);
                    $alert = ($filetime < time() - 60 * 60 * 24) ? 'table-danger':'';
                    echo '<tr class="' . $alert . '">';
                    echo '<th scope="row"><a target="_blank" href="/i/' . $file . '">' . $file . '</a></th>';
                    echo '<td>' . date ("d.m.Y H:i:s.", $filetime) . '</td>';
                    echo '</tr>';
                }
            }
        ?>
    </tbody>
    </table>
    
<? /* =count($links);?>
    <div class="media-body ml-3">
        <form class="form-inline" action="#" method="POST">
            <div class="form-group">
                <label for="checkstatuslink">Фид:</label>
                <input type="text" class="form-control" id="checkstatuslink" name="checkstatuslink" placeholder="Ссылка на проверяемый фид">
                <button type="submit" class="btn btn-primary">Проверить</button>
            </div>
        </form>
    </div>*/?>
</div>
<table class="table">
    <thead class="thead-dark">
        <tr>
            <th scope="col">Адрес ссылки</th>
            <th scope="col">Код ответа от сервера</th>
        </tr>
    </thead>
    <tbody>
        <? foreach($deadUrls as $url => $status) {
            echo '<tr>';
            echo '<th scope="row">' . $url . '</th>';
            echo '<td>' . $status . '</td>';
            echo '</tr>';
        } ?>
    </tbody>
</table>

<div class="alert alert-warning alert-dismissible fade show" role="alert">
    Выбирайте ниже, что нужно перегенирировать внепланово
</div>

<div class="container">
    <form class="form-inline" action="#" method="POST">
        <? foreach($available_types as $key=>$val) { ?>
            <? $position = array_search($key, $positions);
            if($position % $column_coff == 0) {
                $end = ($position != 0) ? '</div>' : '';
                echo $end . '<div class="col-4">';
            }?>
            <div class="form-check form-check-inline justify-content-start">
                <input name="feeds_types[<?=$key;?>]" class="form-check-input" type="checkbox" id="inlineCheckbox<?=$key?>" value="<?=$key; ?>">
                <label class="form-check-label" for="inlineCheckbox<?=$key?>"><?=$val; ?></label>
            </div>
            <? if($last_value == $val){
                echo '</div>';
            }?>
        <? } ?>
        <button type="submit" class="btn btn-primary">Перегенировать</button>
    </form>
    <hr>
    <div id="accordion">
        <? foreach($feeds_answer as $key => $answer){?>
            <div class="card">
                <div class="card-header" id="heading<?=$key?>">
                    <h5 class="mb-0">
                        <button class="btn btn-link" data-toggle="collapse" data-target="#collapse<?=$key?>" aria-expanded="true" aria-controls="collapseOne">
                            <?=$available_types[$key];?>
                        </button>
                    </h5>
                </div>
                <div id="collapse<?=$key?>" class="collapse show" aria-labelledby="heading<?=$key?>" data-parent="#accordion">
                    <div class="card-body">
                        <?=$answer;?>
                    </div>
                </div>
            </div>
        <? } ?>
    </div>
</div>

<?require "../templates/footer.php"?>