$(document).ready(function(){

		//popoliamo il menu con tutti i tipi di frutta
		$.ajax({
			url : "frutta.txt",
	        dataType: "text",
	        success : function (data) {
	            var frutta=data.split("\n");
	            frutta.sort(); //ordine lessicografico
	            for(i=0;i<frutta.length;i++){
	                $("#fruit").html( $("#fruit").html()+"<li><a href=''>"+frutta[i]+"</a></li>" );
	            }
	        }
		});
		$.ajax({
			url : "verdura.txt",
	        dataType: "text",
	        success : function (data) {
	            var verdura=data.split("\n");
	            verdura.sort();
	            for(i=0;i<verdura.length;i++){
	                $("#vegetables").html( $("#vegetables").html()+"<li><a href=''>"+verdura[i]+"</a></li>" );
	            }
	        }
		});
		$.ajax({
			url : "fruttasecca.txt",
	        dataType: "text",
	        success : function (data) {
	            var fruttasecca=data.split("\n");
	            fruttasecca.sort();
	            for(i=0;i<fruttasecca.length;i++){
	                $("#driedFruit").html( $("#driedFruit").html()+"<li><a href=''>"+fruttasecca[i]+"</a></li>" );
	            }
	        }
		});
		$.ajax({
			url : "legumi.txt",
	        dataType: "text",
	        success : function (data) {
	            var legumi=data.split("\n");
	            legumi.sort();
	            for(i=0;i<legumi.length;i++){
	                $("#legumes").html( $("#legumes").html()+"<li><a href=''>"+legumi[i]+"</a></li>" );
	            }
	        }
		});
		$.ajax({
			url : "spezie.txt",
	        dataType: "text",
	        success : function (data) {
	            var spezie=data.split("\n");
	            spezie.sort();
	            for(i=0;i<spezie.length;i++){
	                $("#spices").html( $("#spices").html()+"<li><a href=''>"+spezie[i]+"</a></li>" );
	            }
	        }
		});
		$.ajax({
			url : "altro.txt",
	        dataType: "text",
	        success : function (data) {
	            var altro=data.split("\n");
	            altro.sort();
	            for(i=0;i<altro.length;i++){
	                $("#other").html( $("#other").html()+"<li><a href=''>"+altro[i]+"</a></li>" );
	            }
	        }
		});

		$.ajax({
			url : "citta.txt",
			dataType: "text",
			success : function (data){
				var citta = data.split("\n");
				citta.sort();
				for(i=0; i<citta.length; i++){
					$("#cit").html( $("#cit").html()+"<option value='"+citta[i]+"'>"+citta[i]+"</option>");
				}
			}
		});

});