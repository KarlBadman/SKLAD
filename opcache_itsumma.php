<?php
$status = opcache_get_status();
echo "Opcache Free: ".round($status['memory_usage']['free_memory']/($status['memory_usage']['free_memory']+$status['memory_usage']['used_memory'])*100, 2)."%";
