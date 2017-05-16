<html xmlns="http://www.w3.org/1999/xhtml"><head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Fast&Fruit</title>
  <link href="assets/css/loginStyle.css" rel="stylesheet" type="text/css" media="screen">
  </head>
  <body>
    <?php
        session_start();

    ?>
   
    <div class="main">
      <div class="header">
        <div class="logo">
          <a href="index.php"><img src="assets/images/logo.jpg"></a>
        </div>      
      </div>
      <div class="downBar">
        <p>Frutta e verdura della migliore qualità da più di 1000 negozi in tutta Italia. Accedi subito!</p>
      </div>
      <p id="resultTitle">Registrazione</p>
      <div id="amount">
          <?php
              if(!isset($_SESSION['iduser'])){
                  echo "Registrazione avvenuta con successo!.";
                  echo '<a href="login.php">Clicca qui per effettuare il login.</a>';
              }
              else{
                  echo "Modifica avvenuta con successo!.";
                  echo '<a href="account.php">Clicca qui per ritornare alla pagina precedente.</a>';
              }
          ?>

       </div>
    </div>
    <div class="footer">
      <p>Fast&Fruit. Copyright &#169; 2016. Tutti i diritti riservati. fastandfruit.it</p>  
    </div>
  </body>
</html>