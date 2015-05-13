function add() {
	$("#cron").append("<tr><td>\
				<input class='num' type='text' name='data["+linecount+"][h]' />\
				<input class='num' type='text' name='data["+linecount+"][m]' />\
			</td>\
			<td><input class='num' type='text' name='data["+linecount+"][dom]' /></td>\
			<td><input class='num' type='text' name='data["+linecount+"][mon]' /></td>\
			<td><input class='num' type='text' name='data["+linecount+"][dow]' /></td>\
			<td><input class='' type='text' name='data["+linecount+"][cmd]' /></td>\
			<td>\
				<input type='hidden' id='state-"+linecount+"' name='data["+linecount+"][state]' value='normal' />\
				<img onclick='del("+linecount+");' src='icons/delete.png' alt='X' />\
			</td>\
		</tr>");
	linecount++;
}

function del(id) {
	$("#state-"+id).val('deleted');
	$("#row-"+id).find('td').slideUp(300);
	console.log("Deleted row "+id);
}

function debug() {
	$('pre').toggleClass('invisible');
}