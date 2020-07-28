<?/*

Установка Demon Collector

*/?>

<script type="text/javascript">
    (function(_,r,e,t,a,i,l){_['retailCRMObject']=a;_[a]=_[a]||function(){(_[a].q=_[a].q||[]).push(arguments)};_[a].l=1*new Date();l=r.getElementsByTagName(e)[0];i=r.createElement(e);i.async=!0;i.src=t;l.parentNode.insertBefore(i,l)})(window,document,'script','https://collector.retailcrm.pro/w.js','_rc');

    _rc('create', 'RC-70177655605-5', {
        <?if($USER->isAuthorized()):?>
        'customerId': '<?=$USER->getId();?>'
        <?endif;?>
    });

    _rc('send', 'pageView');
</script>