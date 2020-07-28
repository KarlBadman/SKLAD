//зум деталка
(function () {
    window.swopImage = {
        settings : {
            waiting : false,
            lastPointX : 0,
            lastPointY : 0,
            debug : false,
        },

        reverse : function (s) {
            return s.split("").reverse().join("");
        },

        swop_array : function (arr, filename) {
            var key = filename.replace(/[^\d]+/g, '');
            key = swopImage.reverse(key);
            var new_array = [];
            var array_keys = [];
            var iterrator = 0;
            for(var k=0; k < key.length; k++){
                if($.inArray(key[k], array_keys) == -1){
                    if(arr.length > key[k]){
                        array_keys.push(key[k]);
                        new_array[key[k]] = arr[iterrator];
                        arr[iterrator] = '';
                        iterrator++;
                    }
                }
            }
            arr = arr.filter(function(n) { return n});
            $.each(new_array, function( index, value ) {
                if(typeof(value) == "undefined"){
                    new_array[index] = arr[0];
                    arr.splice(0, 1);
                }
            });
            return new_array.concat(arr);
        },

        generateCanvas : function () {
            if (swopImage.settings.debug)
                console.info('generateCanvas fired');
            swopped = this;
            swopImage.settings.waiting = false;
            var division_param = 4;
            var fullWidth = swopped.width;
            var fullHeight = swopped.height;
            var piece_w = fullHeight / division_param;
            var piece_h = fullWidth / division_param;
            var list = [];
            for (var i = 0; i <= division_param * division_param; i++) {
                list.push(i);
            }
            var filename  = swopped.src.match("\/uf\/([^\/]+)\/");
            filename = filename[1] + swopped.src.replace( /.*\/upload\/.*?([^\/]+)\..+?$/, '$1' );
            var arraymap = swopImage.swop_array(list, filename);

            var c = document.createDocumentFragment();
            for (var x = 0; x < division_param; x++) {
                for (var y = 0; y < division_param; y++) {
                    some = arraymap[(y * division_param)+ x];
                    var pos = Math.floor(some / division_param);
                    var position_x = -piece_w * pos;
                    var position_y = -piece_h * (some - pos*division_param);
                    var clear = 'none';
                    if (y == 0) {
                        clear = 'both';
                    }
                    var e = document.createElement("div");
                    e.style.background='url(' + swopped.src + ') no-repeat ' + position_x + 'px ' + position_y + 'px';
                    // e.style.backgroundSize = fullWidth + 'px';
                    e.style.height = piece_h + 'px';
                    e.style.width = piece_w + 'px';
                    e.style.cssFloat='left';
                    e.style.clear=clear;
                    c.appendChild(e);
                }
            }
            if(swopped.container[0].appendChild(c)){
                $('.wrap_slider_detail.visible .draggable .slick-active .image-mousetrap').trigger('mousemove')
                return true;
            }
            return false;
        },

        mouseEnterEvent : function (self) {
            if (swopImage.settings.debug)
                console.info('mouseEnterEvent fired');
            var container = self.find('.container');
            var original = self.find('.original');
            if(container.children().length == 0 && !swopImage.settings.waiting){
                var swopped = new Image();
                swopped.container = container;
                swopped.originaldata = original;
                swopImage.settings.waiting = true;
                swopped.onload = swopImage.generateCanvas;
                swopped.src = original.data('zoomsrc');
            }
        },

        mouseMoveEvent : function (self, e) {
            if (swopImage.settings.debug)
                console.info('mouseMoveEvent fired');
            var parent = self.parent();
            var container = parent.find('.container');
            var original = parent.find('.original');
            var swopped = new Image();
            swopped.src = original.data('zoomsrc');

            var thumbnailWidth = original.width();
            var thumbnailHeight = original.height();
            self.css({'max-height': thumbnailHeight + 'px', 'max-width': thumbnailWidth + 'px'});
            var fullWidth = swopped.width;
            var fullHeight = swopped.height;
            swopImage.settings.lastPointX = e.pageX || swopImage.settings.lastPointX;
            swopImage.settings.lastPointY = e.pageY || swopImage.settings.lastPointY;
            var mouseX = swopImage.settings.lastPointX - self.offset().left;
            var mouseY = swopImage.settings.lastPointY - self.offset().top;
            var posX = (Math.round((mouseX/thumbnailWidth)*750)/750) * (fullWidth-thumbnailWidth);
            var posY = thumbnailHeight + (Math.round((mouseY/thumbnailHeight)*750)/750) * (fullHeight-thumbnailHeight);
            container.css({
                'width': fullWidth + 'px',
                'height': fullHeight+ 'px',
                'left': '-' + posX + 'px',
                'top': '-' + posY + 'px'
            });
        },


        // Init method
        init : function () {
            if (typeof(isMobile) == 'object' && isMobile.any) {
                if(swopImage.settings.debug)
                    console.info('Mobil');

                $('.item__page .photos .images').on('mouseup', '.item .image-mousetrap', function(e) {
                    purepopup.ajaxToModal(false, purepopup.modalFilling, 550, $(this), true);
                });
            } else {
                if(swopImage.settings.debug)
                    console.info('Desc');
                $('.item__page .photos .images').on('mouseenter', '.slick-active .cloud_zoom_item', function() {
                    swopImage.mouseEnterEvent($(this));
                });
                $('.item__page .photos .images').on('mousemove', '.item .image-mousetrap', function(e) {
                    flag = 1;
                    swopImage.mouseMoveEvent($(this), e);
                });
                $('.item__page .photos .images').on('touchmove', '.item .image-mousetrap', function(e) {
                    swopImage.mouseMoveEvent($(this), e);
                })
            }

            if ($('.service_image').length > 0) {
                swopImage.mouseEnterEvent($('.service_image'));
                $('.service_image .original').hide();
            }
        }
    }

    // Call init metod
    $(document).ready(function () {
        swopImage.init();
    });
})();