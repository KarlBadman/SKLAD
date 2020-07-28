'use strict';
importScripts('sw-toolbox.js');
toolbox.precache(['index.html','/css/dsklad-styles.css', '/css/index/index.css']);
toolbox.router.get('/images/*', toolbox.cacheFirst);
toolbox.router.get('/*', toolbox.networkFirst, {
networkTimeoutSeconds: 5});