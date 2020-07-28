<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Page\Asset;

$this->setFrameMode(true);

if (!empty($arResult['ORDERS'])) {
    ?>
    <table class="hidden-s">
        <tr>
            <th>Номер заказа</th>
            <th>Дата заказа</th>
            <th>Информация о доставке</th>
            <th>Общая стоимость</th>
            <th>Статус</th>
        </tr>
        <?
        foreach ($arResult['ORDERS'] as $intKey => $arItem) {
            Asset::getInstance()->addString('<style>#status_'.$intKey.':before {background-color: '.$arItem['STATUS']['COLOR'].';}</style>');
            ?>
            <tr>
                <td>
                    <b><a href="/personal/order/<?= $arItem['ID'] ?>/">№ <?= $arItem['ID'] ?></a></b>
                </td>
                <td><?= $arItem['DATE_LONG'] ?></td>
                <td>
                    <p><?= stristr($arItem['DELIVERY'],'|',true)?></p>
                    <? if (!empty($arItem['TRACKING_NUMBER'])) { ?>
                        <p>Номер отслеживания ТК<?=$arItem['DELIVERY_COMPANY'];?>:</p>
                        <p class="tracking">
                            <a href="<?= $arItem['TRACKING_LINK'] ?>" target="_blank" rel="noopener"><?= $arItem['TRACKING_NUMBER'] ?></a>
                            <span class="icon__help">
                                <svg><use xlink:href="<?= SITE_TEMPLATE_PATH?>/images/sprite.svg#help"></use></svg>
                            </span>
                        </p>
                        <?
                    }
                    ?>
                </td>
                <td>
                    <strong class="ds-price"><?= $arItem['PRICE'] ?></strong>
                </td>
                <td>
                    <span class="status" id="status_<?= $intKey ?>" style="color: <?= $arItem['STATUS']['COLOR'] ?>;">
                        <i></i> <?= $arItem['STATUS']['NAME'] ?>
                    </span>
                </td>
            </tr>
            <?
        }
        ?>
    </table>

    <table class="hidden-gt-s">
        <tr>
            <th>Заказ</th>
            <th>Доставка</th>
        </tr>
        <?
        foreach ($arResult['ORDERS'] as $intKey => $arItem) {
            ?>
            <tr>
                <td>
                    <div class="info">
                        <b><a href="/personal/order/<?= $arItem['ID'] ?>/">№ <?= $arItem['ID'] ?></a></b>
                        от <?= $arItem['DATE_SHORT'] ?>
                    </div>
                    <ul>
                        <li>Стоимость: <span><b class="ds-price"><?= $arItem['PRICE'] ?></b></span></li>
                    </ul>
                    <span class="status" id="status_<?= $intKey ?>" style="color: <?= $arItem['STATUS']['COLOR'] ?>;">
                       <i></i> <?= $arItem['STATUS']['NAME'] ?>
                    </span>
                </td>
                <td>
                    <p><?= $arItem['DELIVERY'] ?></p>
                    <? if (!empty($arItem['TRACKING_NUMBER'])) { ?>
                        <p>Номер отслеживания ТК&nbsp;“<?= $arItem['DELIVERY_COMPANY'];?>”:</p>
                        <p class="tracking">
                            <a href="<?= $arItem['TRACKING_LINK'] ?>" target="_blank" rel="noopener"><?= $arItem['TRACKING_NUMBER'] ?></a>
                        </p>
                        <?
                    }
                    ?>
                </td>
            </tr>
            <?
        }
        ?>
    </table>
    <?
} else {
    ?>
    <div class="wrap_history_empty">
        <div class="inner_history_empty">
            <img src="<?= SITE_TEMPLATE_PATH?>/images/img_history_empty.png" alt=""/>
            <div class="desc_history_empty">
                <p class="tit_history_empty">История заказов отсутствует.</p>
                <p>К сожалению, вы еще не успели совершить ни одной покупки в нашем магазине.</p>
                <p>Не беда, вы всегда сможете подобрать товар в нашем <a href="/catalog/">каталоге</a>.</p>
            </div>
        </div>
    </div>
    <?
}
?>