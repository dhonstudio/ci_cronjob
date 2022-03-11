<?php

$curl = curl_init();

/*
| -------------------------------------------------------------------
|  Set up this cronjob email backup link and db
| -------------------------------------------------------------------
*/
$link   = 'https://domain.com/';
$dbs    = 'default+users';

curl_setopt($curl, CURLOPT_URL, $link.'email/send_backup?db='.$dbs);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
curl_exec($curl);
echo date('d-m-Y H:i:s').': backup success';
curl_close($curl);