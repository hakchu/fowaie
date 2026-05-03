$(function() {
	$(".datepicker").datepicker({
		dateFormat: 'yy-mm-dd',
		yearSuffix: '年',
		showMonthAfterYear: true,
		monthNames: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
		dayNames: ['日', '月', '火', '水', '木', '金', '土'],
		dayNamesMin: ['日', '月', '火', '水', '木', '金', '土'],
	});

	$('#record1 button.edit').click(function() {
		var shift_user_id = $(this).val();
		window.location.href = 'edit.php?shift_user_id=' + shift_user_id + '&backto=0';
	});

	$('#record2 button.edit').click(function() {
		var shift_user_id = $(this).val();
		window.location.href = 'edit.php?shift_user_id=' + shift_user_id + '&backto=1';
	});

	$('button.print').click(function() {
		var shift_user_id = $(this).val();
		var page = $(this).attr('page');
		window.open('print.php?shift_user_id=' + shift_user_id + '&page=' + page, '_blank');
	});
	
	$('#record1 button.print_all').click(function() {
		var date = $(this).attr('date');
		window.open('print_all.php?date=' + date, '_blank');
	});
	
	$('#record2 button.print_all').click(function() {
		var year = $(this).attr('year');
		var month = $(this).attr('month');
		var user_id = $(this).attr('user_id');
		window.open('print_all2.php?year=' + year + '&month=' + month + '&user_id=' + user_id, '_blank');
	});
	
	if($('input.issue:checked').length == $('input.issue').length) {
		$('#all').prop('checked', true);
	}
	
	$('#all').click(function() {
		if(this.checked) {
			$('input.issue').attr('checked','checked');
			$.ajax({
				url: './',
				type: 'POST',
				data: {
					'all': 1,
				},
				dataType: 'json',
			});
		} else {
			$('input.issue').removeAttr('checked');
			$.ajax({
				url: './',
				type: 'POST',
				data: {
					'all': 0,
				},
				dataType: 'json',
			});
		}
	});

	$('input.issue').change(function() {
		var shift_user_id = $(this).val();
		var issue = ($(this).prop('checked') == true) ? 1 : 0;
		
		$.ajax({
			url: './',
			type: 'POST',
			data: {
				'shift_user_id': shift_user_id,
				'issue': issue,
			},
			dataType: 'json',
		});
	});
	
	var nowchecked = [];
	$('input[type="radio"]:checked').each(function(){
		nowchecked.push( $(this).attr('id') );
	});

	$('input[type="radio"]').click(function(){
		var idx = $.inArray( $(this).attr('id'), nowchecked );
		if( idx >= 0 ) {
			$(this).prop('checked', false);
			nowchecked.splice(idx, 1);
		} else {
			var name = $(this).attr('name');
			$('input[name="' + name + '"]').each(function(){
				var idx2 = $.inArray( $(this).attr('id'), nowchecked);
				if( idx2 >= 0 ){
					nowchecked.splice(idx2,1);
				}
			});
			nowchecked.push( $(this).attr('id') );
		}
	});

	$('input[name="delete"]').click(function() {
		var id = $('#record_id').val();
		$('#delete_dialog').load('delete.php?record_id=' + id);
		$('#delete_dialog').dialog('open');
	});


	var recordId = $("#record_id").val();
	var shift_user_id = $("#shift_user_id").val();
	allFields = $( [] ),
	tips = $( ".validateTips" );
	
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
					"record_id": recordId
				}, function(){
					window.location.href = 'edit.php?shift_user_id=' + shift_user_id;
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

	$('input#money0, input#money1').change(function(){
		var money0 = $('input#money0').val();
		var money1 = $('input#money1').val();
		$('input#money2').val(money0 - money1);
	});
});