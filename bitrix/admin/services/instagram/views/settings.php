<br><br>
<div class="row">
    <div class="col-md-12">
        <form action="/bitrix/admin/services/instagram/">
            <input type="hidden" name="ACTION" value="SETTINGS_SAVE" />
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="clientidfield">Instagram Client ID</label>
                    <input type="text" name="client_id" class="form-control" id="clientidfield" placeholder="Client ID" value="<?=$client->config['client_id']?>">
                </div>
                <div class="form-group col-md-6">
                    <label for="clientsecretfield">Instagram Client Secret</label>
                    <input type="text" name="client_secret" class="form-control" id="clientsecretfield" placeholder="Client secret" value="<?=$client->config['client_secret']?>">
                </div>
                <div class="form-group col-md-6">
                    <label for="clientaccesstoken">Instagram Client Token</label>
                    <input type="text" name="client_access_token" class="form-control" id="clientaccesstoken" placeholder="Client access token" readonly value="<?=$client->config['client_access_token']?>">
                </div>
                <div class="form-group col-md-6">
                    <label for="users_count">Кол - во подписчиков</label>
                    <input type="text" name="users_count" class="form-control" id="users_count" placeholder="Users count" value="<?=$client->config['users_count']?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="redirecturldield">Instagram Redirect URL</label>
                    <input type="text" name="redirect_url" class="form-control" id="redirecturldield" placeholder="Redirect URL" value="<?=$client->config['redirect_url']?>">
                </div>
                <div class="form-group col-md-6">
                    <label for="scopefield">Запрашиваемые права</label>
                    <input type="text" name="scope" class="form-control" id="scopefield" placeholder="Scope field" value="<?=$client->config['scope']?>">
                </div>
                <div class="form-group col-md-6">
                    <label for="iblockidfield">Инфоблок для хранения картинок (ALARM!!! Очень внимательно, не потри чужого!!! Лучше <a href="#" data-toggle="modal" data-target="#iblockcreatemodal">создай свой!!!</a>)</label>
                    <select id="iblockidfield" name="iblock_id" class="form-control">
                        <option>Выберите инфоблок (HL) для хранения</option>
                        <?foreach ($client->config['iblocks'] as $hl) : ?>
                            <option <?if ($hl['CHECKED']) : ?>selected<?endif;?> value="<?=$hl['ID']?>"><?=$hl['NAME']?></option>
                        <?endforeach;?>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="imagescountfield">Кол - во картинок в виджете</label>
                    <input class="form-control" type="number" id="imagescountfield" name="images_count" max="10" min="4" value="<?=$client->config['images_count']?>">
                </div>
            </div>
            <button type="submit" class="btn btn-outline-danger"><span style="font-size:25px;"><i class="fab fa-accessible-icon"></i></span> Сохранить</button><br><br>
            <div class="jumbotron">
                <h1 class="display-4">Внимание товарищь!!!</h1>
                <p class="lead">Центральный комитет партии, на XX съезде народных коммисаров постановил, декрет о сохранении настроек!</p>
                <hr class="my-4">
                <p>Что бы не допустить утечки информации и отказа в обслуживании, ЦК партии, было принято единогласное решение, <b>ПОСЛЕ КАЖДОГО СОХРАНЕНИЯ НАСТРОЕК, ПРОХОДИТЬ ПРОЦЕДУРУ АВТОРИЗАЦИИ НА СЕРВИСЕ ПОВТОРНО,</b> 
                это позволит сократить сроки выполнения пятилетнего плана, и не допустить срыва мероприятий, диверсионными действиями иностранных агентов! <b>Товарищь, БУДЬ БДИТЕЛЕН!!!</b> </p>
            </div>
        </form>
    </div>
</div>
<!-- MODAL -->
<div class="modal fade" id="iblockcreatemodal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="/bitrix/admin/services/instagram/?ACTION=AJAX_CALL&METHOD=IBLOCKADD">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title">Решил создать свой инфоблок?! Огонь! Так держать!</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="iblock_name" class="col-form-label">Тогда уж назови его как нибудь</label>
                        <input type="text" class="form-control" id="iblock_name" data-valid="123" maxlength="20" name="iblock_name" placeholder="Но помни, что у тебя в распоряжении всего английских 20 букв" />
                        <br><div class="alert ajax_text"></div>
                    </div>
                    <div class="alert alert-info" role="alert">И не забудь потом перезагрузить страницу и выбрать в настройках ИБ для хранения (А то мне лень!)</div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Создать</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                </div>
            </form>
        </div>
    </div>
</div>