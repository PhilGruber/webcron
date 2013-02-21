<?php

error_reporting(E_ALL);

$crontab = shell_exec('crontab -l');
$lines = explode("\n", $crontab);

$data = array();
$file = array();
foreach ($lines as $id => $l) {
	$l = trim($l);
	$file[$id] = $l;

	if (strpos($l, '#') !== false)
		$l = substr($l, 0, strpos($l, '#'));

	if (empty($l))
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

if (isset($_POST['save'])) {
	foreach($_POST['data'] AS $id => $d) {
		if ($d['state'] == 'deleted') {
			unset($file[$id]);
			echo "deleted<br />";
		} else
			$file[$id] = "{$d['m']}\t{$d['h']}\t{$d['dom']}\t{$d['mon']}\t{$d['dow']}\t{$d['cmd']}";
	}
	$output = '';
	foreach ($file as $l)
		$output .= "$l\n";
	$output = preg_replace("!\n+$!", "\n", $output);

	file_put_contents('/tmp/crontab.plain', print_r($file, true));
	file_put_contents('/tmp/crontab.txt', $output);
	exec('crontab /tmp/crontab.txt');
	$message = 'Crontab was saved.';
}
	print_r($_POST);
	print_r($data);
?>
<html>
	<head>
		<link rel='stylesheet' type='text/css' href='cron.css' />
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script src="cron.js"></script>
		<script>
			var linecount = <?= count($file); ?>;
		</script>
	</head>
	<body>
	<div class='crontab'>
	<?php if (isset($message)): ?>
	<div class='notice'>
		<?= $message ?>
	</div>
	<?php endif; ?>
		<form method='POST'>
		<table id='cron'>
			<tr>
				<td>Time</td>
				<td>Day</td>
				<td>Month</td>
				<td>Weekday</td>
				<td>Command</td>
				<td></td>
			</tr>
		<?php foreach ($data as $id => $e): ?>
		<tr id='row-<?= $id ?>'>
			<td>
				<input class='num' type='text' name='data[<?= $id ?>][h]' value='<?= $e['h'] ?>' />
				<input class='num' type='text' name='data[<?= $id ?>][m]' value='<?= $e['m'] ?>' />
			</td>
			<td><input class='num' type='text' name='data[<?= $id ?>][dom]' value='<?= $e['dom'] ?>' /></td>
			<td><input class='num' type='text' name='data[<?= $id ?>][mon]' value='<?= $e['mon'] ?>' /></td>
			<td>
				<input class='num' type='text' name='data[<?= $id ?>][dow]' value='<?= $e['dow'] ?>' />
			</td>
			<td>
				<input class='' type='text' name='data[<?= $id ?>][cmd]' value='<?= $e['cmd'] ?>' />
			</td>
			<td>
				<input type='hidden' id='state-<?= $id ?>' name='data[<?= $id ?>][state]' value='normal' />
				<img onclick='del(<?= $id ?>);' src='icons/delete.png' alt='X' />
			</td>
		</tr>
		<?php endforeach; ?>
		</table>
		<img class='addbutton' onclick='add(<?= $id ?>);' src='icons/add.png' alt='+' />
		<br />
		<input type='submit' value='save' name='save' />
		</form>
		</div>
	</body>
</html>
