'use strict';
importScripts('sw-toolbox.js');
toolbox.options.debug = true;
toolbox.precache(['../css/dsklad-styles.css']);
toolbox.router.get('/images/*', toolbox.cacheFirst);
toolbox.router.get('/*', toolbox.networkFirst, {
networkTimeoutSeconds: 5});