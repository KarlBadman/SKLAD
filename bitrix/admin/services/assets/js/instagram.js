// Instagram js file
(insta = {
    
    settings : {
        debug : true,
        defaultPaths : {
            cropNoImagePath : '/bitrix/admin/services/instagram/img/imagenotfound.png'
        },
        cropperOptions : {
            viewMode : 0,
            dragMode : "move",
            // aspectRatio : "",
            // data : "",
            preview : "",
            responsive : true,
            restore : true,
            checkCrossOrigin : true,
            checkOrientation : true,
            modal : true,
            guides : true,
            center : true,
            highlight : true,
            background : true,
            autoCrop : true,
            autoCropArea : "", 
            movable : true,
            rotatable : true,
            scalable : true, 
            zoomable : true,
            zoomOnTouch : true,
            zoomOnWheel : true,
            cropBoxMovable : true,
            cropBoxResizable : true,
            toggleDragModeOnDblclick : true,
            minCanvasWidth : 0,
            minCanvasHeight : 0,
            minCropBoxWidth : 0,
            minCropBoxHeight : 0,
            minContainerWidth : 200,
            minContainerHeight : 100,
            // events
            ready : function () {},
            cropstart : function () {},
            cropmove : function () {},
            cropend : function () {},
            crop : function () {},
            zoom : function () {}
        }, 
        croppedDefaults : {
            moveLeftSize : -10,
            moveRightSize : 10,
            moveUpSize : -10,
            moveDownSize : 10,
            rotateUndoRate : -90,
            rotateRedoRate : 90,
            zoomInSize : .1,
            zoomOutSize : -.1,
            scaleXRate : 1,
            scaleYRate : 1,
        },
        widgetPopUpPositions : {
            left : 50,
            top : 70,
        },
        emptyString : "",
    },
    
    selectors : {
        mediaAjaxResultText : ".alert.ajax_text",
        mediaView : {
            mediaAddModal : "#mediaaddmodal",
            mediaCropImg : ".media-crop",
            mediaCard : ".card",
            mediaAddModalButtons : {
                aspectRatio : "[data-event=\"setAspectRation\"]",
                moveRight : "[data-event=\"moveRight\"]",
                moveLeft : "[data-event=\"moveLeft\"]",
                moveUp : "[data-event=\"moveUp\"]",
                moveDown : "[data-event=\"moveDown\"]",
                rotateUndo : "[data-event=\"rotateUndo\"]",
                rotateRedo : "[data-event=\"rotateRedo\"]",
                zoomIn : "[data-event=\"zoomIn\"]",
                zoomOut : "[data-event=\"zoomOut\"]",
                scaleX : "[data-event=\"scaleX\"]",
                scaleY : "[data-event=\"scaleY\"]",
                crop : "[data-event=\"crop\"]",
                reset : "[data-event=\"reset\"]",
            },
            mediaAddModalHiddenDataSource : "[name=\"data-source\"]",
            mediaAddModalHiddenDataImg : "[name=\"data-img\"]",
        },
        widgetView : {
            widgetUpdateModal : "#mediawidgetupdatemodal",
            widgetMediaAction : ".widgetmediaddaction",
            widgetCard : ".figure",
            widgetFormFields : {
                id : "[name=\"id\"]",
                instaID : "[name=\"img-id\"]",
                popupLeftPos : "[name=\"popup-left-position\"]",
                popupTopPos : "[name=\"popup-top-position\"]",
                tag : "[name=\"tag\"]",
                tagLink : "[name=\"tag-link\"]",
                popupText : "[name=\"popup-text\"]",
                popupLink : "[name=\"popup-link\"]"
            }
            
        }
    },
    
    _ajaxSuccessHandler : function (r, fns, fnf) {
        var _this = this;
        
        if (r.status) {
            
            _this.__debug('info', 'Ajax request is success executed, now if you return post handler function, it will be start');
            if (typeof fns === "function") fns();
            
        } else {
            _this.__debug('info', r.errorText);
            if (typeof fnf === "function") fnf();
        }
    },
    
    _ajaxFailHandler : function (r) {
        var _this = this;
        
        if (r.errorText.length > 0)
            _this.__debug('error', r.errorText);
    },
    
    __debug : function (event, message) {
        var _this = this;
        
        if (!_this.settings.debug) return;
        
        if (event == 'info') {
            console.info (message);
        } else if (event == 'warn') {
            console.warn (message);
        } else if (event == 'error') {
            console.error (message);
        } else {
            console.log (message);
        }
        
    },
    
    modalEventListners : function () {
        var _this = this, cropImage;
        
        // On modal media add shown
        $(_this.selectors.mediaView.mediaAddModal).on('shown.bs.modal', function (e) {
            var mediaSource = $(e.relatedTarget).parents(_this.selectors.mediaView.mediaCard).data('source');
            
            if (mediaSource.ORIGIN_SRC.SRC.length > 0) {
                $(_this.selectors.mediaView.mediaAddModalHiddenDataSource).val(JSON.stringify(mediaSource));
                $(_this.selectors.mediaView.mediaCropImg).prop('src', mediaSource.ORIGIN_SRC.SRC);
                $(_this.selectors.mediaView.mediaCropImg).cropper(_this.settings.cropperOptions);
            } else 
                _this.__debug('error', "Mediasource data origin src is trouble");
            
        });
        
        // On modal media add hide
        $(_this.selectors.mediaView.mediaAddModal).on('hidden.bs.modal', function (e) {
            
            $(_this.selectors.mediaView.mediaAddModalHiddenDataSource).val('');
            $(_this.selectors.mediaView.mediaAddModalHiddenDataImg).val('');
            
            $(_this.selectors.mediaView.mediaCropImg).prop('src', _this.settings.defaultPaths.cropNoImagePath);
            $(_this.selectors.mediaView.mediaCropImg).cropper("destroy");
        });
        
        // On modal widget update shown
        $(_this.selectors.widgetView.widgetUpdateModal).on('shown.bs.modal', function (e) {
            var widgetCource = $(e.relatedTarget).parents(_this.selectors.widgetView.widgetCard).data('source');
            
            $(_this.selectors.widgetView.widgetFormFields.id).val(widgetCource.ID ? widgetCource.ID : null);
            $(_this.selectors.widgetView.widgetFormFields.instaID).val(widgetCource.UF_IMG_ID ? widgetCource.UF_IMG_ID : null);
            $(_this.selectors.widgetView.widgetFormFields.tag).val(widgetCource.UF_TAG ? widgetCource.UF_TAG : null);
            $(_this.selectors.widgetView.widgetFormFields.tagLink).val(widgetCource.UF_TAG_LINK ? widgetCource.UF_TAG_LINK : null);
            $(_this.selectors.widgetView.widgetFormFields.popupText).val(widgetCource.UF_POPUP_TEXT ? widgetCource.UF_POPUP_TEXT : null);
            $(_this.selectors.widgetView.widgetFormFields.popupLink).val(widgetCource.UF_POPUP_LINK ? widgetCource.UF_POPUP_LINK : null);
            
        });
        
        // On modal widget update hide
        $(_this.selectors.widgetView.widgetUpdateModal).on('hidden.bs.modal', function (e) {
            // TODO erase form method on hide modal
            // $(_this.selectors.widgetView.widgetUpdateModal + " form").reset();
        });
    },
    
    widgetEventListners : function () {
        var _this = this;
        
        // On widget click by image
        $(_this.selectors.widgetView.widgetMediaAction).on('click', function (e) {
            var pos, elem_left, elem_top, Xinner, Yinner;

            pos = $(this).offset(); elem_left = pos.left;
            elem_top = pos.top; Xinner = e.pageX - elem_left;
            Yinner = e.pageY - elem_top;
            
            $(_this.selectors.widgetView.widgetFormFields.popupLeftPos).val(Xinner ? Xinner : _this.settings.widgetPopUpPositions.left);
            $(_this.selectors.widgetView.widgetFormFields.popupTopPos).val(Yinner ? Yinner : _this.settings.widgetPopUpPositions.top);
            
        });
        
    },
    
    cropperEventListners : function () {
        var _this = this;
        
        // On media add cropped image aspectRatio event
        $(_this.selectors.mediaView.mediaAddModalButtons.aspectRatio).on('click', function () {
            var aspectRationValue = $(this).val();
            $(_this.selectors.mediaView.mediaCropImg).cropper("setAspectRatio", aspectRationValue);
        });
        
        // On media add cropped image moveLeft event
        $(_this.selectors.mediaView.mediaAddModalButtons.moveLeft).on('click', function () {
            $(_this.selectors.mediaView.mediaCropImg).cropper("move", _this.settings.croppedDefaults.moveLeftSize, 0);
        });

        // On media add cropped image moveRight event
        $(_this.selectors.mediaView.mediaAddModalButtons.moveRight).on('click', function () {
            $(_this.selectors.mediaView.mediaCropImg).cropper("move", _this.settings.croppedDefaults.moveRightSize, 0);
        });
        
        // On media add cropped image moveUp event
        $(_this.selectors.mediaView.mediaAddModalButtons.moveUp).on('click', function () {
            $(_this.selectors.mediaView.mediaCropImg).cropper("move", 0, _this.settings.croppedDefaults.moveUpSize);
        });
        
        // On media add cropped image moveDown event
        $(_this.selectors.mediaView.mediaAddModalButtons.moveDown).on('click', function () {
            $(_this.selectors.mediaView.mediaCropImg).cropper("move", 0, _this.settings.croppedDefaults.moveDownSize);
        });
        
        // On media add cropped image rotateUndo event
        $(_this.selectors.mediaView.mediaAddModalButtons.rotateUndo).on('click', function () {
            $(_this.selectors.mediaView.mediaCropImg).cropper("rotate", _this.settings.croppedDefaults.rotateUndoRate);
        });
        
        // On media add cropped image rotateRedo event
        $(_this.selectors.mediaView.mediaAddModalButtons.rotateRedo).on('click', function () {
            $(_this.selectors.mediaView.mediaCropImg).cropper("rotate", _this.settings.croppedDefaults.rotateRedoRate);
        });
        
        // On media add cropped image zoomIn event
        $(_this.selectors.mediaView.mediaAddModalButtons.zoomIn).on('click', function () {
            $(_this.selectors.mediaView.mediaCropImg).cropper("zoom", _this.settings.croppedDefaults.zoomInSize);
        });
        
        // On media add cropped image zoomOut event
        $(_this.selectors.mediaView.mediaAddModalButtons.zoomOut).on('click', function () {
            $(_this.selectors.mediaView.mediaCropImg).cropper("zoom", _this.settings.croppedDefaults.zoomOutSize);
        });
        
        // on media add cropped image scaleX event
        $(_this.selectors.mediaView.mediaAddModalButtons.scaleX).on('click', function () {
            _this.settings.croppedDefaults.scaleXRate = _this.settings.croppedDefaults.scaleXRate > 0 ? -_this.settings.croppedDefaults.scaleXRate : Math.abs(_this.settings.croppedDefaults.scaleXRate);
            $(_this.selectors.mediaView.mediaCropImg).cropper("scaleX", _this.settings.croppedDefaults.scaleXRate);
        });
        
        // on media add cropped image scaleY event
        $(_this.selectors.mediaView.mediaAddModalButtons.scaleY).on('click', function () {
            _this.settings.croppedDefaults.scaleYRate = _this.settings.croppedDefaults.scaleYRate > 0 ? -_this.settings.croppedDefaults.scaleYRate : Math.abs(_this.settings.croppedDefaults.scaleYRate);
            $(_this.selectors.mediaView.mediaCropImg).cropper("scaleY", _this.settings.croppedDefaults.scaleYRate);
        });
        
        // On media add cropped image crop event
        $(_this.selectors.mediaView.mediaAddModalButtons.crop).on('click', function () {
            var DATA = $(_this.selectors.mediaView.mediaCropImg).cropper("getCroppedCanvas");
            $(_this.selectors.mediaView.mediaCropImg).cropper("destroy");
            $(_this.selectors.mediaView.mediaCropImg).prop('src', DATA.toDataURL());
            $(_this.selectors.mediaView.mediaAddModalHiddenDataImg).val(DATA.toDataURL());
        });
        
        // On media add cropped image reset event 
        $(_this.selectors.mediaView.mediaAddModalButtons.reset).on('click', function () {
            $(_this.selectors.mediaView.mediaCropImg).cropper("reset");
        });
        
    },
    
    ajaxEventListners : function () {
        var _this = this, ajaxDefaultParams = {};
        
        ajaxDefaultParams = {
            "method" : "POST",
            "async" : true,
            "dataType" : "json",
            "success" : function (r) {  _this._ajaxSuccessHandler(r) },
            "error" : function (r) { _this._ajaxFailHandler(r) },
        };
        
        // Media add ajax event handler
        $(_this.selectors.mediaView.mediaAddModal + " form").on('submit', function () {
            
            ajaxDefaultParams.url = $(this).prop('action');
            ajaxDefaultParams.data = $(this).serializeArray();
            ajaxDefaultParams.success = function (r) {
                var respond = r;

                _this._ajaxSuccessHandler(r, function () {
                    $(_this.selectors.mediaView.mediaAddModal).modal("hide");
                    $(_this.selectors.mediaView.mediaAddModal + " " + _this.selectors.mediaAjaxResultText).text(_this.settings.emptyString);
                }, function () {
                    $(_this.selectors.mediaView.mediaAddModal + " " + _this.selectors.mediaAjaxResultText).text(respond.errorText);
                });
            }
            
            $.ajax(ajaxDefaultParams); 
            return false;
        });
        
        // Media update ajax event handler
        $(_this.selectors.widgetView.widgetUpdateModal + " form").on('submit', function () {
            
            ajaxDefaultParams.url = $(this).prop('action');
            ajaxDefaultParams.data = $(this).serializeArray();
            ajaxDefaultParams.success = function (r) {
                var respond = r;
                
                _this._ajaxSuccessHandler(r, function () {
                    $(_this.selectors.widgetView.widgetUpdateModal).modal("hide");
                    $(_this.selectors.widgetView.widgetUpdateModal + " " + _this.selectors.mediaAjaxResultText).text(_this.settings.emptyString);
                }, function () {
                    $(_this.selectors.widgetView.widgetUpdateModal + " " + _this.selectors.mediaAjaxResultText).text(respond.errorText);
                });
            };
            
            $.ajax(ajaxDefaultParams);
            return false;
            
        });
        
    },
    
    init : function () {
        
        this.modalEventListners();
        this.ajaxEventListners();
        this.cropperEventListners();
        this.widgetEventListners();
        
    }
    
}).init();