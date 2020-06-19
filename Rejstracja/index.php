<?php

	session_start();
	
	if ((isset($_SESSION['zalogowany'])) && ($_SESSION['zalogowany']==true))
	{
		header('Location: gra.php');
		exit();
	}

?>

<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<b><title>Osadnicy</title></b>
</head>

<body>
	
	<b><p style="font-size:20px;">"Kto szuka i próbuje, ten się uczy i znajduje." <i> - K.T. </i></p></b>
	<br /> 

	<form action="zaloguj.php" method="post">
	
		Login: <br /> <input type="text" name="login" /> <br />
		Hasło: <br /> <input type="password" name="haslo" /> <br /><br />
		<input type="submit" value="Zaloguj się" />

	</form>

	<br/><br/>
	<a href="rejstracja.php">REJSTRACJA</a>

<?php
	if(isset($_SESSION['blad']))	echo $_SESSION['blad'];
?>

</body>
</html>