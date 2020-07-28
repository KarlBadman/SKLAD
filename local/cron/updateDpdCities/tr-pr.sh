#!/bin/bash

# # # #
# Needed for cron 
# After trigger starts processing.php 30 times with 5 min interval
# # # #

/usr/bin/php -f /home/bitrix/www/local/cron/updateDpdCities/trigger.php

for i in {0..30}
do
    /usr/bin/php -f /home/bitrix/www/local/cron/updateDpdCities/processing.php
    sleep 300
done

