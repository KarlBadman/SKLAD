<style>
    .topline-newsite{
        display: none;
        height: 42px;
        padding-top: 8px;
        box-sizing: border-box;
        position: relative;
    }
    .topline-newsite:after{
        content: "";
        display: block;
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 1px;
        background-color: #d9d9d9;
    }

    .topline-newsite[aria-expanded="false"] {
        display: none;
    }

    .topline-newsite[aria-expanded="true"] {
        display: block;
    }

    .topline-newsite .content{
        max-width: 1400px;
        margin: auto;
        display: block;
    }

    .topline-newsite .message{
        display: inline-block;
        color: #1769ff;
        font-size: 16px;

    }
    .topline-newsite .message .back-to-old{
        color: #1769ff;
    }

    .topline-newsite .message .tell-us-about{
        text-decoration: none;
        border-bottom: 1px dotted #1769ff;
        color: #1769ff;
        position: relative;
        cursor: pointer;
    }

    .topline-newsite .close{
        display: block;
        position: absolute;
        top: 10px;
        right: 10px;
        width: 9px;
        height: 9px;
    }
    .topline-newsite .icon__close {
        display: inline-block;
        margin-top: 6px;
        width: 13px;
        height: 13px;
        float: right;
        position: relative;
        margin-right: 18px;
    }


    .topline-newsite .icon__close svg{
        fill:#1769ff;
    }
    @media only screen and (min-width: 320px) {
        .topline-newsite .content {
            width: 94.5545%;
        }
        .topline-newsite{
            height: inherit;
						padding-bottom:10px;
        }
        .topline-newsite .content .message{
            margin-right: 16px;
        }

        .topline-newsite .icon__close{
            margin-right: 1px;
        }
    }

    @media only screen and (min-width: 420px) {
        .topline-newsite:after{
            display: block;
        }
        .topline-newsite .content {
            display: block;
        }

        .topline-newsite{
            height: 65px;
        }
        .topline-newsite .content {
            width: 94.5545%;
        }

        .topline-newsite .content .message{
            margin-right: 16px;
        }
    }

    @media only screen and (min-width: 830px) {
        .topline-newsite{
            height: 42px;
        }
        .topline-newsite .content {
            width: 94.5545%;
        }
    }
</style>
<div class="topline-newsite" aria-expanded="false">
	<div class="content">
		<div class="message">
			<span style="color: #111111;">Если возникли проблемы с работой сайта&nbsp;вы можете </span><span style="color: #111111;"><a href="http://old.dsklad.ru">вернуться на старый сайт</a></span><span style="color: #111111;"> или </span><a href="/contacts.php"><span style="color: #111111;">написать нам об этом</span></a><span style="color: #111111;">.</span>
		</div>
		 <a class="close" >
            <span class="icon__close">
                
            </span>
        </a>
	</div>
</div>
<script>
    $( document ).ready(function(){
        $('.topline-newsite').each(function () {

            var topline = $(this),
                cookieName = 'front_topline-newsiteExpanded',
                expanded = Cookies.get(cookieName);

            if (typeof expanded !== 'undefined') {
                topline.attr('aria-expanded', expanded);
            }else{
                topline.attr('aria-expanded', 'true');
            }

            $(document).on('click', '.topline-newsite .close', function (e) {

                if (e.which !== 1) {
                    return false;
                }

                e.preventDefault();

                topline.attr('aria-expanded', function (i, val) {

                    expanded = !$.parseJSON(val);

                    Cookies.set(cookieName, expanded, {
                        expires: 0.01,
                        path: '/'
                    });

                    return expanded;

                });

            });

        });
    });

</script>