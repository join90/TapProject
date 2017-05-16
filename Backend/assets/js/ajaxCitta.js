$(document).ready(function(){

		//popoliamo il menu con tutte le città
		$.ajax({
			url : "citta.txt",
			dataType: "text",
			success : function (data){
				var citta = data.split("\n");
				citta.sort();
				for(i=0; i<citta.length; i++){
					$("#cit").html( $("#cit").html()+'<option value="'+citta[i]+'">'+citta[i]+'</option>');
				}
			}
		});

});