<div class="header clearfix">
    <h3 class="text-muted">Инстаграм менеджер публикаций (для блока на главной сранице)</h3>
</div>
<div class="alert alert-info">
    <h4>Добро пожаловать в редактор менеджера публикаций инстаграм для сайта dsklad.ru</h4>
    <ol>
        <?if (!$client->isAuthorised) : ?>
            <li>Для начала вам необходимо авторизоваться на сервисе <a target="_blank" href="/bitrix/admin/services/instagram/?ACTION=CHECK_AUTH">Instagram</a> вся магия начнется после!</li>
            <li>Если авторизовался нажми <a href="/bitrix/admin/services/instagram/">СЮДА</a> завеса тайны точно пропадет, и начнется чистое волшебство!</li>
        <?else : ?>
            <li>Отлично!!! Поздравляю, у тебя все получилось! Твои усилия будут вознаграждены! Так держать! Ты молодец! Ты лучш(ий, ая, ее)!</li>
            <li>Теперь, когда перед тобой открыты все мыслимые горизонты, ты можешь поступать на свое усмотрение, учитывая <b>НИЖЕСЛЕДУЩЕЕ</b></li>
            <li>Тебе доступна <a href="/bitrix/admin/services/instagram/?ACTION=GET_CURRENT_USER">твоя собcтвенная карточка</a> информацию для которой мы почерпнули из сервиса агентурных данных</li>
            <li>Так же ты можешь в реальном времени просматривать <a href="/bitrix/admin/services/instagram/?ACTION=GET_MEDIA">все доносы</a> предоставленные твоей агентурной сетью</li>
            <!--<li>Доносы твоей агентурной сети по <a href="/bitrix/admin/services/instagram/?ACTION=GET_TAG">кодовому слову</a> принятому по новому секретному протоколу</li>-->
            <!--<li>Доносы <a href="/bitrix/admin/services/instagram/?ACTION=GET_USER_BY_ID">агентов союзнических</a> разведывательных управлений и шпионских сетей</li>-->
            <li>Сервис <a href="/bitrix/admin/services/instagram/?ACTION=WIDGET">удачно выбранных и достоверных</a> агентурных данных, для последущей ротации на главной странице сайта Главного Разведывательного Управления DSKLAD.RU</li>
            <li>И главное помни, враг не дремлет и постоянно ведет неустанное скрытое наблюдение. Так что будь БДИТЕЛЕН! </li>
        <?endif;?>
    </ol>
</div>

<div class="row">
    <div class="col-md-12">
        <a href="/bitrix/admin/services/instagram/?ACTION=GET_CURRENT_USER" class="btn btn-outline-info btn-lg <?if (!$client->isAuthorised) : ?>disabled<?endif;?> <?if ($client->isCurrentPage == "GET_CURRENT_USER") : ?>active<?endif;?>" role="button" aria-disabled="true"><span style="font-size:36px;"><i class="fab fa-fort-awesome-alt"></i></span><br/> Пользователь</a>
        <a href="/bitrix/admin/services/instagram/?ACTION=GET_MEDIA" class="btn btn-outline-info btn-lg <?if (!$client->isAuthorised) : ?>disabled<?endif;?> <?if ($client->isCurrentPage == "GET_MEDIA") : ?>active<?endif;?>" role="button" aria-disabled="true"><span style="font-size:36px;"><i class="fas fa-camera-retro"></i></span><br/> Собственные медиа</a>
        <!--<a href="/bitrix/admin/services/instagram/?ACTION=GET_TAG" class="btn btn-outline-info btn-lg <?if (!$client->isAuthorised) : ?>disabled<?endif;?> <?if ($client->isCurrentPage == "GET_TAG") : ?>active<?endif;?>" role="button" aria-disabled="true"><span style="font-size:36px;"><i class="fas fa-utensils"></i></span><br/> Картинки по тегу</a>-->
        <!--<a href="/bitrix/admin/services/instagram/?ACTION=GET_LOCATION" class="btn btn-outline-info btn-lg <?if (!$client->isAuthorised) : ?>disabled<?endif;?> <?if ($client->isCurrentPage == "GET_LOCATION") : ?>active<?endif;?>" role="button" aria-disabled="true"><span style="font-size:36px;"><i class="fas fa-map-marked"></i></span><br/> Картинки по гео позиции</a>-->
        <!--<a href="/bitrix/admin/services/instagram/?ACTION=GET_USER_BY_ID" class="btn btn-outline-info btn-lg <?if (!$client->isAuthorised) : ?>disabled<?endif;?> <?if ($client->isCurrentPage == "GET_USER_BY_ID") : ?>active<?endif;?>" role="button" aria-disabled="true"><span style="font-size:36px;"><i class="fas fa-transgender-alt"></i></span><br/> Чужие картинки</a>-->
        <a href="/bitrix/admin/services/instagram/?ACTION=WIDGET" class="btn btn-outline-info btn-lg <?if (!$client->isAuthorised) : ?>disabled<?endif;?> <?if ($client->isCurrentPage == "WIDGET") : ?>active<?endif;?>" role="button" aria-disabled="true"><span style="font-size:36px;"><i class="far fa-flushed"></i></span><br/> Виджет главной</a>
        <a href="/bitrix/admin/services/instagram/?ACTION=SETTINGS" class="btn btn-outline-info btn-lg <?if ($client->isCurrentPage == "SETTINGS") : ?>active<?endif;?>" role="button" aria-disabled="true"><span style="font-size:36px;"><i class="fas fa-skull-crossbones"></i></span><br/> Настройки менеджера</a>
        <a href="/bitrix/admin/services/instagram/?ACTION=LOGOUT" class="btn btn-outline-info btn-lg <?if ($client->isCurrentPage == "LOGOUT") : ?>active<?endif;?>" role="button" aria-disabled="true"><span style="font-size:36px;"><i class="fas fa-hiking"></i></span><br/>Уйти по английски</a>
    </div>
</div>