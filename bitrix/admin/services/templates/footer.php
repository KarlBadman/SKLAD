
    </div>
    <div class="col-sm-12 col-lg-3">
        <div class="nav flex-column nav-pills"  aria-orientation="vertical">
            <a class="nav-link <?if(strpos($APPLICATION->GetCurPage(),'/services/set_tags/')):?>active<?endif;?>" href="<?=SERVICES_ADMIN_URL?>set_tags/">Массовое тегирование</a>
            <a class="nav-link <?if(strpos($APPLICATION->GetCurPage(),'/services/set_prices/')):?>active<?endif;?>" href="<?=SERVICES_ADMIN_URL?>set_prices/">Массовое Изменение цен</a>
            <a class="nav-link <?if(strpos($APPLICATION->GetCurPage(),'/services/get_logs_list/')):?>active<?endif;?>" href="<?=SERVICES_ADMIN_URL?>get_logs_list/">Лог данные из БД</a>
            <a class="nav-link <?if(strpos($APPLICATION->GetCurPage(),'/services/get_terminals_by_city/')):?>active<?endif;?>" href="<?=SERVICES_ADMIN_URL?>get_terminals_by_city/">Получить информацию по пунктам DPD города</a>
            <a class="nav-link <?if(strpos($APPLICATION->GetCurPage(),'/services/get_order_by_transaction/')):?>active<?endif;?>" href="<?=SERVICES_ADMIN_URL?>get_order_by_transaction/">Получить номер заказа по транзакции</a>
            <a class="nav-link <?if(strpos($APPLICATION->GetCurPage(),'/services/get_full_images/')):?>active<?endif;?>" href="<?=SERVICES_ADMIN_URL?>get_full_images/">Изображения товаров</a>
            <a class="nav-link <?if(strpos($APPLICATION->GetCurPage(),'/services/feeds_generator/')):?>active<?endif;?>" href="<?=SERVICES_ADMIN_URL?>feeds_generator/">Генерация фидов</a>
            <a class="nav-link <?if(strpos($APPLICATION->GetCurPage(),'/services/get_speed_info/')):?>active<?endif;?>" href="<?=SERVICES_ADMIN_URL?>get_speed_info/">Статистика Скорости</a>
            <a class="nav-link <?if(strpos($APPLICATION->GetCurPage(),'/services/speed_monitor/')):?>active<?endif;?>" href="<?=SERVICES_ADMIN_URL?>speed_monitor/">Монитор показателей скорости PSI</a>
            <a class="nav-link <?if(strpos($APPLICATION->GetCurPage(),'/services/get_zip_images/')):?>active<?endif;?>" href="<?=SERVICES_ADMIN_URL?>get_zip_images/">Архивировать снимки по списку товаров</a>
            <a class="nav-link <?if(strpos($APPLICATION->GetCurPage(),'/services/ga-transactions-delete/')):?>active<?endif;?>" href="<?=SERVICES_ADMIN_URL?>ga-transactions-delete/">Отмена транзакций Google Analitycs</a>
            <a class="nav-link <?if(strpos($APPLICATION->GetCurPage(),'/services/instagram/')):?>active<?endif;?>" href="<?=SERVICES_ADMIN_URL?>instagram/">Интеграция с инстаграм</a>
            <a class="nav-link <?if(strpos($APPLICATION->GetCurPage(),'/services/product_cost_list/')):?>active<?endif;?>" href="<?=SERVICES_ADMIN_URL?>product_cost_list/">Себестоимость товаров</a>
            <a class="nav-link <?if(strpos($APPLICATION->GetCurPage(),'/services/planfix/')):?>active<?endif;?>" href="<?=SERVICES_ADMIN_URL?>planfix/">Интеграция с Planfix</a>
        </div>
    </div>

</div><!--/row-->

<footer>
    <p></p>
</footer>

</div><!--/.container-->


<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
    <script type="text/javascript">
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
        ga('create', 'UA-55823711-1', 'dsklad.ru');
        ga('require', 'ec');
    </script>
    <script src="//code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        });
    </script>
    <script src="<?=SERVICES_ADMIN_URL?>assets/js/bootstrap.js"></script>
    <script src="<?=SERVICES_ADMIN_URL?>assets/js/cropper.min.js"></script>
    <script src="<?=SERVICES_ADMIN_URL?>assets/js/ga-transaction-delete.js"></script>
    <script src="<?=SERVICES_ADMIN_URL?>assets/js/instagram.js"></script>
    <script src="<?=SERVICES_ADMIN_URL?>assets/js/planfix.js"></script>
    <script src="<?=SERVICES_ADMIN_URL?>assets/js/printtis.js"></script>
    <script src="/local/templates/dsklad/js/swop.js"></script>
</body>
</html>
