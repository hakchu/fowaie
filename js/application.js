$(function() {

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
			"承認": function() {
				// PHPファイルにPOST
				$.post("detail.php", {
					"approve":applicationId.val(),
					"comment":$("#comment").val()
				}, function(){
					location.reload();
				});
			},
			"非承認": function() {
				// PHPファイルにPOST
				$.post("detail.php", {
					"unapprove":applicationId.val(),
					"comment":$("#comment").val()
				}, function(){
					location.reload();
				});
			},
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

	$('button.register').click(function() {
		var id = $(this).val();
        $('<form/>', {action: '', method: 'post'})
		.append($('<input/>', {type: 'hidden', name: 'register_id', value: id}))
  		.appendTo(document.body)
  		.submit();
	});
});