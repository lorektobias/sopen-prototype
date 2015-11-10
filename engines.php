<?php
$params = " -H 'accept-encoding: gzip, deflate, sdch' -H 'accept-language: sv-SE,sv;q=0.8,en-US;q=0.6,en;q=0.4' -H 'accept: */*' -H 'referer: https://www.google.se/' -H 'authority: www.google.se' --compressed";
if(isset($argv[1])){
	$word = $argv[1];
} else {
	$word = trim(fgets(STDIN));
	echo $word;
}
$cmd = "curl 'https://www.google.se/s?sclient=psy-ab&q=" . $word . "'" . $params;
$res = shell_exec($cmd);
$json = json_decode($res, true);
$res = preg_replace('%(<b>|</b>)%', '', array_column($json[1], 0));
print_r($res);

