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
				
				$city = NULL; $Via = NULL; $telefono = "NULL"; $ncivico = NULL;  $nome = NULL; $cognome = NULL; //quelle not null li inizializzo con null

				if (($_POST['nome'] != "") && ($_POST['cognome'] != "")) {
					$nome = "'".htmlentities($_POST['nome'] , ENT_QUOTES)."'";
					$cognome = "'".htmlentities($_POST['cognome'], ENT_QUOTES)."'";
				}
					
				if(isset($_POST['city'])){
					$city = trim($_POST['city']);
					$city = htmlentities($city, ENT_QUOTES); //conversione in entità html
					$city = "'".str_replace("\n","",$city)."'"; 
				}

				
				if (isset($_POST['tipoVia'])){
					
					if(($_POST['indirizzo'] != "") && ($_POST['numerocivico'] != "")){
						
						$Via = "'".htmlentities($_POST['tipoVia']." ".$_POST['indirizzo'],ENT_QUOTES)."'";
						$ncivico = "'".htmlentities($_POST['numerocivico'],ENT_QUOTES)."'";
					}
				}
				

				if($_POST['telefono'] != ""){	
					
					$cerca = array("-"," ","/");
					$telefono = str_replace($cerca, "", $_POST['telefono']);

					if (is_numeric($telefono)) {
						
						$telefono = "'".$_POST['telefono']."'";	
					}
					else
						$telefono = "NULL";
				}

				if(preg_match("/^[A-Za-z0-9-_.+%]+@[A-Za-z0-9-.]+.[A-Za-z]{2,4}$/", $_POST['email'])){
					
					if(($_POST['password']!= "") && ($_POST['rpassword'] != "")){
						
						if(strlen($_POST['password']) >= 8){
							if($_POST['password'] == $_POST['rpassword']){

								$email = "'".htmlentities($_POST['email'],ENT_QUOTES)."'";
								$password = "'".htmlentities($_POST['password'],ENT_QUOTES)."'";

								$queryEmailC = "SELECT c.email, c.pass from clienti c where c.email = ".$email.";";
								$queryEmailN = "SELECT n.email, n.pass from negozi n where n.email = ".$email.";";
								$resultEmailC = mysqli_query($conn,$queryEmailC) or die("Error with MySQL Query: ".mysqli_error($conn));
								$resultEmailN = mysqli_query($conn,$queryEmailN) or die("Error with MySQL Query: ".mysqli_error($conn));


								if(!isset($_SESSION['iduser'])){
									
									if((mysqli_num_rows($resultEmailC) == 0) && (mysqli_num_rows($resultEmailN) == 0) ){ //se non c'è la stessa email nel db

										$query = "INSERT into clienti (nome,cognome,comune,viaPiazza,ncivico,telefono,email,pass,presente) values (".$nome.",".$cognome.",".$city.",".$Via.",".$ncivico.",".$telefono.",".$email.",".$password.",1);";
										
										$result = mysqli_query($conn,$query) or die("Error with MySQL Query: ".mysqli_error($conn));
									}
									else
										exit("Email gia' esistente!");
								}
								
								else{
									
									$success = false;
									
									if($_POST['oldEmail'] == $_POST['email']) //controllo se l'email è mia, nel caso aggiorno
										$success = true;

									$query = "UPDATE clienti set nome = ".$nome.", cognome = ".$cognome.", comune = ".$city.", viaPiazza = ".$Via.", ncivico = ".$ncivico.", telefono = ".$telefono.", email = ".$email.", pass = ".$password." where clienti.id = ".$_SESSION['iduser'].";";


									if(($success == true) || ((mysqli_num_rows($resultEmailC) == 0) && (mysqli_num_rows($resultEmailN) == 0))){ //se sono io, oppure quell'email non è presente nel db alloro aggiorno

										$result = mysqli_query($conn,$query) or die("Error with MySQL Query: ".mysqli_error($conn));
										header("Location: controlpanel.php");
									}
									else
										exit("Email gia' esistente!");
									
								}
								
							header("Location: success.php");
							
						}
						else
							exit("Ridigita correttamente la password!");	
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
			<form method="post" action="registerUser.php" name="Login">
				<div id="login">
					<div class="log">
						<div class="title">Nome</div>
						<input type="text" name="nome"
							<?php
								if(isset($_SESSION['iduser'])){
									$query = 'Select * from clienti i where i.id = '.$_SESSION['iduser'].';';
									$result = mysqli_query($conn,$query) or die("Error with MySQL Query: ".mysqli_error($conn));
									$arrayC = mysqli_fetch_row($result);	
					
									echo 'value="'.$arrayC[1].'"';	
								}
								
							?>

						></input>
					</div>	
					<div class="log">
						<div class="title">Cognome</div>
						<input type="text" name="cognome"
							<?php
								if(isset($_SESSION['iduser']))
									echo 'value="'.$arrayC[2].'"';
							?>
						></input>
					</div>
					<div class="log">
						<div class="title">Comune</div>
						<select class="selectelement" name="city" id="cit">
							
							<?php
								if (isset($_SESSION['iduser']))
									if ($arrayC[3] != "NULL") 
										echo '<option value="'.$arrayC[3].'" selected>'.$arrayC[3].'</option>'; 	
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
									if (isset($_SESSION['iduser'])){
										if ($arrayC[4] != "NULL"){
											$via = explode(" ", $arrayC[4]);
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
									if (isset($_SESSION['iduser'])){
										if ($arrayC[4] != "NULL"){
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
									if (isset($_SESSION['iduser'])){
										if ($arrayC[5] != "NULL"){
											echo 'value= "'.$arrayC[5].'"';
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
									if (isset($_SESSION['iduser'])){
										if ($arrayC[6] != "NULL"){
											echo 'value= "'.$arrayC[6].'"';
										}
									}			
								?>

						></input>
					</div>
					<div class="log">
						<div class="title">Email</div>
						<input type="text" name="email"
							<?php
								if (isset($_SESSION['iduser'])){
									echo 'value= "'.$arrayC[7].'"';
										
								}			
							?>
						></input>
						<?php
							if(isset($_SESSION['iduser']))
								echo '<input type="hidden" name="oldEmail" value="'.$arrayC[7].'"></input>';
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
								if (isset($_SESSION['iduser'])) 
									echo 'value="Modifica"';
								else
									echo 'value="Inserisci"';
								
							?>

						/>
					</div>
					<?php
						if(!isset($_SESSION['iduser']))
							echo '<div id="textSeller">Sei un venditore? <a href="registerSeller.php">Clicca qui!</a></div>';
					?>
				</div>
			</form>
		</div>
		<div class="footer">
			<p>Fast&Fruit. Copyright &#169; 2016. Tutti i diritti riservati. fastandfruit.it</p>	
		</div>
	</body>
</html>