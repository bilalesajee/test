	<link rel="stylesheet" href="../ui/development-bundle/themes/base/jquery.ui.all.css">
	
	<script src="../ui/development-bundle/external/jquery.bgiframe-2.1.2.js"></script>
	<script src="../ui/development-bundle/ui/jquery.ui.core.js"></script>
	<script src="../ui/development-bundle/ui/jquery.ui.widget.js"></script>
	<script src="../ui/development-bundle/ui/jquery.ui.mouse.js"></script>
	<script src="../ui/development-bundle/ui/jquery.ui.button.js"></script>
	<script src="../ui/development-bundle/ui/jquery.ui.draggable.js"></script>
	<script src="../ui/development-bundle/ui/jquery.ui.position.js"></script>
	<script src="../ui/development-bundle/ui/jquery.ui.resizable.js"></script>
	<script src="../ui/development-bundle/ui/jquery.ui.dialog.js"></script>
	<script src="../ui/development-bundle/ui/jquery.effects.core.js"></script>
	<link rel="stylesheet" href="../demos.css">
	<script>
	$(function() 
	{
		$( "#dialog-form" ).dialog({
			autoOpen: false,
			//height: '100%',
			width: '920',
			modal: true,
			position: 'top' /*,
			buttons: {
				"Create an account": function() {
					var bValid = true;
					
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			},
			close: function() {
				//allFields.val( "" ).removeClass( "ui-state-error" );
			}*/
		});

		/*$( "#create-user" )
			.button()
			.click(function() {
				$( "#dialog-form" ).dialog( "open" );
			});*/
	});
	function opendialog(frmid,page)
	{
		$( "#"+frmid ).load( page);
		$( "#"+frmid ).dialog( "open" );
		
	}
	function showdialog(frmid,page)//showpage(0,'','addsupplier.php','subsection','$div')
	{
		if(frmid=='')
		{
			frmid=	'dialog-form';
		}
		$( "#"+frmid ).load( page);
		$( "#"+frmid ).dialog( "open" );
		
	}

	</script>
<div id="dialog-form" title="" >
</div>
<!--<button id="create-user" onclick="opendialog('dialog-form','addsupplier.php')">Create new user</button>-->

