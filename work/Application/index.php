<?php

include 'MyClass/map.class.php';

$map=new map();
$ip=$map->getip();
echo $ip;
$pos=$map->locationByIP($ip);
$pos1=$map->locationByGPS($pos['lng'],$pos['lat']);
echo '<pre>';
print_r($pos);
print_r($pos1);
include 'MyClass/CheckIp.class.php';
$check=new CheckIp();
print_r($check->getrealplace($ip));die;
