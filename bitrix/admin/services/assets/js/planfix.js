// Planfix js file
(function () {
    
    cardPrint = {
        
        settings : {
            debug : false,
            importCSS : true,
            importStyle : true,
            printContainer : true,
            loadCss : "",
            pageTitle : "",
            removeInline : false,
            printDelay : 333,
            header : null,
            formValues : true,
            selectors : {
                printBtnClass : '.print-btn',
                printContainerClass : '.print-container'
            }
        },
        
        printBlock : function () {
            var _this = this;
            
            $(document).on("click", _this.settings.selectors.printBtnClass, function (e) {
                e.preventDefault(); e.stopPropagation();
                
                $(_this.settings.selectors.printContainerClass).printThis({
                    debug: _this.settings.debug,
                    importCSS: _this.settings.importCSS,
                    importStyle: _this.settings.importStyle,
                    printContainer: _this.settings.printContainer,
                    loadCSS : _this.settings.loadCss, 
                    pageTitle: _this.settings.pageTitle,
                    removeInline: _this.settings.removeInline,
                    printDelay: _this.settings.printDelay,
                    header: _this.settings.header,
                    formValues: _this.settings.formValues
                });

            });
            
        }, 
        
        init : function () {
            this.printBlock();
        }
    }
    
    
    $(document).ready(function () {
        cardPrint.init();
    });
    
})()