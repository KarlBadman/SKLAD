<?php

$kod = '{"data": {"id": "1637230502947381685_6234489494", "user": {"id": "6234489494", "full_name": "\u0412\u0438\u0442\u0430\u043b\u0438\u0439", "profile_picture": "https://scontent.cdninstagram.com/t51.2885-19/s150x150/22861121_285726901921341_7680435774847713280_n.jpg", "username": "snwm88"}, "images": {"thumbnail": {"width": 150, "height": 150, "url": "https://scontent.cdninstagram.com/t51.2885-15/s150x150/e35/22858229_240196549843819_6634770074752253952_n.jpg"}, "low_resolution": {"width": 320, "height": 320, "url": "https://scontent.cdninstagram.com/t51.2885-15/s320x320/e35/22858229_240196549843819_6634770074752253952_n.jpg"}, "standard_resolution": {"width": 640, "height": 640, "url": "https://scontent.cdninstagram.com/t51.2885-15/s640x640/sh0.08/e35/22858229_240196549843819_6634770074752253952_n.jpg"}}, "created_time": "1509393106", "caption": null, "user_has_liked": true, "likes": {"count": 1}, "tags": [], "filter": "Normal", "comments": {"count": 0}, "type": "image", "link": "https://www.instagram.com/p/Ba4nHfPnOG1/", "location": null, "attribution": null, "users_in_photo": []}, "meta": {"code": 200}}';

echo "<pre>";
print_r(json_decode($kod,true));
echo "</pre>";
?>