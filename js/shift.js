$(function() {
	$(".datepicker").datepicker({
		dateFormat: 'yy-mm-dd',
		yearSuffix: '年',
		showMonthAfterYear: true,
		monthNames: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
		dayNames: ['日', '月', '火', '水', '木', '金', '土'],
		dayNamesMin: ['日', '月', '火', '水', '木', '金', '土'],
	});

	$('button[name="print"]').click(function() {
		var name = $(this).attr('name');
		var date = $(this).attr('date');
		var pr = $(this).attr('print');
		window.open(name + '.php?date=' + date + '&print=' + pr, '_blank')
	});

	$('button[name="print_user"]').click(function() {
		var name = $(this).attr('name');
		var month = $(this).attr('month');
		var user_id = $(this).attr('user_id');
		window.open(name + '.php?month=' + month + '&user_id=' + user_id, '_blank')
	});

	$('button[name="print_staff"]').click(function() {
		var name = $(this).attr('name');
		var month = $(this).attr('month');
		var staff_id = $(this).attr('staff_id');
		window.open(name + '.php?month=' + month + '&staff_id=' + staff_id, '_blank')
	});
	
	$('button#add_row').click(function() {
		var shift_user_id = $(this).attr('shift_user_id');
		var option = $('td[shift_user_id="' + shift_user_id + '"] select').html().replace(' selected', '');
		var row = '<span class="shift_row"><input type="tel" class="time" name="staff_start[]" />～<input type="tel" class="time" name="staff_end[]" /><select name="staff_id_1[]">' + option + '</select><select name="staff_id_2[]">' + option + '</select><input type="hidden" name="shift_user_id[]" value="' + shift_user_id + '" /><input type="hidden" name="shift_staff_id[]" value="" /></span>';
		$(row).appendTo($('td[shift_user_id="' + shift_user_id + '"]'));
		$("input.time").mask("99:99");
	});
	
	$('button.delete_shift').click(function() {
		var id = $(this).val();
        $('<form/>', {action: '', method: 'post'})
		.append($('<input/>', {type: 'hidden', name: 'delete_id', value: id}))
  		.appendTo(document.body)
  		.submit();
	});
	
	$('input#check_all').on("click",function(){
		$('input.foyer').prop("checked", $(this).prop("checked"));
	});
});