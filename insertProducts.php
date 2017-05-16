<html xmlns="http://www.w3.org/1999/xhtml"><head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Fast&Fruit - rimetti in vendita un prodotto</title>
	<link href="assets/css/style.css" rel="stylesheet" type="text/css" media="screen">
	<link href="assets/css/controlpanelStyle.css" rel="stylesheet" type="text/css" media="screen">
	<link href="assets/css/insertProductStyle.css" rel="stylesheet" type="text/css" media="screen">
	<script src="assets/js/jquery.js"></script>
	<script src="assets/js/js.cookie.js"></script>
	<script src="assets/js/main.js"></script>
		<script src="assets/js/insertProduct.js"></script>
	</head>

	<body>
		<?php
			session_start();
			require 'connect.php';
			
			if(isset($_GET['oldProduct'])){ //faccio la query di un prodotto vecchio per rimetterlo in vendita!
				
				$queryFetch = "SELECT p.id, p.titolo, p.categoria, p.marchio, p.provenienza, p.prezzo, p.pezzatura, p.quantUnita, p.disponibilita, p.maturazione, p.tipoAgricoltura, p.km0, p.descrizione, p.tipo  FROM prodotti p where p.id=".$_GET['oldProduct'].";";

				$resultFetch = mysqli_query($conn,$queryFetch) or die("Error with MySQL Query: ".mysqli_error($conn));

				$queryImage = "SELECT i.nomefile from immagini i where i.prodotto = ".$_GET['oldProduct'].";";

				$resultFetchImg = mysqli_query($conn,$queryImage) or die("Error with MySQL Query: ".mysqli_error($conn));
				
			}
			

			if(isset($_POST['sendProduct'])) { //inserisci un nuovo prodotto

				$titolo = NULL; $tipoProdotto = NULL; $categoria = NULL; $marchio = "NULL"; $provenienza = "NULL";
				$prezzo = "0"; $disponibilita = "0"; $pezzatura = "NULL"; $maturazione = "NULL"; 
				$km0 = "0"; $descrizione = "NULL"; 

				if($_POST['Titolo'] != "Es:PomodoroDatterino IGP")						
					$titolo = "'".htmlentities($_POST['Titolo'],ENT_QUOTES)."'";
				
				if(isset($_POST['tipoProdotto'])){
					if(($_POST['tipoProdotto'] != "") && ($_POST['Categoria'] != "") ){
						$tipoProdotto = trim($_POST['tipoProdotto']);
						$categoria = trim($_POST['Categoria']);
						$tipoProdotto = str_replace("\n", "", $tipoProdotto);
						$categoria = str_replace("\n", "", $categoria);
						
						$tipoProdotto = "'".htmlentities($tipoProdotto,ENT_QUOTES)."'";
						$categoria = "'".htmlentities($categoria,ENT_QUOTES)."'";
						
					}
				}

				if($_POST['Marchio'] != "Es:Valfrutta")
					$marchio = "'".htmlentities($_POST['Marchio'],ENT_QUOTES)."'";

				if($_POST['Provenienza'] != "Es:Pachino")
					$provenienza = "'".htmlentities($_POST['Provenienza'],ENT_QUOTES)."'";

				if($_POST['Prezzo'] != "")
					$prezzo = str_replace(",", ".", $_POST['Prezzo']); //sostituisco la virgola col punto per essere inserito correttamente nel db	
				if(isset($_POST['disponibilita'])){
					if($_POST['disponibilita'] != "")
						$disponibilita = $_POST['disponibilita'];
				}
				if(isset($_POST['pezzatura'])){
					if($_POST['pezzatura'] != "")
						$pezzatura = $_POST['pezzatura'];
				}
				if(isset($_POST['maturazione'])){
					if($_POST['Maturazionep']!= "")
						$maturazione = $_POST['Maturazionep'];
				}

				if(isset($_POST['km0']))
					$km0 = $_POST['km0'];

				if(($_POST['descrizione'] != "Descrivi il tuo prodotto..") && ($_POST['descrizione'] != "")) 
					$descrizione = "'".htmlentities($_POST['descrizione'],ENT_QUOTES)."'";
					
				$quantUnita = "'".htmlentities($_POST['QuantUnita'].$_POST['Tipo'],ENT_QUOTES)."'";

				$datetime = date_create()->format('Y-m-d H:i:s');

				$allowed_types = array("image/jpeg","image/png"); //array di formati per il controllo dei file

				if($_POST['sendProduct'] == "Inserisci"){
					
					$query = "INSERT INTO `dbfastandfruits`.`prodotti` (`titolo`, `categoria`, `marchio`, `provenienza`, `prezzo`, `pezzatura`, `quantUnita`, `disponibilita`, `dataora`, `maturazione`, `tipoAgricoltura`, `km0`, `negozio`, `presente`, `descrizione`, `tipo`) VALUES (".$titolo.", ".$categoria.", ".$marchio.", ".$provenienza.", ".$prezzo.", ".$pezzatura.", ".$quantUnita.", ".$disponibilita.", '".$datetime."', ".$maturazione.", ".$_POST['TipoAgricoltura'].", ".$km0.", ".$_SESSION['idseller'].", 1, ".$descrizione.", ".$tipoProdotto.");";

					if( ($_FILES['file0']['name'] != "") ){ 
						
						//----------------------mi controllo il formato del file---------------///
						
						if(in_array($_FILES['file0']['type'], $allowed_types)){  //Se l'immagine è corretta
							
							$success = true;  //controllo le altre immagini
							for($i=1; $i<3; $i++){
								if($_FILES['file'.$i]['name'] != ""){
									if( !(in_array($_FILES['file'.$i]['type'], $allowed_types)) ){
										$success = false;
										$i = 3;
									}
								} 				
							}		

							if($success == true){	
								
								$result = mysqli_query($conn,$query) or die("Error with MySQL Query: ".mysqli_error($conn)); //inserisco il prodotto
								
								$lastIDProduct = mysqli_insert_id($conn);
								$toFileMain = "prodotti/".$lastIDProduct.$_FILES['file0']['name'];   
								$imageMain = "'".$lastIDProduct.$_FILES['file0']['name']."'";
								move_uploaded_file($_FILES['file0']['tmp_name'], $toFileMain);

								$queryImg = "INSERT INTO immagini (nomefile,principale,prodotto) VALUES (".$imageMain.",1,".$lastIDProduct.");";
								$resultImg = mysqli_query($conn,$queryImg) or die("Error with MySQL Query: ".mysqli_error($conn));

								for($i=1; $i<3; $i++){
									if($_FILES['file'.$i]['name'] != ""){
										$toFileSecond = "prodotti/".$lastIDProduct.$_FILES['file'.$i]['name'];
										move_uploaded_file($_FILES['file'.$i]['tmp_name'], $toFileSecond);	
										$imageSecond = "'".$lastIDProduct.$_FILES['file'.$i]['name']."'";
										$queryImg = "INSERT INTO immagini (nomefile,principale,prodotto) VALUES (".$imageSecond.",0,".$lastIDProduct.");";
										$resultImg = mysqli_query($conn,$queryImg) or die("Error with MySQL Query: ".mysqli_error($conn));
									}
								}

								exit("Prodotto inserito correttamente! <a href=\"controlpanel.php\">Torna al pannello di controllo</a>");								
							}
							
							else
								exit("Errore nel formato dei file! Caricare i file solo con estensione .jpeg o .png <a href=\"insertproducts.php\">Torna indietro</a>");
						}

						else
							exit("Errore nel formato dei file! Caricare i file solo con estensione .jpeg o .png <a href=\"insertproducts.php\">Torna indietro</a>");			

					}
					else
						exit("Devi inserire l'immagine principale del prodotto! <a href=\"insertproducts.php\">Torna indietro</a>");
					
				}
					
					
				else {

					
					$query = "UPDATE prodotti set titolo = ".$titolo.", categoria = ".$categoria.", marchio = ".$marchio.", provenienza = ".$provenienza.", prezzo = ".$prezzo.", pezzatura = ".$pezzatura.", disponibilita = ".$disponibilita.", dataora = '".$datetime."', maturazione = ".$maturazione.", tipoAgricoltura = ".$_POST['TipoAgricoltura'].", km0 = ".$km0.", negozio = ".$_SESSION['idseller'].", presente = 1, descrizione = ".$descrizione.", tipo = ".$tipoProdotto." where prodotti.id = ".$_POST['oldProduct'].";";
					
					
					$success = true;  //controllo se ci sono immagini caricate e nel caso controllare il formato
					$file = "";
					for($i=0; $i<3; $i++){
						if($_FILES['file'.$i]['name'] != ""){
							if( !(in_array($_FILES['file'.$i]['type'], $allowed_types)) ){
								$success = false;
								$i = 3;
							}
							else
								$file .= $_FILES['file'.$i]['name'].$i.","; 
						} 				
					}
					
					$file = substr($file,0,-1);	//tolgo l'ultimo carattere ","
					
					if(($success == true) && ($file != "")){ //se non ci sono errori nei formati dei file and se ci sono i file che l'utente vuole inserire
						
						$result = mysqli_query($conn,$query) or die("Error with MySQL Query: ".mysqli_error($conn)); //eseguo l'update del prodotto
						$queryImg;
						$subfile = explode(",", $file); //mi creo l'array dei file che l'utente ha caricato
						$primaInserita = false;
						$imageUpdate; //variabile per la query 
						$toFile; // file di destinazione

						for($i=0; $i<count($subfile); $i++){
							
							$product = substr($subfile[$i], 0,-1); //prendo il nome del file "Es: logo.jpg"
							
							
							//-----------------controllo se l'utente sta reinserendo lo stesso tipo di file "es. 34logo.jpg". Se è cosi viene tolto il 34 e inserito un altro id del prodotto. Questo passaggio viene effettuato per evitare che l'immagine vada in conflitto. -------//
							
							if(!is_numeric(substr($product, 0,1))){ 	
								$toFile = "prodotti/".$_POST['oldProduct'].$product; //output del file
								$imageUpdate = "'".$_POST['oldProduct'].$product."'"; //il nome dell'immagine da inserire nella query
							}
							else{
								$toFile = "prodotti/".$product;
								$imageUpdate = "'".$product."'"; //il nome dell'immagine da inserire nella query
							}
							
							//----------------------mi sposto le immagini che inserisco nella cartella------//
							
							$tipoQuery = substr($subfile[$i], -1); //prendo il carattere finale della stringa "es: logo.jpg0" per distinguere il tipo di immagine 
							
							if($tipoQuery == "0") //controllo se l'immagine che stiamo considerando è quella principale o secondaria
								move_uploaded_file($_FILES['file0']['tmp_name'], $toFile); //sposto il file da una dir temporanea alla destinazione scelta
							elseif ($tipoQuery == "1") 
								move_uploaded_file($_FILES['file1']['tmp_name'], $toFile);
							elseif ($tipoQuery == "2") 
								move_uploaded_file($_FILES['file2']['tmp_name'], $toFile);
									
							
							if(intval($tipoQuery) == 0){ //se c'è quella principale

								$queryImg = "DELETE from immagini where prodotto = ".$_POST['oldProduct'].";";
								$resultImg = mysqli_query($conn,$queryImg) or die("Error with MySQL Query: ".mysqli_error($conn));
								
								$queryImg = "INSERT INTO immagini (nomefile,principale,prodotto) VALUES (".$imageUpdate.",1,".$_POST['oldProduct'].");";
								$resultImg = mysqli_query($conn,$queryImg) or die("Error with MySQL Query: ".mysqli_error($conn));
								
								$primaInserita = true; //per sapere se anche le altre immagine devono essere inserite 
							}
							else{
								if($primaInserita == true){
									
									$queryImg = "INSERT INTO immagini (nomefile,principale,prodotto) VALUES (".$imageUpdate.",0,".$_POST['oldProduct'].");";
									$resultImg = mysqli_query($conn,$queryImg) or die("Error with MySQL Query: ".mysqli_error($conn));	
								}
								else
									exit("Devi caricare l'immagine principale del prodotto!<a href=\"insertproducts.php?oldProduct=".$_POST['oldProduct']."\">Torna indietro</a>");
							}
											
						}

					}
					elseif ($success == true)  //siamo nel caso in cui l'utente non sta aggiornando immagini
						$result = mysqli_query($conn,$query) or die("Error with MySQL Query: ".mysqli_error($conn));
						
					else
						exit("Errore nel formato dei file! Caricare i file solo con estensione .jpeg o .png <a href=\"insertproducts.php?oldProduct=".$_POST['oldProduct']."\">Torna indietro</a>");

					
					exit("Prodotto aggiornato correttamente! <a href=\"controlpanel.php\">Torna al pannello di controllo</a>");
				
				}
																		
			}

		?>

		<div class="main">
			<div class="header">
				<div class="logo">
					<a href="index.php">  
						<img src="assets/images/logo.jpg">
					</a>
				</div>
				<div class="CPcenter">
					<div id="CPimgShopContainer">
						<img src=
							<?php
								$queryNegozio = "SELECT n.nome, n.imgProfilo from negozi n where n.id=".$_SESSION['idseller'].";";
								$resultNegozio = mysqli_query($conn,$queryNegozio) or die("Error with MySQL Query: ".mysqli_error($conn));
								$arrayNegozio = mysqli_fetch_row($resultNegozio);

								if($arrayNegozio[1] != "NULL")
									echo '"negozi/'.$arrayNegozio[1].'"';	
							?>

						>
					</div>
					<div id="CPshopName"><?php echo $arrayNegozio[0]; ?></div>
				</div>

				<div class="CPright">
					<a href=""><p class="newInsertionButton"><br>Vendi un nuovo prodotto</p></a>
					<p>Oppure</p>
					<a href=""><p class="newInsertionButton"><br>Rimettine uno in vendita</p></a>
				</div>
			</div>
			
			<div id="CPpage">

				<div id="coreTitle">Metti in vendita il prodotto</div>
				<div id="insert">
		
		<form method="post" action="insertProducts.php" name="insertProduct" enctype="multipart/form-data">
			<div class="elemento">
				<div class="title">Titolo prodotto</div>
				<?php
					if(isset($_GET['oldProduct']))
						echo '<input type="hidden" value="'.$_GET['oldProduct'].'" name="oldProduct"></input>';
				?>

				<input type="text" name="Titolo" id="titolo" maxlength="40" value=
					<?php
						if(isset($_GET['oldProduct'])){
							$arrayFetch = mysqli_fetch_row($resultFetch);
							echo '"'.$arrayFetch[1].'"';
						}
						else
							echo '"Es:Pomodoro Datterino IGP"';

					?>

				></input>
			</div>

			<div class="elemento">
				<div class="title">Tipo prodotto</div>
				<select id="productType" class="selectelement" name="tipoProdotto">
					<?php
					  if (isset($_GET['oldProduct']))
					  	echo '<option value="'.$arrayFetch[13].'" selected >'.$arrayFetch[13].'</option>';
					  else{	 
					?>
					<option value="0" selected disabled>-</option>
					<?php } ?>
					<option value="frutta">Frutta</option>
					<option value="verdura">Verdura</option>
					<option value="frutta secca">Frutta secca</option>
					<option value="legumi">Legumi</option>
					<option value="spezie">Spezie/aromi</option>
					<option value="altro">Altro</option>
				</select>
			</div>

			<div class="elemento">
				<div class="title">Categoria prodotto</div>
				<select id="categoria" class="selectelement" name="Categoria">
					<?php
					  if (isset($_GET['oldProduct']))
					  	echo '<option value="'.$arrayFetch[2].'" selected >'.$arrayFetch[2].'</option>';
					  else
					  {	 
					?>
					<option value="0" selected disabled>-</option>
					<?php } ?>
				</select>
			</div>
			
			<div class="elemento">
				<div class="title">Marchio Prodotto</div>
				<input type="text" name="Marchio" id="marchio"
					<?php
						if(isset($_GET['oldProduct'])){
							if($arrayFetch[3] != "NULL")
								echo 'value="'.$arrayFetch[3].'" maxlength="20"></input>';
							else
								echo 'value="Es:Valfrutta" maxlength="20"></input>';								
						}
						else{
					?>

				 value="Es:Valfrutta" maxlength="20"></input>
				 <?php } ?>
			</div>
			
			<div class="elemento">
				<div class="title">Provenienza</div>
				<input type="text" name="Provenienza" id="provenienza"
					<?php
						if(isset($_GET['oldProduct'])){
							if($arrayFetch[4] != "NULL")
								echo 'value="'.$arrayFetch[4].'" maxlength="20"></input>';
							else
								echo 'value="Es:Pachino" maxlength="20"></input>';								
						}
						else{
					?>

				 value="Es:Pachino" maxlength="20"></input>
				<?php } ?>
			</div>
			
			<div class="elementoprezqnt">
				<div class="elementprezzo">
					<div class="title">Prezzo Prodotto</div>
					<?php
						if(isset($_GET['oldProduct'])){
							echo '<input type="text" name="Prezzo" value="'.str_replace(".", ",", $arrayFetch[5]).'"></input>'; 
						}
						else{
					?>
					<input type="text" name="Prezzo"></input>
					<?php } ?>
				</div>
				<div class="elementprezzo">
					<div class="tit">&euro; per </div>
					<?php
						if(isset($_GET['oldProduct'])){
							echo '<input type="text" name="QuantUnita" value="'.substr($arrayFetch[7],0,-2).'"></input>';
						}
						else{
					?>
					<input type="text" name="QuantUnita" value="1"></input>
					<?php } ?>
				</div>
			</div>
			
			<div class="elementoprezqnt">
				<select id="tipo" name="Tipo">
					<?php
						if(isset($_GET['oldProduct'])){
							echo '<option value="'.substr($arrayFetch[7],-2,2).'" selected >'.substr($arrayFetch[7], -2,2).'</option>';
						}
						else{
					?>
					<option value="Kg" selected >Kg</option>
	  				
	  				<?php } ?>
	  				<option value="Pz">Pz</option>
				</select>
			</div>

			<div class="elemento">
				<div class="title">Disponibilità</div>
				<select class="selectelement" name="disponibilita">
					<?php
						if(isset($_GET['oldProduct'])){
							if($arrayFetch[8] == "1")
								echo '<option value="1" selected>Bassa</option>';
							elseif($arrayFetch[8] == "2")
								echo '<option value="2" selected>Media</option>';
							else
								echo '<option value="3" selected>Alta</option>';
						}
						else{

					?>
					<option value="0" selected disabled>-</option>
					<?php } ?>
	  				<option value="1">Bassa</option>
					<option value="2">Media</option>
					<option value="3">Alta</option>
				</select>
			</div>

			<div class="elemento">
				<div class="title">Pezzatura Prodotto</div>
				<select class="selectelement" name="pezzatura">
					<?php
						if(isset($_GET['oldProduct'])){
							if($arrayFetch[6] == "1")
								echo '<option value="1" selected>Piccola</option>';
							elseif($arrayFetch[6] == "2")
								echo '<option value="2" selected>Media</option>';
							else
								echo '<option value="3" selected>Grande</option>';
						}
						else{

					?>
					<option value="0" selected disabled>-</option>
					<?php } ?>
	  				<option value="1">Piccola</option>
					<option value="2">Media</option>
					<option value="3">Grande</option>
				</select>
			</div>
			<div class="elemento">
				<div class="title">Maturazione</div>
				<select class="selectelement" name="Maturazionep">
					<?php
						if(isset($_GET['oldProduct'])){
							if($arrayFetch[9] == "1")
								echo '<option value="1" selected>Meno maturo</option>';
							elseif($arrayFetch[9] == "2")
								echo '<option value="2" selected>Maturazione media</option>';
							else
								echo '<option value="3" selected>Più maturo</option>';
						}
						else{

					?>
  					<option value="0" selected disabled>-</option>
  					<?php } ?>
	  				<option value="1">Meno maturo</option>
					<option value="2">Maturazione media</option>
					<option value="3">Più maturo</option>
				</select> 
			</div>
			<div class="elemento">	
				<div class="title">Agricoltura</div>
				<select class="selectelement" name="TipoAgricoltura">
					<?php
						if(isset($_GET['oldProduct'])){
							if($arrayFetch[10] == "1")
								echo '<option value="1" selected>normale</option>';
							elseif($arrayFetch[10] == "2")
								echo '<option value="2" selected>biologica</option>';
						}
						else{

					?>
					<option value="0" selected >normale</option>
					<?php } ?>
	  				<option value="1">biologica</option>
				</select>
			</div>
			
			<div class="elementocheck">
				<div class="title">Km0</div>
				 <input name="km0" type="checkbox" value="1"
				 		<?php
				 			if(isset($_GET['oldProduct'])){
				 				if($arrayFetch[11] == "1")
				 					echo 'checked="checked"';
				 			}
				 		?>
				 />
			</div>

			<div class="elemento">
				<div class="title">Descrizione</div>
				<?php
					if(isset($_GET['oldProduct']))
						echo '<textarea rows="12" cols="70" id="descrizione" name="descrizione">'.$arrayFetch[12].'</textarea>';
					
					else{

				?>
				<textarea rows="12" cols="70" id="descrizione" name="descrizione">Descrivi il tuo prodotto..</textarea>
				<?php } ?>
			</div>
			<div id="elementoImg">
				<div class="title">Carica le immagini del prodotto</div>
				<div id="imageMain">
					<div class="title">Immagine principale</div>
					<input type="file" name="file0"></input>
					<?php
						if(isset($_GET['oldProduct'])){
							if(($arrayFetchImg = mysqli_fetch_row($resultFetchImg)) != NULL)
								echo '<div class="boxImage"><img src="prodotti/'.$arrayFetchImg[0].'"></div>';
						}
					?>
				</div>
				<div class="imageSecond">
					<div class="title">Seconda immagine</div>
					<input type="file" name="file1"></input>
					<?php
						if(isset($_GET['oldProduct'])){
							if(($arrayFetchImg = mysqli_fetch_row($resultFetchImg)) != NULL)
								echo '<div class="boxImage"><img src="prodotti/'.$arrayFetchImg[0].'"></div>';
						}
					?>
				</div>

				<div class="imageSecond">
					<div class="title">Terza immagine</div>
					<input type="file" name="file2"></input>
					<?php
						if(isset($_GET['oldProduct'])){
							if(($arrayFetchImg = mysqli_fetch_row($resultFetchImg)) != NULL)
								echo '<div class="boxImage"><img src="prodotti/'.$arrayFetchImg[0].'"></div>';
						}
					?>
				</div>
			</div>
			<div class="sendproduct">
				<input name="sendProduct" type="submit" 
						<?php
							if(isset($_GET['oldProduct']))
								echo 'value="Pubblica"';
							else
								echo 'value="Inserisci"';

						?>
				 class="sendProductButton"/>
			</div>
		</form>
		</div>
		</div>
		</div>
		<div class="footer">
			<p>Fast&Fruit. Copyright &#169; 2016. Tutti i diritti riservati. fastandfruit.it</p>	
		</div>
	</body>
</html>