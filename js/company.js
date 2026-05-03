$(function() {
	var companyId = $( "#company_id" ),
	allFields = $( [] ),
	tips = $( ".validateTips" );

	$('button.trans').click(function() {
		var name = $(this).attr('name');
		var id = $(this).val();
		window.location.href = name + '.php?company_id=' + id;
	});

	$('button.delete').click(function() {
		var id = $(this).val();
		$('#delete_dialog').load('delete.php?company_id=' + id);
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
					"company_id":companyId.val()
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

});