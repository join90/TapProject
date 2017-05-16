<html xmlns="http://www.w3.org/1999/xhtml"><head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Fast&Fruit</title>
	<link href="assets/css/loginStyle.css" rel="stylesheet" type="text/css" media="screen">
	</head>
	<body>
		<?php 
			session_start();
		
			require 'connect.php';
			
			$success=true;
			if(isset($_POST['inviadati'])){
			
				$username = $_POST['username'];
				$password = $_POST['password'];
				
			
				$query = "SELECT c.id FROM clienti c where c.email = '".$username."' and c.pass = '".$password."' and c.presente = 1;";
			
				$result = mysqli_query($conn,$query) or die("Errore nella selezione dei parametri!");
			
				if(mysqli_num_rows($result) == 1){
					$row = mysqli_fetch_row($result); //restituisce un array con l'id dell'utente trovato
					
					$_SESSION['iduser'] = $row[0];
					$_COOKIE['session'] = $row[0];
					setcookie("session",$row[0],time()+2000000,"/");	
					header("Location: ".$_COOKIE['strproduct']);
						
					
				}
				else{

					$query = "SELECT n.id FROM negozi n where n.email = '".$username."' and n.pass = '".$password."' and n.presente = 1;";
					$result = mysqli_query($conn,$query) or die("Errore nella selezione dei parametri!");

					if(mysqli_num_rows($result) == 1){
						
						$row = mysqli_fetch_row($result); //restituisce un array con l'id dell'utente trovato
						$_SESSION['idseller'] = $row[0];	
						header("Location: controlpanel.php");
					}
					else
						$success=false;

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
			</div>
			<div class="downBar">
				<p>Frutta e verdura della migliore qualità da più di 1000 negozi in tutta Italia. Accedi subito!</p>
			</div>
			<?php
				if($success==false){ ?>

			<div class="downBarError">
				<p>Credenziali non valide</p>
			</div>

			<?php
				}
			?>
			<p id="resultTitle">Effettua il login!</p>
			<form method="post" action="login.php" name="Login">
				<div id="login">
					<div class="log">
						<div class="title">Email</div>
						<input type="text" name="username"></input>
					</div>	
					<div class="log">
						<div class="title">Password</div>
						<input type="password" name="password"></input>
					</div>
					<!--<div id="radios">
						<div class="radio">
        					<div class="titler">client</div> 
        					<input type="radio" name="scelta" value="clienti"/>
        				</div>
        				<div class="radio">
        					<div class="titler">seller</div> 
        					<input type="radio" name="scelta" value="negozi"/>
        				</div>
        			</div>-->
        			<div id="sendlogin">
						<input name="inviadati" type="submit" value="Accedi"/>
					</div>
				</div>
			</form>
		</div>
		<div class="footer">
			<p>Fast&Fruit. Copyright &#169; 2016. Tutti i diritti riservati. fastandfruit.it</p>	
		</div>
	</body>
</html>