$(document).ready(function(){

	var existCookies=Cookies.get();//recupera tutti i cookie del sito
	$.each( existCookies, function(key, value){ //scorre l'oggetto existCookies
		if( key.substring(0,5)=="ready" ){
  			//alert( key + ": " + value );
  			$("#"+value).css("background-color","rgb(127, 255, 88)");
  			$("#"+value).find(".ready img").css("visibility", "hidden");
  		}
	});

	$(".client, .dateTime, .ship, .payed, .ready, .succeded, .productTitle, .brand, .origin, .price, .quantity, .priceTot, .maturation, .agriType, .km0").on("mouseover", function(){
		evidenzia($(this).attr("class")); //passa alla funzione la classe dell'elemento selezionato
	});
	$(".client, .dateTime, .ship, .payed, .ready, .succeded, .productTitle, .brand, .origin, .price, .quantity, .priceTot, .maturation, .agriType, .km0").on("mouseout", function(){
		disevidenzia($(this).attr("class")); //passa alla funzione la classe dell'elemento selezionato
	});

	function evidenzia(elem){
		$(".legend").find("."+elem).css("text-decoration", "underline"); //elem è la classe dell'elemento coinvolto (dentro .legend)
	}
	function disevidenzia(elem){
		$(".legend").find("."+elem).css("text-decoration", "none");
	}

	//cambia il colore di sfondo di un ordine pronto
	$(".ready img").on("click", function(){
		$(this).parent().parent().parent().css("background-color","rgb(127, 255, 88)");
		$(this).css("visibility", "hidden");

		//settaggio cookie
		var idRow=$(this).parent().parent().parent().attr("id");//id della riga dell'ordine
		Cookies.set('ready'+idRow, ""+idRow, { expires: 1, path: '/'}); //nome,valore,scadenza in gg, path di validità
		
	});

	//resetta il testo della risposta al feedback
	$("#replyText").on("focus", function(){
		$(this).val("");
	});

	//per la risposta al feedback nel controlpanel.php
	$("#feedbackReplyButton").on("click",function(e){
		var testoFeedbackRisposta=$("#replyText").val();
		if(testoFeedbackRisposta!="" && testoFeedbackRisposta!="(max 200 caratteri)")
			$(this).trigger("click");
		else{
			e.preventDefault();
			alert("Risposta non valida");
		}
	});

});