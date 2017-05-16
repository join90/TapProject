$(document).ready(function(){

	//per tenere selezionata la città corretta
	var city=Cookies.get("city");
	if(city!= undefined){
		city=city.charAt(0).toUpperCase()+city.substr(1);
		city=city.replace("+"," ");
		$(".citySelection").val(city);
		$(".citySelectionEvidence, .citySelectionLeftIndex").val("index.php?selectcity="+city);
		$(".citySelectionLeft").val("search.php?findproduct=&selectcity="+city);
	}
	//cambia testo nell'input di ricerca
	$("#userSearch").on("focus", function(){
		if($(this).val()=="Cerca il tuo prodotto..") {
			$(this).val("");
		}
	});
	//in caso di defocus resetta il value della text input
	$("#userSearch").on("focusout", function(){
		if($("#userSearch").val()==""){
			$("#userSearch").val("Cerca il tuo prodotto..");
		}
	});

	//mostrare i menu a tendina dei prodotti sulla sinistra
	$("ul.cat > li > p").on("click", function(){
		var list=$(this).parent().find("ul");//seleziona gli ul dentro ul.cat>li
		//mostra la lista dei prodotti
		if(list.css("height")=="0px"){
			list.css("height", "auto");
		}
		else{
			list.css("height", "0px");
		}
	});

	//miniature immagini prodotto
	$(".miniPictureContainer").on("mouseover", function(){
		$(this).css("border-color","white");
	});
	$(".miniPictureContainer").on("mouseout", function(){
		$(this).css("border-color","#AEAEAE");
	});

	$(".miniPictureContainer").on("click", function(){
		var mainPicture=$("#mainPictureContainer img");
		var pictureToSet=$(this).find("img").attr("src");//path immagine della miniatura cliccata
		mainPicture.attr("src", pictureToSet);
	});

	
	//textarea del feedback
	$("#userComment").on("focus", function(){
		$(this).text("");
	});

	//submit del pagamento con domicilio, dentro il carrello
	$("#payHome").on("click",function(e){
		var ore=$('#selectHour').find(":selected").text();
		var minuti=$('#selectMinute').find(":selected").text();
		if(	ore!="ore" && minuti!="min")
			$(this).trigger("click");
		else{
			e.preventDefault();
			alert("Orario di spedizione non corretto");
		}
	});

	//submit del feedback dentro product.php
	$(".feedbackSubmit").on("click",function(e){
		var voto=$("input[name=feedbackrating]:checked", "#feedbackSend").val();
		var testoFeedback=$("#userComment").val();
		if(	voto!="0" && testoFeedback!="" && testoFeedback!="Inserisci un commento sul prodotto (max 500 caratteri)")
			$(this).trigger("click");
		else{
			e.preventDefault();
			alert("Feedback non valido");
		}
	});

		
});