// Logger JS file

(function () {
    
    window.logger = {
        
        settings : {
            debug : false,
            logFilePath : '/local/templates/dsklad/ajax/jslog.php',
            ajaxMethod : "POST"
        },
        
        config : {
            stdOuthandler : {},
        },
        
        events : {
            load : 'logger_load'
        },

        setStackTraceHandler : function () {
            var _this = this, xhr, requestString = "";
            
            window.addEventListener('error', function (e) {
                var request = {};
                request.location = window.location.href ? window.location.href : "- not set -";
                request.error = e.error ? e.error : "- not set -";
                request.type = e.type ? e.type : "- not set -";
                request.filename = e.filename ? e.filename : "- not set -";
                request.lineno = e.lineno ? e.lineno : "- not set -";
                request.message = e.message ? e.message : "- not set -";
                if (_this.settings.debug) console.log(e);
                xhr = new XMLHttpRequest();
                xhr.open(_this.settings.ajaxMethod, _this.settings.logFilePath, true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.send("request="+encodeURIComponent(JSON.stringify(request)));
            });
            
        },

        setLoggerEventTrigger : function (en, detail) {
            var _this = this, detail, en;
            
            detail = detail ? detail : 'Event success triggered';
            en = en ? en : 'default_event';
            document.addEventListener(en, function(e) {
                if (_this.settings.debug)
                    console.log(e.detail);
            });
            
            try {
                var event = new CustomEvent(en, {
                    detail : detail
                });
            } catch(err) {
                var event = document.createEvent(en);
                event.initEvent(en, true, true);
                event.detail = detail;
            }
            
            document.dispatchEvent(event);
        },
        
        escapeJSON : function() {
            var result = "";
            for (var i = 0; i < this.length; i++) {
                var ch = this[i];
                
                switch (ch) {
                    case "\\": ch = "\\\\"; break;
                    case "\'": ch = "\\'"; break;
                    case "\"": ch = '\\"'; break;
                    case "\&": ch = "\\&"; break;
                    case "\t": ch = "\\t"; break;
                    case "\n": ch = "\\n"; break;
                    case "\r": ch = "\\r"; break;
                    case "\b": ch = "\\b"; break;
                    case "\f": ch = "\\f"; break;
                    case "\v": ch = "\\v"; break;
                    default: break;
                }
        
                result += ch;
            }
    
            return result;
        },
        
        init : function () {
            this.setStackTraceHandler();
            this.setLoggerEventTrigger(this.events.load, 'Logger load is success');
        }
        
    }.init();
    
})()