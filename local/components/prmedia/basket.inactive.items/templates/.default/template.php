<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $templateFile
 * @var string $templateFolder
 * @var CBitrixComponent $component
 * @var string $componentPath
 */

$this->setFrameMode(true);
?>

<div class="inactive-items__popup">
    <div class="title">Этих товаров больше нет в корзине</div>
    <div class="list">
        <?
        $i = 0;
        foreach ($arResult['ITEMS'] as $item) {
            ?>
            <div class="item">
                <a class="image" href="<?= $item['NAME_URL'] ?>" target="_blank" style="background-image: url(<?= $item['IMAGE'] ?>);"></a>
                <div class="description">
                    <div class="name">
                        <a href="<?= $item['NAME_URL'] ?>" target="_blank"><?= $item['NAME'] ?></a>
                    </div>
                    <div class="data">
                        <p class="article"><span>Артикул:</span> <?= $item['ARTICLE'] ?></p>
                        <?
                        if (!empty($item['COLOR'])) {
                            ?>
                            <p class="color"><span>Цвет:</span> <?= $item['COLOR'] ?></p>
                            <?
                        }

                        if (!empty($item['SIZE'])) {
                            ?>
                            <p class="size"><span>Размер:</span> <?= $item['SIZE'] ?> см</p>
                            <?
                        }
                        ?>
                        <p class="count"><?= $item['QUANTITY'] ?> шт.</p>
                    </div>
                </div>
                <div class="count"><?= $item['QUANTITY'] ?> шт.</div>
            </div>
            <?
            if ($i == 2) {
                ?>
                </div>
                <div class="list list-show-more">
                <?
            }

            $i++;
        }
        ?>
    </div>
    <?
    if ($i > 2) {
        ?>
        <div class="show-more show">
            <span class="show">Показать всё</span>
            <span class="hide">Скрыть</span>
            <span class="icon__darr">
                <svg>
                    <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#darr"></use>
                </svg>
            </span>
        </div>
        <?
    }

    if (!empty($arResult['RELATED'])) {
        ?>
        <div class="hr"></div>
        <div class="title-related">Предлагаем присмотреться к похожим товарам</div>
        <div class="related">
            <?
            foreach ($arResult['RELATED'] as $item) {
                ?>
                <a class="image" href="<?= $item['URL'] ?>" target="_blank" style="background-image: url(<?= $item['IMAGE'] ?>);"></a>
                <?
            }
            ?>
        </div>
        <?
    }
    ?>
</div>
