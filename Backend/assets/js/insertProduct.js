$(document).ready(function(){

	$("#titolo").on("focus",function(){
		if ($(this).val() == "Es:Pomodoro Datterino IGP") {
			$("#titolo").val("");
		}
	});

	$("#marchio").on("focus",function(){
		if ($(this).val() == "Es:Valfrutta") {
			$("#marchio").val("");
		}
	});


	$("#provenienza").on("focus",function(){
		if ($(this).val() == "Es:Pachino") {
			$("#provenienza").val("");
		}
	});

	$("#descrizione").on("focus",function(){
		if ($(this).text() == "Descrivi il tuo prodotto..") {
			$("#descrizione").text("");
		}
	});

	//popolamento del menu "categoria" in base al tipo di prodotto selezionato es. frutta, verdura, ecc. 
	$("#productType").change(function(){
		var selection=$(this).find(":selected").text().toLowerCase(); //prendo il testo dell'elemento selezionato del select (tutto minuscolo)
		if(selection== "spezie/aromi"){ //viene fatto perchè il file non si può chiamare "spezie/aromi"
			selection="spezie";
		}
		else if(selection== "frutta secca"){
			selection="fruttasecca";
		}
		$("#categoria").html("<option value=\"0\" selected disabled>-</option>"); //resetto le opzioni dell'elemento categoria poichè per ogni tipo di prodotto selezionato questa lista va resettata (a meno dell'opzione di default "-")
		$.ajax({
			url : selection+".txt", //frutta.txt, verdura.txt, ecc.
	        dataType: "text",
	        success : function (data) {
	            var lista=data.split("\n");
	            lista.sort(); //ordine lessicografico
	            for(i=0;i<lista.length;i++){
	                $("#categoria").html( $("#categoria").html()+"<option value=\" "+lista[i].toLowerCase()+" \">" +lista[i]+ "</option>"); //aggiungo le opzioni all'elemento categoria
	            }
	        }
		});
	});

}); 