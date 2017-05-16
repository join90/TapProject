$(document).ready(function(){
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

	//per mostrare il div per inserire il feedback
	$("#insertComment").on("click", function(){
		var div=$("#feedbackInsertionContainer");
		if(div.css("visibility")=="hidden"){
			div.css({
			"visibility":"visible",
			"height":"auto"
			});
			$(".rating input[type=radio]").attr('disabled', false);
		}
		else{
			div.css({
			"visibility":"hidden",
			"height":"0px"
			});
		}
	});
	
	//textarea del feedback
	$("#userComment").on("focus", function(){
		$(this).text("");
	});

	//valutazione del prodotto
	var productVote=parseFloat($("#productVote").text());
	var radioToSelect;
	if(productVote>4.75)
		radioToSelect="star5";
	else if(productVote>4.25)
		radioToSelect="star4half";
	else if(productVote>3.75)
		radioToSelect="star4";
	else if(productVote>3.25)
		radioToSelect="star3half";
	else if(productVote>2.75)
		radioToSelect="star3";
	else if(productVote>2.25)
		radioToSelect="star2half";
	else if(productVote>1.75)
		radioToSelect="star2";
	else if(productVote>1.25)
		radioToSelect="star1half";
	else
		radioToSelect="star1";

	$(".productRating #"+radioToSelect).prop("checked","true");

	//$(".productRating input:radio").attr("disabled","true");

	//valutazione del negozio
	var shopVote=parseFloat($("#shopVote").text());
	var radioToSelect2;
	if(shopVote>4.75)
			radioToSelect2="star5";
	else if(shopVote>4.25)
			radioToSelect2="star4half";
	else if(shopVote>3.75)
			radioToSelect2="star4";
	else if(shopVote>3.25)
			radioToSelect2="star3half";
	else if(shopVote>2.75)
			radioToSelect2="star3";
	else if(shopVote>2.25)
			radioToSelect2="star2half";
	else if(shopVote>1.75)
			radioToSelect2="star2";
	else if(shopVote>1.25)
			radioToSelect2="star1half";
	else
		radioToSelect2="star1";

	$(".shopRating #"+radioToSelect2).prop("checked","true");
	//su shop.php le stelline hannp un altra classe quindi consideriamo anche queste
	$(".mainShopRating #"+radioToSelect2).prop("checked","true");
	//$(".shopRating input:radio").attr("disabled","true");


});