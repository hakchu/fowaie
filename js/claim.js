$(function() {
	$('button.print').click(function() {
		var name = $(this).attr('name');
		var year = $(this).attr('year');
		var month = $(this).attr('month');
		window.open(name + '.php?year=' + year + '&month=' + month, '_blank')
	});
});