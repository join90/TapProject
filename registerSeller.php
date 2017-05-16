<html xmlns="http://www.w3.org/1999/xhtml"><head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Fast&Fruit</title>
	<link href="assets/css/registerStyle.css" rel="stylesheet" type="text/css" media="screen">
	<script src="assets/js/js.cookie.js"></script>
	<script src="assets/js/jquery.js"></script>
	<script src="assets/js/main.js"></script>
	<script src="assets/js/ajaxCitta.js"></script>
	</head>
	<body>
		<?php
			session_start();
			require 'connect.php';

			if(isset($_POST['inviadati'])){
	
				$domicilio = "0"; $costoDomicilio = "0"; $cellulare = "NULL"; 
				$Via = NULL; 
				$telefono = NULL; 
				$numeroc = NULL; 
				$nome = NULL; 
				$city = NULL;
	
				if($_POST['telefono'] != ""){	
					
					$cerca = array("-"," ","/");
					$telefono = str_replace($cerca, "", $_POST['telefono']); //cerca e sostituisce i caratteri presenti nell'array in  spazi vuoti

					if (is_numeric($telefono)) {
						$telefono = "'".$_POST['telefono']."'";	
					}
					else
						$telefono = NULL;
				}

				if(($_POST['indirizzo'] != "") && ($_POST['numerocivico']!= "") && ($_POST['nome']!= "") && ($_POST['city']!= "") ){
					
					$Via = "'".htmlentities($_POST['tipoVia']." ".$_POST['indirizzo'],ENT_QUOTES)."'";
					$numeroc = "'".htmlentities($_POST['numerocivico'],ENT_QUOTES)."'";
					$nome = "'".htmlentities($_POST['nome'],ENT_QUOTES)."'";
					
					$city = trim($_POST['city']); //elimino gli spazi di inizio e fine
					$city = htmlentities($city, ENT_QUOTES); //conversione in entità html
					$city = "'".str_replace("\n","",$city)."'"; //elimino anche i backslash se ce ne sono (PER SICUREZZA!)
				}
						
							
				
				if($_POST['cellulare'] != ""){	
					
					$cerca = array("-"," ","/");
					$cellulare = str_replace($cerca, "", $_POST['cellulare']);

					if (is_numeric($cellulare)) {
						
						$cellulare = "'".$_POST['cellulare']."'";	
					}
					else
						$cellulare = "NULL";
				}
				
				if(isset($_POST['domicilio'])){  //se il domicilio è stato selezionato
					$domicilio = "1";
					$costoDomicilio = $_POST['costoDomicilio'];	
				}
				//-----------------------mi prendo i giorni della settimana---------------------//

				$strGiorni = "";
				for($i=0; $i<7; $i++){
					if(isset($_POST['g'.$i])){
						$strGiorni .= $_POST['g'.$i].'-';
					}
					if($i == 6){
						if($strGiorni != "")
							$strGiorni = "'".substr($strGiorni, 0,-1)."'"; //tolgo il trattino finale
						else
							$strGiorni = NULL;
					}
				}


				//---------------------mi prendo le fasce orarie--------------------------------------//
				
				$strOrarioApertura = NULL;
	
				$oraIM =  $_POST['o0'].':'.$_POST['o1'];
				$oraFM =  $_POST['o2'].':'.$_POST['o3'];
				$oraIP =  $_POST['o4'].':'.$_POST['o5'];
				$oraFP =  $_POST['o6'].':'.$_POST['o7'];

				$strOrarioM = array($_POST['o0'],$_POST['o1'],$_POST['o2'],$_POST['o3']);
				$strOrarioP = array($_POST['o4'],$_POST['o5'],$_POST['o6'],$_POST['o7']);
				
				
				if( (!in_array('c', $strOrarioM)) && (!in_array('c', $strOrarioP)) )
					
					$strOrarioApertura = "'da ".$oraIM." a ".$oraFM."/da ".$oraIP." a ".$oraFP."'";
					
								
				elseif( !in_array("c", $strOrarioM) )

					$strOrarioApertura = "'da ".$oraIM." a ".$oraFM."'";

				elseif( !(in_array("c", $strOrarioP)) )
					
					$strOrarioApertura = "'da ".$oraIP." a ".$oraFP."'";
				
				
				
				//----------------------controllo email-------------------////////			
				                  	
				if(preg_match("/^[A-Za-z0-9-_.+%]+@[A-Za-z0-9-.]+.[A-Za-z]{2,4}$/", $_POST['email'])){
					
					if(($_POST['password'] != "") && ($_POST['rpassword'] != "")){
						
						if(strlen($_POST['password']) >= 8){
							if($_POST['password'] == $_POST['rpassword']){
								
								$email = "'".htmlentities($_POST['email'],ENT_QUOTES)."'";
								$password = "'".htmlentities($_POST['password'],ENT_QUOTES)."'";

								$queryEmailC = "SELECT c.email, c.pass from clienti c where c.email = ".$email.";";
								$queryEmailN = "SELECT n.email, n.pass from negozi n where n.email = ".$email.";";
								$resultEmailC = mysqli_query($conn,$queryEmailC) or die("Error with MySQL Query: ".mysqli_error($conn));
								$resultEmailN = mysqli_query($conn,$queryEmailN) or die("Error with MySQL Query: ".mysqli_error($conn));
								
								if(!isset($_SESSION['idseller'])){

									if((mysqli_num_rows($resultEmailC) == 0) && (mysqli_num_rows($resultEmailN) == 0) ){ //se non c'è la stessa email nel db
										
										$query = "INSERT into negozi (nome,citta,viaPiazza,ncivico,telefono,cellulare,giorniSettimanaApertura,orariApertura,domicilio,costoDomicilio,valutazione,email,pass,presente) values (".$nome.",".$city.",".$Via.",".$numeroc.",".$telefono.",".$cellulare.",".$strGiorni.",".$strOrarioApertura.",".$domicilio.",".$costoDomicilio.",0,".$email.",".$password.",1);";
										
										$result = mysqli_query($conn,$query) or die("Error with MySQL Query: ".mysqli_error($conn));

										header("Location: success.php");

									}
									
									else
										exit("Email gia' esistente!");	
								}
								
								else{

									$success = false;
									
									if($_POST['oldEmail'] == $_POST['email']) //controllo se l'email è mia, nel caso aggiorno
										$success = true;

									$query = "UPDATE negozi set nome = ".$nome.", citta = ".$city.", viaPiazza = ".$Via.", ncivico = ".$numeroc.", telefono = ".$telefono.", cellulare = ".$cellulare.", giorniSettimanaApertura = ".$strGiorni.", orariApertura = ".$strOrarioApertura.", domicilio = ".$domicilio.", costoDomicilio = ".$costoDomicilio.", email = ".$email.", pass = ".$password." where negozi.id = ".$_SESSION['idseller'].";";
									
									if(($success == true) || ((mysqli_num_rows($resultEmailC) == 0) && (mysqli_num_rows($resultEmailN) == 0))){ //sono io, mi aggiorno
										$result = mysqli_query($conn,$query) or die("Error with MySQL Query: ".mysqli_error($conn));
										header("Location: controlpanel.php");
									}	
									else
										exit("Email gia' esistente!");		

								}
								
							}
							else
								echo "Ridigita correttamente la password!";	
						}
						else
							echo "La password deve essere di almeno 8 caratteri!";

							
					}
					else
						echo "Devi inserire la password!";
				
				}
				else
					echo "Parametri email non corretti!";
				
			}

			?>

		<div class="main">
			<div class="header">
				<div class="logo">
					<a href="index.php">  
						<img src="assets/images/logo.jpg">
					</a>
				</div>		
			</div>
			<div class="downBar">
				<p>Frutta e verdura della migliore qualità da più di 1000 negozi in tutta Italia. Accedi subito!</p>
			</div>
			<p id="resultTitle">Compila i dati</p>
			<form method="post" action="" name="Login">
				<div id="login">
					<div class="log">
						<div class="title">Nome</div>
						<input type="text" name="nome"
							<?php
								if(isset($_SESSION['idseller'])){
									$queryR = "select * from negozi where id = ".$_SESSION['idseller'].";";
									$resultR = mysqli_query($conn,$queryR);
									$arrayR = mysqli_fetch_row($resultR);
									echo "value='".$arrayR[1]."'";
								}
							?>
						></input>
					</div>	
					<div class="log">
						<div class="title">Città</div>
						<select class="selectelement" id="cit" name="city">
							<?php
								if (isset($_SESSION['idseller']))
									if ($arrayR[2] != "NULL") 
										echo '<option value="'.$arrayR[2].'" selected>'.$arrayR[2].'</option>'; 	
									else 
										echo '<option value="0" selected disabled>-</option>';
								else
									echo '<option value="0" selected disabled>-</option>';
							?>
							
						</select>
					</div>
					<div class="address">
						<div class="indirizzo">
							<div class="title">Indirizzo</div>
							<select class="selectelement" name="tipoVia">
								<?php
									$via = NULL;
									if (isset($_SESSION['idseller'])){
										if ($arrayR[3] != "NULL"){
											$via = explode(" ", $arrayR[3]);
											echo '<option value="'.$via[0].'" selected>'.$via[0].'</option>';
											?>
											<option value="Via">Via</option>
											<option value="P.zza">P.zza</option>
											<option value="V.le">V.le</option>
											<option value="C.da">C.da</option>
											<?php
										}
									}
									
									else
									
									{			
								?>
								<option value="0" selected disabled>-</option>
	  							<option value="Via">Via</option>
								<option value="P.zza">P.zza</option>
								<option value="V.le">V.le</option>
								<option value="C.da">C.da</option>
								<?php

							}

							?>
							</select>							
							<input type="text" name="indirizzo"
								<?php
									if (isset($_SESSION['idseller'])){
										if ($arrayR[3] != "NULL"){
											$straddress = "";
											for($i=1; $i<count($via); $i++)
												$straddress = $straddress.$via[$i]." ";
											echo 'value="'.$straddress.'"';
										}
									}

								?>

							></input>
						</div>
						<div class="numeroc">	
							<div class="titlen">N°</div>										
							<input  type="text" name="numerocivico"
								<?php
									if (isset($_SESSION['idseller'])){
										if ($arrayR[4] != "NULL"){
											echo 'value= "'.$arrayR[4].'"';
										}
									}			
								?>

							></input>	
						</div>	
					</div>
					<div class="log">
						<div class="title">Telefono</div>
						<input type="text" name="telefono"
							<?php
									if (isset($_SESSION['idseller'])){
										if ($arrayR[5] != "NULL"){
											echo 'value= "'.$arrayR[5].'"';
										}
									}			
								?>

						></input>
					</div>
					<div class="log">
						<div class="title">Cellulare</div>
						<input type="text" name="cellulare"
							<?php
									if (isset($_SESSION['idseller'])){
										if ($arrayR[6] != "NULL"){
											echo 'value= "'.$arrayR[6].'"';
										}
									}			
								?>
						></input>
					</div>
					<div class="giorniApertura">
						<div class="title">Giorni Apertura</div>
						<div id="radios">
							<div class="giorno">
        						<div class="titler">Lunedì</div> 
        						<input type="checkbox" name="g0" value="lun"
        							<?php
        								$giorni = "";
        								if(isset($_SESSION['idseller'])){
        									$giorni = explode("-", $arrayR[7]);
        									for($i = 0; $i<count($giorni); $i++){
        										if($giorni[$i] == "lun"){
        											echo 'checked="checked"';
        											$i = count($giorni);
        										}
        									}

        								}
        							?>

        						/>
        					</div>
        					<div class="giorno">
        						<div class="titler">Martedì</div> 
        						<input type="checkbox" name="g1" value="mar"
        							<?php
         								if(isset($_SESSION['idseller'])){
          									for($i = 0; $i<count($giorni); $i++){
        										if($giorni[$i] == "mar"){
        											echo 'checked="checked"';
        											$i = count($giorni);
        										}
        									}

        								}
        							?>
        						/>
        					</div>
        					<div class="giorno">
        						<div class="titler">Mercoledì</div> 
        						<input type="checkbox" name="g2" value="mer"
        							<?php
        								if(isset($_SESSION['idseller'])){
        									for($i = 0; $i<count($giorni); $i++){
        										if($giorni[$i] == "mer"){
        											echo 'checked="checked"';
        											$i = count($giorni);
        										}
        									}

        								}
        							?>

        						/>
        					</div>
        					<div class="giorno">
        						<div class="titler">Giovedì</div> 
        						<input type="checkbox" name="g3" value="giov"
        							<?php
        								if(isset($_SESSION['idseller'])){
         									for($i = 0; $i<count($giorni); $i++){
        										if($giorni[$i] == "giov"){
        											echo 'checked="checked"';
        											$i = count($giorni);
        										}
        									}
        								}
        							?>

        						/>
        					</div>
        					<div class="giorno">
        						<div class="titler">Venerdì</div> 
        						<input type="checkbox" name="g4" value="ven"
        							<?php
         								if(isset($_SESSION['idseller'])){
        									for($i = 0; $i<count($giorni); $i++){
        										if($giorni[$i] == "ven"){
        											echo 'checked="checked"';
        											$i = count($giorni);
        										}
        									}

        								}
        							?>

        						/>
        					</div>
        					<div class="giorno">
        						<div class="titler">Sabato</div> 
        						<input type="checkbox" name="g5" value="sab"
        							<?php        								
        								if(isset($_SESSION['idseller'])){
         									for($i = 0; $i<count($giorni); $i++){
        										if($giorni[$i] == "sab"){
        											echo 'checked="checked"';
        											$i = count($giorni);
        										}
        									}

        								}
        							?>
        						/>
        					</div>
        					<div class="giorno">
        						<div class="titler">Domenica</div> 
        						<input type="checkbox" name="g6" value="dom"
        							<?php
        								if(isset($_SESSION['idseller'])){
        									for($i = 0; $i<count($giorni); $i++){
        										if($giorni[$i] == "dom"){
        											echo 'checked="checked"';
        											$i = count($giorni);
        										}
        									}

        								}
        							?>
        						/>
        					</div>
        				</div>
        			</div>
        		
        			<div class="orariAperturam">
        				<div class="tipo">Orari Apertura Mattina</div>
        				<div class="giornatam">
        					<div class="titlem">da</div>
        					<select class="selectm" name="o0">
        						<?php
        							$numberOrariTot = "";
        							if(isset($_SESSION['idseller'])){
        								if(strlen($arrayR[8]) > 16 ){  //abbiamo tutte le fascie orarie 
		        							$suborari = explode(" ", $arrayR[8]);  //tolgo gli spazi
		        							$numberOrari = explode(":", $suborari[1]); //number array
		        							$suborariS = explode("/",$suborari[3]);
		        							$numberOrari1 = explode(":", $suborariS[0]); //number array 
		        							$numberOrari2 = explode(":", $suborari[4]); //number array
		        							$numberOrari3 = explode(":", $suborari[6]); //number array
		        							$numberOrariTot = array($numberOrari[0],$numberOrari[1],$numberOrari1[0],$numberOrari1[1],$numberOrari2[0],$numberOrari2[1],$numberOrari3[0],$numberOrari3[1]);
	        							}
	        							else{
	        								$suborari = explode(" ", $arrayR[8]);  //tolgo gli spazi
		        							$numberOrari = explode(":", $suborari[1]); //number array
		        							$numberOrari1 = explode(":",$suborari[3]); //number array
		        							$numberOrariTot = array($numberOrari[0],$numberOrari[1],$numberOrari1[0],$numberOrari1[1]);
	        							}	
        							
    									if(strlen($numberOrariTot) > 4)
    										echo '<option value="'.$numberOrariTot[0].'" selected>'.$numberOrariTot[0].'</option>';
    									else
    									{
    										if( intval($numberOrariTot[0]) < 12 ){ //ora inizio quindi fascia mattutina
    											echo '<option value="'.$numberOrariTot[0].'" selected>'.$numberOrariTot[0].'</option>';	
    										}
    										else
    											echo '<option value="c" selected>hh</option>';
    									}
    									echo '<option value="c">hh</option>';
    								}
    								else
    								{
        						 ?>
      							

								<option value="c" selected>hh</option>
								<?php
							}
								?>
								<option value="00">00</option><option value="01">01</option><option value="02">02</option><option value="03">03</option><option value="04">04</option><option value="05">05</option><option value="06">06</option><option value="07">07</option><option value="08">08</option><option value="09">09</option><option value="10">10</option><option value="11">11</option>
							</select>
							<div class="titlem">:</div>
							<select class="selectm" name="o1">
								<?php
									if (isset($_SESSION['idseller'])) {
										
										if(strlen($numberOrariTot) > 4)
    										echo '<option value="'.$numberOrariTot[1].'" selected>'.$numberOrariTot[1].'</option>';
    									else
    									{
    										if( intval($numberOrariTot[0]) < 12 ){ //ora inizio quindi fascia mattutina
    											echo '<option value="'.$numberOrariTot[1].'" selected>'.$numberOrariTot[1].'</option>';	
    										}
    										else
    											echo '<option value="c" selected>mm</option>';
    									}
    									echo '<option value="c">mm</option>';
									}
									else
									{		
								?>
								<option value="c" selected>mm</option>
								<?php

								}	?>
	  							<option value="00">00</option><option value="15">15</option><option value="30">30</option><option value="45">45</option>
							</select>
							<div class="titlem">a</div>
							<select class="selectm" name="o2">
								<?php
									if (isset($_SESSION['idseller'])) {
										
										if(strlen($numberOrariTot) > 4)
    										echo '<option value="'.$numberOrariTot[2].'" selected>'.$numberOrariTot[2].'</option>';
    									else
    									{
    										if( intval($numberOrariTot[0]) < 12 ){ //ora inizio quindi fascia mattutina
    											echo '<option value="'.$numberOrariTot[2].'" selected>'.$numberOrariTot[2].'</option>';	
    										}
    										else
    											echo '<option value="c" selected>hh</option>';
    									}
    									echo '<option value="c">hh</option>';
									}
									else
									{

								?>
								<option value="c" selected>hh</option>
								<?php
							}
								?>
								<option value="00">00</option><option value="01">01</option><option value="02">02</option><option value="03">03</option><option value="04">04</option><option value="05">05</option><option value="06">06</option><option value="07">07</option><option value="08">08</option><option value="09">09</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option>
							</select>
							<div class="titlem">:</div>
							<select class="selectm" name="o3">
								<?php
									if (isset($_SESSION['idseller'])) {
										
										if(strlen($numberOrariTot) > 4)
    										echo '<option value="'.$numberOrariTot[3].'" selected>'.$numberOrariTot[3].'</option>';
    									else
    									{
    										if( intval($numberOrariTot[0]) < 12 ){ //ora inizio quindi fascia mattutina
    											echo '<option value="'.$numberOrariTot[3].'" selected>'.$numberOrariTot[3].'</option>';	
    										}
    										else
    											echo '<option value="c" selected>mm</option>';
    									}
    									echo '<option value="c">mm</option>';
									}
									else
									{

								?>
								<option value="c" selected>mm</option>
								<?php
							}
							?>
	  							<option value="00">00</option><option value="15">15</option><option value="30">30</option><option value="45">45</option>
							</select>	
        				</div>
        			</div>
        			<div class="orariAperturap">
        				<div class="tipo">Orari Apertura Pomeriggio</div>
        				<div class="giornatap">
        					<div class="titlem">da</div>
        					<select class="selectm" name="o4">
        						<?php
									if (isset($_SESSION['idseller'])) {
										
										if(strlen($numberOrariTot) > 4)
    										echo '<option value="'.$numberOrariTot[4].'" selected>'.$numberOrariTot[4].'</option>';
    									else
    									{
    										if( intval($numberOrariTot[0]) >= 12 ){ //ora inizio quindi fascia pomeridiana
    											echo '<option value="'.$numberOrariTot[0].'" selected>'.$numberOrariTot[0].'</option>';	
    										}
    										else
    											echo '<option value="c" selected>hh</option>';
    									}
    									echo '<option value="c">hh</option>';
									}
									else
									{

								?>
								<option value="c" selected>hh</option>
								<?php
							}
								?>
								<option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option>
							</select>
							<div class="titlem">:</div>
							<select class="selectm" name="o5">
								<?php
									if (isset($_SESSION['idseller'])) {
										
										if(strlen($numberOrariTot) > 4)
    										echo '<option value="'.$numberOrariTot[5].'" selected>'.$numberOrariTot[5].'</option>';
    									else
    									{
    										if( intval($numberOrariTot[0]) >= 12 ){ //ora inizio quindi fascia pomeridiana
    											echo '<option value="'.$numberOrariTot[1].'" selected>'.$numberOrariTot[1].'</option>';	
    										}
    										else
    											echo '<option value="c" selected>mm</option>';
    									}
    									echo '<option value="c">mm</option>';
									}
									else
									{

								?>
								<option value="c" selected>mm</option>
								<?php
							}
								?>
	  							<option value="00">00</option><option value="15">15</option><option value="30">30</option><option value="45">45</option>
							</select>
							<div class="titlem">a</div>
							<select class="selectm" name="o6">
								<?php
									if (isset($_SESSION['idseller'])) {
										
										if(strlen($numberOrariTot) > 4)
    										echo '<option value="'.$numberOrariTot[6].'" selected>'.$numberOrariTot[6].'</option>';
    									else
    									{
    										if( intval($numberOrariTot[0]) >= 12 ){ //ora inizio quindi fascia pomeridiana
    											echo '<option value="'.$numberOrariTot[2].'" selected>'.$numberOrariTot[2].'</option>';	
    										}
    										else
    											echo '<option value="c" selected>hh</option>';
    									}
    									echo '<option value="c">hh</option>';
									}
									else
									{

								?>
								<option value="c" selected>hh</option>
								<?php }  ?>
								<option value="00">00</option><option value="01">01</option><option value="02">02</option><option value="03">03</option><option value="04">04</option><option value="05">05</option><option value="06">06</option><option value="07">07</option><option value="08">08</option><option value="09">09</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option>
							</select>
							<div class="titlem">:</div>
							<select class="selectm" name="o7">
								<?php
									if (isset($_SESSION['idseller'])) {
										
										if(strlen($numberOrariTot) > 4)
    										echo '<option value="'.$numberOrariTot[7].'" selected>'.$numberOrariTot[7].'</option>';
    									else
    									{
    										if( intval($numberOrariTot[0]) >= 12 ){ //ora inizio quindi fascia pomeridiana
    											echo '<option value="'.$numberOrariTot[3].'" selected>'.$numberOrariTot[3].'</option>';	
    											
    										}
    										else
    											echo '<option value="c" selected>mm</option>';
    									}
    									echo '<option value="c">mm</option>';
									}
									else
									{

								?>
								<option value="c" selected>mm</option>
								<?php } ?>
	  							<option value="00">00</option><option value="15">15</option><option value="30">30</option><option value="45">45</option>
							</select>	
        				</div>
        			</div>
					<div class="dom">
						<div class="title">Domicilio</div>
						<div id="domcheck">
							<input type="checkbox" name="domicilio"
								<?php
									if (isset($_SESSION['idseller'])) {
										if($arrayR[9] == "1")
											echo 'checked="checked"';
									}
								?>

							/>
        				</div>
        			</div>		
        			<div class="dom">
						<div class="title">Costo Domicilio</div>
						<input id="costo" type="text" name="costoDomicilio" 
							<?php
									if (isset($_SESSION['idseller'])) {
										if($arrayR[9] == "1")
											echo 'value="'.$arrayR[10].'"';
									}
								?>
						 />
        				<p id="euro">€</p>
        			</div>	
					
					<div class="log">
						<div class="title">Email</div>
						<input type="text" name="email"
							<?php
								if (isset($_SESSION['idseller'])){
									echo 'value= "'.$arrayR[13].'"';
										
								}			
							?>
						></input>
						<?php
							if(isset($_SESSION['idseller']))
								echo '<input type="hidden" name="oldEmail" value="'.$arrayR[13].'"></input>';
						?>						
					</div>
					<div class="log">
						<div class="title">Password</div>
						<input type="password" name="password"></input>
					</div>
					<div class="log">
						<div class="title">Reinserisci Password</div>
						<input type="password" name="rpassword"></input>
					</div>
					<div id="sendlogin">
						<input name="inviadati" type="submit" 
							<?php
								if (isset($_SESSION['idseller'])) 
									echo 'value="Modifica"';
								else
									echo 'value="Inserisci"';
								
							?>
						/>
					</div>
					<?php
						if(!isset($_SESSION['idseller']))
							echo '<div id="textSeller">Sei un cliente? <a href="registerUser.php">Clicca qui!</a></div>';
					?>	
				</div>
			</form>
		</div>
		<div class="footer">
			<p>Fast&Fruit. Copyright &#169; 2016. Tutti i diritti riservati. fastandfruit.it</p>	
		</div>
	</body>
</html>