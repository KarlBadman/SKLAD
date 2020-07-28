// GA transaction delete
(function () {

    var gaObj = {

        deleteTransaction : function () {
            var self = this;

            $('body').on('submit', '[data-form=\"deleteTransactions\"]', function(e) {
                var transaction = $(this).find('[name=\"transaction-id\"]').val();

                if (transaction.length == 0 || typeof(transaction) === "undefined") {

                    if ($('[data-alert=\"fail\"]').hasClass('d-none'))
                        $('[data-alert=\"fail\"]').toggleClass('d-none');
                    if (!$('[data-alert=\"success\"]').hasClass('d-none'))
                        $('[data-alert=\"success\"]').toggleClass('d-none');

                } else {

                    ga('ec:setAction', 'refund', {'id': transaction});
                    ga('send', 'event', 'Enhanced Ecommerce', 'Refund');

                    $('[data-alert=\"success\"] b').text('c ID ' + transaction);
                    $(this).find('[type=\"text\"]').val('');

                    if ($('[data-alert=\"success\"]').hasClass('d-none'))
                        $('[data-alert=\"success\"]').toggleClass('d-none');
                    if (!$('[data-alert=\"fail\"]').hasClass('d-none'))
                        $('[data-alert=\"fail\"]').toggleClass('d-none');
                }

                return false;
            });
        },

        init : function () {
            this.deleteTransaction();
        }

    }

    $(document).ready(function () {
        gaObj.init();
    });

})();
