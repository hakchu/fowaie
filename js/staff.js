$(function() {
	$(".datepicker").datepicker({
		dateFormat: 'yy-mm-dd',
		yearSuffix: '年',
		showMonthAfterYear: true,
		monthNames: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
		dayNames: ['日', '月', '火', '水', '木', '金', '土'],
		dayNamesMin: ['日', '月', '火', '水', '木', '金', '土'],
	});

	var staffId = $( "#staff_id" ),
	allFields = $( [] ),
	tips = $( ".validateTips" );
	
	$('button.trans').click(function() {
		var name = $(this).attr('name');
		var id = $(this).val();
		window.location.href = name + '.php?staff_id=' + id;
	});

	$('button.delete').click(function() {
		var id = $(this).val();
		$('#delete_dialog').load('delete.php?staff_id=' + id);
		$('#delete_dialog').dialog('open');
	});

	$( "#delete_dialog" ).dialog({
		autoOpen: false,
		height: 300,
		width: 500,
		modal: true,
		show: "fade",
		hide: "fade",
		buttons: {
			"削除": function() {
				// PHPファイルにPOST
				$.post("delete.php", {
					"staff_id":staffId.val()
				}, function(){
					location.reload();
				});
			},
			"キャンセル": function() {
				location.reload();
			}
		},
		close: function() {
			allFields.val( "" ).removeClass( "ui-state-error" );
			location.reload();
		}
	});

	$('button.print').click(function() {
		var name = $(this).attr('name');
		var staff_id = $(this).attr('staff_id');
		var start = $(this).attr('start');
		var end = $(this).attr('end');
		window.open(name + '.php?staff_id=' + staff_id + '&start=' + start + '&end=' + end, '_blank')
	});

	$('button.delete_shift').click(function() {
		var id = $(this).val();
        $('<form/>', {action: '', method: 'post'})
		.append($('<input/>', {type: 'hidden', name: 'delete_id', value: id}))
  		.appendTo(document.body)
  		.submit();
	});	

	$('button.delete_day_off').click(function() {
		var id = $(this).val();
        $('<form/>', {action: '', method: 'post'})
		.append($('<input/>', {type: 'hidden', name: 'delete_id', value: id}))
  		.appendTo(document.body)
  		.submit();
	});	
});