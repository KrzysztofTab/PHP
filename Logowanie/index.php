<?php
	session_start();

			//jeśli użytkownik nie jest zalogowany przeżuć go do panelu logowania
	if((isset($_SESSION['zalogowany'])) && ($_SESSION['zalogowany']== true))
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
	<b><title>OSADNICY</title></b>
</head>

<body>
	
	<b>Kto nie ustaje w swych próbach, temu się udaje. - K.T.</b><br/><br/>

	<form action="zaloguj.php" method="post">
	
		Login: <br /> <input type="text" name="login" /> <br />

				<!--type="password" - funkcja która pokazuje kropi za znaki-->
		Hasło: <br /> <input type="password" name="haslo" /> <br /><br />

			<input type="submit" value="Zaloguj się" />
	
	</form>

<?php
			//wyświetlanie informacji o złych danych do logowania
	if(isset($_SESSION['blad'])) echo $_SESSION['blad'];
?>
	
</body>
</html>