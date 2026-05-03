$(function() {
	$(".datepicker").datepicker({
		dateFormat: 'yy-mm-dd',
		yearSuffix: '年',
		showMonthAfterYear: true,
		monthNames: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
		dayNames: ['日', '月', '火', '水', '木', '金', '土'],
		dayNamesMin: ['日', '月', '火', '水', '木', '金', '土'],
	});

	var applicationId = $( "#application_id" ),
	allFields = $( [] ),
	tips = $( ".validateTips" );
	
	$('button.detail').click(function() {
		var id = $(this).val();
		$('#detail_dialog').load('detail.php?application_id=' + id);
		$('#detail_dialog').dialog('open');
	});

	$( "#detail_dialog" ).dialog({
		autoOpen: false,
		height: 500,
		width: 500,
		modal: true,
		show: "fade",
		hide: "fade",
		buttons: {
			"閉じる": function() {
				location.reload();
			}
		},
		close: function() {
			allFields.val( "" ).removeClass( "ui-state-error" );
			location.reload();
		}
	});

	$('button.print').click(function() {
		var id = $(this).val();
		window.open('print.php?id=' + id);
	});

	$('button.edit').click(function() {
		var id = $(this).val();
		window.location.href = 'edit.php?id=' + id;
	});

	$('input[name=lock]').change(function(){
		if($(this).prop('checked')) {
			$('input[name=submit]').prop('disabled', false);
		} else {
			$('input[name=submit]').prop('disabled', true);
		}
	});
	
	$('input[div=amount]').change(function(){
		var total = 0;
		$('input[div=amount]').each(function(){
			var amount = ($(this).val() != "") ? $(this).val() : 0;
			amount = parseInt(amount);
			total += amount;
		});
		$('td[div=total]').text(total.toLocaleString() + '円');
	});
});