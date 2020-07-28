<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult['ITEMS'] */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>
<a href="<? if ($arResult['QUANTITY'] > 0): ?>/order/<? else: ?>javascript:void();<? endif ?>" data-count="<?= $arResult['QUANTITY'] ?>" class="cart active">
    <span class="icon__cart">
        <svg>
            <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#cart"></use>
        </svg>
    </span>
    <span class="label">Корзина</span>
</a>
<? if ($arResult['QUANTITY'] > 0): ?>
    <div class="incart-wrap">
        <div class="incart">
            <h2>Корзина <sup><?= $arResult['QUANTITY'] ?></sup></h2>
            <table>
                <? foreach ($arResult['ITEMS'] as $arItem):?>
                    <tr data-id="<?=$arItem["ID"]?>">
                        <td class="image">
                            <? if(!empty($arItem['IMAGE']['SRC'])): ?>
                                <img src="<?= $arItem['IMAGE']['SRC'] ?>" alt="<?= $arItem['NAME'] ?>">
                            <? else: ?>
                                <img src="<?= SITE_TEMPLATE_PATH ?>/images/no_photo.png" width="66" alt="<?= $arItem['NAME'] ?>">
                            <? endif; ?>
                        </td>
                        <td class="data">
                            <p class="title">
                                <a href="<?= $arItem['URL'] ?>"><?= $arItem['NAME'] ?></a>
                            </p>

                            <p class="modifitation">
                                <span class="label"> Цвет:</span>
                                <span style="background: rgb(<?= $arItem['COLOR']['UF_RGB'] ?>)" class="color"></span>
                                <span><?= $arItem['COLOR']['UF_NAME'] ?></span>
                            </p>
                        </td>
                        <td class="price"><?= $arItem['PRICE'] ?>.–
													<div class="smbas_del">
														<span class="icon__cross">
															<svg>
																<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/bitrix/templates/dsklad/images/sprite.svg#cross"></use>
															</svg>
														</span>
													</div>
												</td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                    </tr>
                <? endforeach ?>
            </table>
            <div class="order"><a href="/basket/" class="button type-blue fill size-41">Оформить заказ</a>
            </div>
        </div>
    </div>
<? endif ?>
