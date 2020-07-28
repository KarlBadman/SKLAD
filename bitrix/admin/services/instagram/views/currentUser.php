<br><br>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive-md text-center">
            <table class="table table-striped table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th class="align-middle">Фотокарточка</th>
                        <th class="align-middle">Имя</th>
                        <th class="align-middle">Позывной</th>
                        <th class="align-middle">Характеристика</th>
                        <th class="align-middle">Явки</th>
                        <th class="align-middle">Агентурные связи</th>
                        <th class="align-middle">Кол-во агентов</th>
                        <th class="align-middle">Компромат</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <?if ($client->currentUserInfo['profile_picture']) : ?>
                                <img class="rounded-circle" width="50" height="50" src="<?=$client->currentUserInfo['profile_picture']?>" alt="" />
                            <?else : ?>
                                <img width="50" height="50" src="./img/nophoto.png" alt="" />
                            <?endif;?>
                        </td>
                        <td class="align-middle"><?=$client->currentUserInfo['full_name']?></td>
                        <td class="align-middle"><?=$client->currentUserInfo['user_name']?></td>
                        <td class="align-middle"><?=$client->currentUserInfo['bio']?></td>
                        <td class="align-middle"><?=$client->currentUserInfo['web']?></td>
                        <td class="align-middle"><?=$client->currentUserInfo['follows']?></td>
                        <td class="align-middle"><?=$client->currentUserInfo['followers']?></td>
                        <td class="align-middle"><?=$client->currentUserInfo['media_count']?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>