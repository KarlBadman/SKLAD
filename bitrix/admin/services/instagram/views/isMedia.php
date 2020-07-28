<br><br>
<div class="row">
    <?if (!empty($client->currentMedia)) : ?>
        <?foreach ($client->currentMedia as $media) : ?>
            <div class="col-md-2 mb-5">
                <div class="card <?if ($media['ACTIVE'] != "Y") : ?>bg-light<?endif;?>" data-source="<?=$client->prepare2json($media);?>">
                    <?if (!empty($media['THUMB']['SRC'])) : ?>
                        <a href="#" 
                            class="<?if ($media['ACTIVE'] != "Y") : ?>btn btn-link disabled<?endif;?> mediaddaction"
                            data-toggle="modal" data-target="#mediaaddmodal">
                            <img class="card-img-top" width="<?=$media['THUMB']['WIDTH']?>" height="<?=$media['THUMB']['HEIGHT']?>" src="<?=$media['THUMB']['SRC']?>" alt="<?=$media['CAPTION']?>">
                        </a>
                    <?else : ?>
                        <img class="card-img-top" width="150" height="150" src="./img/imagenotfound.png" alt="<?=$media['CAPTION']?>">
                    <?endif;?>
                    <div class="card-body">
                        <h5 class="card-title"><a href="#" class="btn btn-link btn-outline-light mediaddaction <?if ($media['ACTIVE'] != "Y") : ?>disabled<?endif;?>" data-toggle="modal" data-target="#mediaaddmodal"><?=$media['CAPTION']?></a></h5>
                        <p class="card-text">
                            <span><b>Создан:</b> <i><?=$media['CREATED']?></i></span><br>
                            <span><b>Тип:</b> <i><?=$media['TYPE']?></i></span><br>
                            <span><b>Ссылка:</b> <a target="_blank" href="<?=$media['LINK']?>"><i class="far fa-hand-point-right"></i></a></span><br>
                            <span><b>Донес:</b> <i><?=$media['USER']?></i></span><br>
                            <span><b>Фильтры:</b> <i><?=$media['FILTER']?></i></span><br>
                            <span><b>Лайки:</b> <i><?=$media['LIKES']?></i></span><br>
                            <?if (!empty($media['DISPLAY_TAGS'])) : ?>
                                <span>
                                    <b>Тэги:</b> 
                                    <a href="#" data-toggle="popover" data-trigger="focus" title="Теги доноса" data-content="<?=implode(' ', $media['TAGS'])?>">
                                        <i><?=$media['DISPLAY_TAGS']?></i>
                                    </a>
                                </span>
                            <?endif;?>
                        </p>
                        <?if ($media['SELECTED'] == 'Y') : ?>
                            <a href="#" data-toggle="modal" data-target="#mediaaddmodal" class="btn btn-success">Убрать из виджета</a>
                        <?else : ?>
                            <a href="#" data-toggle="modal" data-target="#mediaaddmodal" class="btn btn-primary mediaddaction <?if ($media['ACTIVE'] != "Y") : ?>disabled<?endif;?>">Выбрать для виджета</a>
                        <?endif;?>
                    </div>
                </div>
            </div>
        <?endforeach;?>
    <?endif;?>
</div>
<div class="row">
    <div class="col-md-12">
        <?if (!is_bool($client->prevPageID)) : ?>
            <a href="/bitrix/admin/services/instagram/?ACTION=GET_MEDIA&max_id=<?=$client->prevPageID?>">&larr; &larr; &larr; Назад</a> | 
        <?elseif ($client->prevPageID !== false) : ?>
            <a href="/bitrix/admin/services/instagram/?ACTION=GET_MEDIA">&larr; &larr; &larr; Назад</a> | 
        <?endif;?>
        <?if ($client->nextPageID) : ?>
            <a href="/bitrix/admin/services/instagram/?ACTION=GET_MEDIA&max_id=<?=$client->nextPageID?>">Далее &rarr; &rarr; &rarr;</a>
        <?endif;?>
    </div>
</div>
<!-- MODAL -->
<div class="modal fade" id="mediaaddmodal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="/bitrix/admin/services/instagram/?ACTION=AJAX_CALL&METHOD=MEDIAADD">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title">Добавление картинки в виджет</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="alert ajax_text"></div>
                    <div class="row">
                        <div class="col-md-9">
                            <div class="col-md-12">
                                <img class="rounded mx-auto d-block img-fluid media-crop" width="800" src="./img/imagenotfound.png" alt="Image not set" title="Image not set for croping" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <p>Соотношение сторон</p>
                            <div class="btn-group mb-3" role="group" aria-label="">
                                <button type="button" class="btn btn-secondary" data-event="setAspectRation" value="1.7777777777777777">16:9</button>
                                <button type="button" class="btn btn-secondary" data-event="setAspectRation" value="1.3333333333333333">4:3</button>
                                <button type="button" class="btn btn-secondary" data-event="setAspectRation" value="1">1:1</button>
                            </div>
                            <p>Сдвинуть</p>
                            <div class="btn-group mb-3" role="group" aria-label="">
                                <button type="button" class="btn btn-secondary" data-event="moveLeft" value="15"><i class="fa fa-arrow-left"></i></button>
                                <button type="button" class="btn btn-secondary" data-event="moveRight" value="15"><i class="fa fa-arrow-right"></i></button>
                                <button type="button" class="btn btn-secondary" data-event="moveUp" value="15"><i class="fa fa-arrow-up"></i></button>
                                <button type="button" class="btn btn-secondary" data-event="moveDown" value="15"><i class="fa fa-arrow-down"></i></button>
                            </div>
                            <p>Повернуть\Увеличить</p>
                            <div class="btn-group mb-3" role="group" aria-label="">
                                <button type="button" class="btn btn-secondary" data-event="rotateUndo" value="15"><i class="fas fa-undo-alt"></i></i></button>
                                <button type="button" class="btn btn-secondary" data-event="rotateRedo" value="15"><i class="fas fa-redo-alt"></i></button>
                                <button type="button" class="btn btn-secondary" data-event="zoomIn" value=".1"><i class="fa fa-search-plus"></i></button>
                                <button type="button" class="btn btn-secondary" data-event="zoomOut" value=".1"><i class="fa fa-search-minus"></i></button>
                            </div>
                            <p>Отразить\Кропнуть</p>
                            <div class="btn-group mb-3" role="group" aria-label="">
                                <button type="button" class="btn btn-secondary" data-event="scaleX"><i class="fa fa-arrows-alt-h"></i></button>
                                <button type="button" class="btn btn-secondary" data-event="scaleY"><i class="fa fa-arrows-alt-v"></i></button>
                                <button type="button" class="btn btn-secondary" data-event="crop">Кроп <i class="fa fa-check"></i></button>
                            </div>
                            <p>Сбросить\К исходнику</p>
                            <div class="btn-group mb-3" role="group" aria-label="">
                                <button type="button" class="btn btn-secondary" data-event="reset"><i class="fas fa-toilet"></i></button>
                                <!--<button type="button" class="btn btn-secondary" data-event="return">К исходнику <i class="fas fa-cat"></i></button>-->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Добавить</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                </div>
                <input type="hidden" name="data-source" value="" />
                <input type="hidden" name="data-img" value="" />
            </form>
        </div>
    </div>
</div>