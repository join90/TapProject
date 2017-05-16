<!DOCTYPE html>
<html>
<head>
	<title>Inserisci Prodotto</title>
	<link href="assets/css/InsertProductStyle.css" rel="stylesheet" type="text/css" media="screen">
	<script src="assets/js/jquery.js"></script>
	<script src="assets/js/insertProduct.js"></script>

</head>
<body>
	<div id="insert">
		<form method="get" action="" name="insertProduct">
			<div class="elemento">
				<div class="title">Titolo prodotto</div>
				<input type="text" name="Titolo" id="titolo" maxlength="40" value="Es:pomodoro datterino IGP"></input>
			</div>
			
			<div class="elemento">
				<div class="title">Categoria prodotto</div>
				<select class="selectelement" name="categoria">
					<option value="0" selected disabled>-</option>
					<?php
						require 'connect.php';
						$query = "SELECT distinct p.categoria FROM prodotti p;";							
						$result = mysqli_query($conn,$query) or die ("Error: ".mysqli_error($conn));
						if(mysqli_num_rows($result) > 0){
							while ($array = mysqli_fetch_row($result)) {
								foreach ($array as $p) {
				 					echo '<option value="'.$p.'">'.$p.'</option>'; 
				 				}	
							}
						}
					?>
				</select>
			</div>
			
			<div class="elemento">
				<div class="title">Tipo</div>
				<input type="text" name="Tipo" maxlength="20"></input>
			</div>	
			<div class="elemento">
				<div class="title">Marchio Prodotto</div>
				<input type="text" name="Marchio" id="marchio" value="Es:valfrutta" maxlength="20"></input>
			</div>
			
			<div class="elemento">
				<div class="title">Provenienza</div>
				<input type="text" name="Provenienza" id="provenienza" value="Es:Pachino" maxlength="20"></input>
			</div>
			
			<div class="elementoprezqnt">
				<div class="elementprezzo">
					<div class="title">Prezzo Prodotto</div>
					<input type="text" name="Prezzo"></input>
				</div>
				<div class="elementprezzo">
					<div class="tit">€ per </div>
					<input type="text" name="QuantUnita" value="1"></input>
				</div>
			</div>
			
			<div class="elementoprezqnt">
				<select id="tipo">
					<option value="0" selected >Kg</option>
	  				<option value="1">Pz</option>
				</select>
			</div>

			<div class="elemento">
				<div class="title">Disponibilità</div>
				<select class="selectelement">
					<option value="0" selected disabled>-</option>
	  				<option value="1">Bassa</option>
					<option value="2">Media</option>
					<option value="3">Alta</option>
				</select>
			</div>

			<div class="elemento">
				<div class="title">Pezzatura Prodotto</div>
				<select class="selectelement">
					<option value="0" selected disabled>-</option>
	  				<option value="1">Piccola</option>
					<option value="2">Media</option>
					<option value="3">Grande</option>
				</select>
			</div>
			<div class="elemento">
				<div class="title">Maturazione</div>
				<select class="selectelement" name="Maturazionep">
  					<option value="0" selected disabled>-</option>
	  				<option value="1">Meno maturo</option>
					<option value="2">Maturazione media</option>
					<option value="3">Più maturo</option>
				</select> 
			</div>
			<div class="elemento">	
				<div class="title">Agricoltura</div>
				<select class="selectelement" name="TipoAgricoltura">
					<option value="0" selected >normale</option>
	  				<option value="1">biologica</option>
					<option value="2">integrata</option>
				</select>
			</div>
			
			<div class="elementocheck">
				<div class="title">Km0</div>
				 <input name="km0" type="checkbox" value="value"/>
			</div>

			<div class="elemento">
				<div class="title">Descrizione</div>
				<textarea rows="12" cols="35" id="descrizione">Descrivi il tuo prodotto..</textarea>
			</div>
			<div class="sendproduct">
				<input name="sendProduct" type="submit" value="Inserisci"/>
			</div>
		</form>
	</div>
</body>
</html>