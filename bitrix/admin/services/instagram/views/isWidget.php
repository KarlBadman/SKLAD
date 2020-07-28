<br><br>
<div class="row">
    <?if (!empty($client->widgetMedia) && is_array($client->widgetMedia)) : ?>
        <?foreach ($client->widgetMedia as $widgetMedia) : ?>
            <div class="col-md-4">
                <figure class="figure" data-source="<?=$client->prepare2json($widgetMedia);?>">
                    <?if (!empty($widgetMedia['UF_IMG']['SRC'])) : ?>
                        <a href="#" class="widgetmediaddaction" data-toggle="modal" data-target="#mediawidgetupdatemodal" style="display:inline-block;with:100%;height100%;">
                            <img src="<?=$widgetMedia['UF_IMG']['SRC']?>" width="454" height="460" class="figure-img rounded" />
                        </a>
                    <?else : ?>  
                        <img src="./img/imagenotfound.png" alt="<?=$widgetMedia['UF_POPUP_TEXT']?>" width="500" height="330" class="figure-img img-fluid rounded">
                    <?endif;?>
                    <figcaption class="figure-caption text-right"><?=$widgetMedia['UF_PICTURE_ID']?></figcaption>
                </figure>
            </div>
        <?endforeach?>
    <?endif;?>
</div>
<!-- MODAL -->
<div class="modal fade" id="mediawidgetupdatemodal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="/bitrix/admin/services/instagram/?ACTION=AJAX_CALL&METHOD=MEDIAUPDATE">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title">Рекдактирование всплывашки в виджете</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="alert ajax_text"></div>
                    <div class="row">
                        <div class="col-md-9">
                            <div class="form-group">
                                <label for="uf_img_id" class="col-form-label">ID картинки в инстаграме</label>
                                <input type="text" class="form-control" id="uf_img_id" name="img-id" placeholder="Цифробуквы" readonly value="" />
                            </div>
                            <div class="form-group">
                                <label for="uf_popup_left_position" class="col-form-label">Смещение всплывахи слева</label>
                                <input type="number" class="form-control" id="uf_popup_left_position" name="popup-left-position" readonly placeholder="Циферка" value="" />
                            </div>
                            <div class="form-group">
                                <label for="uf_popup_top_position" class="col-form-label">Смещение всплывахи справа</label>
                                <input type="number" class="form-control" id="uf_popup_top_position" name="popup-top-position" readonly placeholder="Циферка" value="" />
                            </div>
                            <div class="form-group">
                                <label for="uf_tag" class="col-form-label">Тег</label>
                                <input type="text" class="form-control" id="uf_tag" name="tag" placeholder="Буквы" value="" />
                            </div>
                            <div class="form-group">
                                <label for="uf_tag_link" class="col-form-label">Ссылка тега</label>
                                <input type="text" class="form-control" id="uf_tag_link" name="tag-link" placeholder="Буквы" value="" />
                            </div>
                            <div class="form-group">
                                <label for="uf_popup_text" class="col-form-label">Текст всплывашки</label>
                                <input type="text" class="form-control" id="uf_popup_text" name="popup-text" placeholder="Буквы" value="" />
                            </div>
                            <div class="form-group">
                                <label for="uf_popup_link" class="col-form-label">Ссылка всплывашки</label>
                                <input type="text" class="form-control" id="uf_popup_link" name="popup-link" placeholder="Буквы" value="" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Изменить</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                </div>
                <input type="hidden" name="id" value="" />
            </form>
        </div>
    </div>
</div>
