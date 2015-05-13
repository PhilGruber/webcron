<?php
if (isset($_POST['save'])) {
  $output = ''; // Will contain new overriding cron jobs
	foreach($_POST['data'] AS $id => $d) {
		if ($d['state'] != 'deleted')
		  $output .= trim("{$d['m']}\t{$d['h']}\t{$d['dom']}\t{$d['mon']}\t{$d['dow']}\t{$d['cmd']}")."\n";
	}
	$output = preg_replace("!\n+!", "\n", $output); // Replace multiple line breaks with only one

	file_put_contents('/tmp/crontab.txt', $output);
	$res = exec('crontab /tmp/crontab.txt 2>&1');
	$message = empty($res)?'Crontab was saved.':$res;
}

$crontab_output = shell_exec('crontab -l'); // Current cron jobs
$crontab = preg_replace("!\n+!", "\n", $crontab_output); // Replace 1+ line breaks with only one
$lines = explode("\n", $crontab);
$data = array(); // Display of current cron jobs (array contains arrays)

foreach ($lines as $id => $l) {
  $l = trim($l); // Remove line break

  if (strpos($l, '#') !== false) // If it contains a '#'
    $l = substr($l, 0, strpos($l, '#')); // Everything after '#'

  if (empty($l)) # We might have emptied the line (if it was a whole comment) so skip it
    continue;

  $l = preg_replace('![ \t]+!', ' ', $l); // Replace multiple whitespaces and tabs by a single space, so empty inputs submitted will be removed
  if ($l[0] == '@') // Format is @time command
    list($time, $cmd) = explode(' ',$l, 2);
  else // Format is m d dom mon dow command
    list($m, $h, $dom, $mon, $dow, $cmd) = explode(' ',$l, 6);

  $data[] = array( // Current cron jobs
    'm' => $m,
    'h' => $h,
    'dom' => $dom,
    'mon' => $mon,
    'dow' => $dow,
    'cmd' => $cmd,
  );
}
?>
<html>
	<head>
		<link rel='stylesheet' type='text/css' href='cron.css' />
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script src="cron.js"></script>
		<script>
			var linecount = <?= count($data); ?>;
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
				<input class='num right' type='text' name='data[<?= $id ?>][h]' value='<?= $e['h'] ?>' />:<!--
				--><input class='num' type='text' name='data[<?= $id ?>][m]' value='<?= $e['m'] ?>' />
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
		<input type='submit' value='save' name='save' /><button onclick="debug(); return false;">debug</button>
		</form>
		</div>
    <?= '<pre class="invisible">Content of crontab -l'."\n".$crontab_output.'</pre>'; ?>
	</body>
</html>
