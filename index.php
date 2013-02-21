<?php

$crontab = shell_exec('crontab -l');
$lines = explode("\n", $crontab);


$data = array();
foreach ($lines as $id => $l) {
	$l = trim($l);
	/* FIXME */
	if (empty($l) || $l[0] == '#')
		continue;
	$l = preg_replace('![ \t]+!', ' ', $l);
	if ($l[0] == '@')
		list($time, $cmd) = explode(' ',$l, 2);
	else
		list($m, $h, $dom, $mon, $dow, $cmd) = explode(' ',$l, 6);
	$data[$id] = array(
		'm' => $m,
		'h' => $h,
		'dom' => $dom,
		'mon' => $mon,
		'dow' => $dow,
		'cmd' => $cmd,
	);
}

if (false)
	exec('crontab /tmp/crontab.txt');
?>
<html>
	<head>
		<link rel='stylesheet' type='text/css' href='cron.css' />
	</head>
	<body>
		<form method='POST'>
		<table>
			<tr>
				<td>Day</td>
				<td>Month</td>
				<td>Time</td>
				<td>Weekday</td>
				<td>Command</td>
			</tr>
		<?php foreach ($data as $id => $e): ?>
		<tr>
			<td><input type='text' name='dom[<?= $id ?>]' value='<?= $e['dom'] ?>' /></td>
			<td><input type='text' name='mon[<?= $id ?>]' value='<?= $e['mon'] ?>' /></td>
			<td>
				<input type='text' name='m[<?= $id ?>]' value='<?= $e['m'] ?>' />:<input type='text' name='h[<?= $id ?>]' value='<?= $e['h'] ?>' />
			</td>
			<td>
				<input type='text' name='dow[<?= $id ?>]' value='<?= $e['dow'] ?>' />
			</td>
			<td>
				<?= $e['cmd'] ?>
			</td>
		</tr>
		<?php endforeach; ?>
		</table>
		</form>
	</body>
</html>
