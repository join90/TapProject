<?php
	if(isset($_POST['upload'])){
		$to = "prodotti/".$_FILES['file']['name'];
		$allowed_types = array("image/jpeg");
		if(in_array($_FILES['file']['type'], $allowed_types)){
			move_uploaded_file($_FILES['file']['tmp_name'], $to);
			echo "Uploaded";	
		}
		else
			echo "Tipo file non corretto! Solo .jpeg";
		
	}	

?>

<!DOCTYPE html>
<html>
<head>
	<title>Upload</title>
</head>
<body>
	<form action="uploadFile.php" method="post" enctype="multipart/form-data">	
		<input type="hidden" name="upload" value="1"></input>
		<input type="file" name="file"></input>
		<input type="submit" value="Upload"></input>
	</form>
</body>
</html>