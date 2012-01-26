


$(function(){

	$("#telefone").bind("keypress",function(e){
		var charCode = e.which || e.keyCode;
		
		if (charCode==46 || charCode==39 || charCode==37){
			return true;
		}
		if (charCode > 31 && (charCode < 48 || charCode > 57)){
			return false;
		}
		return true;
	})

	$("#frmEspontanea").bind("submit", function(){
		var formOk = true;
		var fieldsCorrigir = [];
		var emailCheck = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
		
		if ($("#nome").val().length < 1 ) {
			formOk = false;
			fieldsCorrigir.push("Nome");
		}
		if (($("#email").val().length < 1 ) || (!emailCheck.test($("#email").val()))) {
			formOk = false;
			fieldsCorrigir.push("E-Mail");
		}
		if ($("#telefone").val().length < 1 ) {
			formOk = false;
			fieldsCorrigir.push("Telefone");
		}
		if ($("#cv").val().length < 1) {
			formOk = false;
			fieldsCorrigir.push("CV");
		}
		if (!formOk) {
			alerta("É necessário preecher correctamente os seguintes campos: " +fieldsCorrigir.join(", "));
			return false;
		} else {
			return true;
		}
	});
	
	
});