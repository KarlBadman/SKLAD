        </div>

        <script type="text/javascript">
            var dimensionValue = '';
            <?if ($_SERVER['HTTP_HOST'] != 'www.dsklad.ru') {?>
            dimensionValue = 'test_dimension';
            <?} else {?>
            dimensionValue = 'production_dimension';
            <?}?>
            window.addEventListener("load", function(){
                if(window.ga && ga.create) {
                    ga('set', 'dimension8', dimensionValue);
                }
            }, false);
        </script>
        <div class="ds-modal-overlay closed"></div>
        <div class="ds-modal closed">
            <div class="ds-modal__inner"></div>
        </div>
    </body>
</html>