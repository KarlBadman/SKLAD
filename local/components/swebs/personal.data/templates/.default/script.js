$(document).ready(function () {
    var oldEmail = $(window.analyticSystem.dataSets.dataSelectors.personalPage.email).val();

    $(document).on('click','[data-element="save_personal"]',function () {
       if(oldEmail ==='' && $(window.analyticSystem.dataSets.dataSelectors.personalPage.email).val() !='') {
           $('body').trigger('savePersonalPage');
       }else{
           if ($(window.analyticSystem.dataSets.dataSelectors.personalPage.email).val() != oldEmail && $(window.analyticSystem.dataSets.dataSelectors.personalPage.subscrible).attr("checked") == 'checked') $('body').trigger('savePersonalPage');
       }
    });
});