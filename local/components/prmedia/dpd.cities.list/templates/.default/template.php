<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$this->setFrameMode(true);
?>

<div class="selector">
    <ul class="countries">
        <?
        $i = 0;
        foreach ($arResult['ITEMS'] as $countryName => $countryData) {
            ?>
            <li<?= (strtolower($countryName) == 'россия') ? ' class="active"' : '' ?> data-country="<?= $i ?>">
                <a>
                    <?= $countryName ?>
                    <span class="icon__rarr3">
                    <svg>
                        <use xlink:href="<?= SITE_TEMPLATE_PATH ?>/images/sprite.svg#rarr3"></use>
                    </svg>
                </span>
                </a>
            </li>
            <?
            $i++;
        }
        ?>
    </ul>
    <div class="cities">
        <?
        $i = 0;
        foreach ($arResult['ITEMS'] as $countryName => $countryData) {
            ?>
            <div class="cities-tab<?= (strtolower($countryName) == 'россия') ? ' active' : '' ?>" data-country="<?= $i ?>">
                <?
                if (!empty($countryData['FAVORITES'])) {
                    ?>
                    <dl>
                        <?
                        foreach ($countryData['FAVORITES'] as $favorite) {
                            ?>
                            <dd><a data-city="<?= $favorite['CODE'] ?>"><?= $favorite['NAME'] ?></a></dd>
                            <?
                        }
                        ?>
                    </dl>
                    <?
                }

                foreach ($countryData['CITIES'] as $letter => $citiesByLetter) {
                    ?>
                    <dl>
                        <dt><?= $letter ?></dt>
                        <?
                        foreach ($citiesByLetter as $cityData) {
                            ?>
                            <dd><a data-city="<?= $cityData['CODE'] ?>"><?= $cityData['NAME'] ?></a></dd>
                            <?
                        }
                        ?>
                    </dl>
                    <?
                }
                ?>
            </div>
            <?
            $i++;
        }
        ?>
    </div>
</div>

<script>
    $('.region__popup .selector .countries a').click(function() {
        var countryNode = $(this).parent();
        var countryId = countryNode.data('country');
        $('.region__popup .selector .cities-tab.active').removeClass('active');
        $('.region__popup .selector .countries li.active').removeClass('active');
        $('.region__popup .selector .cities-tab[data-country="'+ countryId +'"]').addClass('active');
        countryNode.addClass('active');
        $('.region__popup .selector .cities').stop().animate({scrollTop: 0}, 500);

    });

    $('.region__popup .selector .cities a').click(function() {
        $('.region__popup input[name="city_name"]').val($(this).text());
        $('.region__popup input[name="city_id"]').val($(this).data('city'));
        $('#autocomplete_head').focusout();
    });
</script>