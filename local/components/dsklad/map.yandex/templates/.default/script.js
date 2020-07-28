//
(function () {

    window.dskladMapYndex = {

        settings: {  // default settings
            
        },

        mapInstance : {  // My map instance

        },

        objectInstance : {  // My object instance

        },

        balloonInstance : { // My map balloon

        },

        legendInstance : {  // My map legend

        },

        fn:function() {
            return false;
        },

        callbackDefault : function(callback, e) {
            var self = this;
            
            if (!!callback) {
                callback(e);
            } else {
                self.fn(e);
            }
        },

        setMapPlacemarks : function () {

            var self = this;

            self.objectInstance = new ymaps.ObjectManager(
                self.settings.placemarks
            );
            self.objectInstance.add(self.plas);
            self.mapInstance.geoObjects.add(self.objectInstance);
        },

        setMapBalloon : function () {

            var self = this;

            self.balloonInstance = new ymaps.Balloon(
                self.mapInstance
            );
        },

        setMapLegend : function(option) { // создание легенды карты (не доделан)

            var self = this

            self.legendInstance = function (options) {
                self.legendInstance.superclass.constructor.call(this, options);
                this._$content = null;
                this._geocoderDeferred = null;
            };

            ymaps.util.augment(self.legendInstance, ymaps.collection.Item, {
                onAddToMap: function (map,content) {
                    self.legendInstance.superclass.onAddToMap.call(this, map);
                    this._lastCenter = null;
                    this.getParent().getChildElement(this).then(this._onGetChildElement, this);
                },

                _onGetChildElement: function (parentDomContainer) {
                    this._$content = $('<div class="legend_box">Тест</div>').appendTo(parentDomContainer);
                    this._mapEventGroup = this.getMap().events.group();
                },
            });

            newControl = new self.legendInstance;

            self.mapInstance.controls.add(newControl,option);

        },

        alignCardSize : function () { // размер облости карты, что-бы были видны все точки
            var self = this;
            self.mapInstance.setBounds(
                self.objectInstance.getBounds(),
                {checkZoomRange:true}).then(function(){
                    if(self.mapInstance.getZoom() > 17) self.mapInstance.setZoom(17);
                    if(self.mapInstance.getZoom() < 2) self.mapInstance.setZoom(dskladMapYndex.settings.map.options.zoom);
                }
            );
        },

        parseArrayDpdToMap : function(obj){ // метод костыль, разбор даннвх от дпд в формат yandex

            arPlas = {"type":"FeatureCollection"};
            arPlas.features = [];

            if (!!obj.DPD.TERMINAL) {

                for (var i = 0; i < obj.DPD.TERMINAL.length; i++) {

                    element = obj.DPD.TERMINAL[i];

                    if (element.is_terminal == 'Y') {
                        color = '#1ab500';
                        preset = 'islands#greenDotIcon';
                        terminal = true;
                    } else {
                        color = '#1d97ff';
                        preset = 'islands#blueDotIcon';
                        terminal = false;
                    }

                    newPlas = {
                        "type": "Feature",
                        "id": element.terminalCode,
                        "geometry": {
                            "type": "Point",
                            "coordinates": [element.geoCoordinates.latitude, element.geoCoordinates.longitude]
                        },
                        "properties": {
                            "balloonContent": element.address.descript,
                            "hintContent": element.terminalName,
                        },
                        "options": {
                            "iconColor": color,
                            "preset": preset,
                            "terminal": terminal,
                        }
                    }

                    arPlas.features.push(newPlas);
                }

                return arPlas;

            }else{
                return false;
            }
        },

        ballonOpenId: function(plasmarkId){ //открыть балон по id точка
            var self = this;

            self.mapInstance.geoObjects.get(0).objects.balloon.open(plasmarkId);
        },

        setMapInstanceEventsListner : function(eventName, callback){
            var self = this;

            if (eventName == "objectClickEventHandler")
                self.objectInstance.events.add('click', function (e) {
                    self.callbackDefault(callback, e);
                });

            if (eventName == "mapClickEventHandler")
                self.mapInstance.events.add('click', function (e) {
                    self.callbackDefault(callback, e);
                });

            if (eventName == "openEventBalloon")
                self.objectInstance.objects.events.add('balloonopen', function (e) {
                    self.callbackDefault(callback, e);
                });

            if(eventName == "overlayClickEventHandler")
                self.objectInstance.objects.overlays.events.add('click', function (e) {
                    self.callbackDefault(callback, e);
                });
        },
        
        updateMapPlacemarks : function (t) {
            var self = this;
            
            if (!!t.DPD.TERMINAL) {
                self.mapInstance.setCenter([t.DPD.mapParams.yandex_lat, t.DPD.mapParams.yandex_lon]);
                self.objectInstance.removeAll();
                self.objectInstance.add(JSON.stringify(self.parseArrayDpdToMap(t)));
                self.alignCardSize();
            } else 
                self.objectInstance.removeAll();
        },
        
        createMapInstance : function () {
            var self = this;

            ymaps.ready(function () {
                self.mapInstance =  new ymaps.Map(
                    self.settings.map.id,
                    self.settings.map.options
                );

                self.mapInstance.options.set('yandexMapDisablePoiInteractivity', self.settings.map.options.yandexMapDisablePoiInteractivity);
                self.setMapPlacemarks();
                self.setMapBalloon();
                if(self.plas) {
                    self.alignCardSize();
                }
                if(self.settings.search_plas = 'Y'){
                    self.setSearchMap();
                }

                $(document).trigger('mymap.eventreadyinstance');

            });
        },

        setSearchMap: function(){ // подключаем поиск
            var self = this;

            function CustomSearchProvider(points) {
                this.points = points.features;
            }

            CustomSearchProvider.prototype.geocode = function (request, options) {
                var deferred = new ymaps.vow.defer(),
                    geoObjects = new ymaps.GeoObjectCollection(),
                    offset = options.skip || 0,
                    limit = options.results || 20;

                var points = [];
                for (var i = 0, l = this.points.length; i < l; i++) {
                    var point = this.points[i];

                    if(!!point.properties.search_description) {

                        if (point.properties.search_description.toLowerCase().indexOf(request.toLowerCase()) != -1) {
                            points.push(point);
                        }

                    }else{
                        if (point.properties.balloonContent.toLowerCase().indexOf(request.toLowerCase()) != -1) {
                            points.push(point);
                        }
                    }
                }

                points = points.splice(offset, limit);

                for (var i = 0, l = points.length; i < l; i++) {
                    var point = points[i],
                        coords = point.geometry.coordinates;

                    if(!!point.properties.search_description) {

                        geoObjects.add(new ymaps.Placemark(coords, {
                            name: point.properties.search_title,
                            description: point.properties.search_description,
                            balloonContentBody: '<p>' + point.geometry.coordinates + '</p>',
                            boundedBy: [coords, coords]
                        }));

                    }else{
                        geoObjects.add(new ymaps.Placemark(coords, {
                            name: point.properties.hintContent,
                            description: point.properties.balloonContent,
                            balloonContentBody: '<p>' + point.geometry.coordinates + '</p>',
                            boundedBy: [coords, coords]
                        }));
                    }
                }

                deferred.resolve({
                    geoObjects: geoObjects,
                    metaData: {
                        geocoder: {
                            request: request,
                            found: geoObjects.getLength(),
                            results: limit,
                            skip: offset
                        }
                    }
                });

                return deferred.promise();
            };

            if (self.mySearchControl) {
                self.mapInstance.controls.remove(self.mySearchControl);
            }

            self.mySearchControl = new ymaps.control.SearchControl({
                options: {
                    provider: new CustomSearchProvider(self.plas),
                    noPlacemark: true,
                    resultsPerPage: 5
                }});

            self.mapInstance.controls.add(self.mySearchControl, { float: 'left' });

            self.mySearchControl.events.add('resultselect', function (e) {

              var pointMap = self.mySearchControl.getResultsArray()[e.get('index')].geometry.getCoordinates();

              for (var i = 0, l = self.plas.features.length; i < l; i++) {
                  var point = self.plas.features[i];

                  if(point.geometry.coordinates[0] == pointMap[0] && point.geometry.coordinates[1] == pointMap[1]) {
                      self.mapInstance.geoObjects.get(0).objects.balloon.open(point.id);
                  }
              }
            });

        },

        // Init method
        init : function () {
            this.createMapInstance();
        }
    };

})();
