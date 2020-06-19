<?php

	session_start();
			//walidacja po wysłaniu formularza
	if (isset($_POST['email']))
	{
			//udana walidacja
		$wszystko_OK=true;
		
			//sprawdzanie poprawność nickname'a
		$nick = $_POST['nick'];
		
			//sprawdzenie długości nicka
		if ((strlen($nick)<3) || (strlen($nick)>20))
			{
				$wszystko_OK=false;
				$_SESSION['e_nick']="Niepoprawna ilość znaków !";
			}

				//sprawdzanie znaków w nicku alfanumerycznych
		if (ctype_alnum($nick)==false)
			{
				$wszystko_OK=false;
				$_SESSION['e_nick']="Wpisano nie właściwe znaki !";
			}

				//sanityzacja - sprawdzanie poprawności adresu email
		$email=$_POST['email'];
		$emailB=filter_var($email,FILTER_SANITIZE_EMAIL);
		
		if ((filter_var($emailB, FILTER_VALIDATE_EMAIL)==false) || ($emailB!=$email))
			{
				$wszystko_OK=false;
				$_SESSION['e_email']="Podano nie poprawny adres E-mail !";
			}	
				//sprawdzanie poprawność hasła
		$haslo1 = $_POST['haslo1'];
		$haslo2 = $_POST['haslo2'];

		if((strlen($haslo1)<8) || (strlen($haslo1)>20))
			{
				$wszystko_OK=false;
				$_SESSION['e_haslo']="Hasło ma niewłaściwą ilość znaków !";
			}
		if($haslo1!=$haslo2)
			{
				$wszystko_OK=false;
				$_SESSION['e_haslo']="Podane hasła nie są identyczne !";
			}	

		$haslo_hash = password_hash($haslo1, PASSWORD_DEFAULT);
		
				//akceptacja regulaminu
		if(!isset($_POST['regulamin']))
			{
				$wszystko_OK=false;
				$_SESSION['e_regulamin']="Potwerdź akceptację regulaminu !";
			}
				
				//sprawdzanie CAPTCHA - "tajny klucz witryny"
		$sekret = "6Lf14aYZAAAAAAZA7IFshVxVz-VxDwh222amrf6a";
		$sprawdz = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$sekret.'&response='.$_POST['g-recaptcha-response']);
		$odpowiedz = json_decode($sprawdz);
		
		if ($odpowiedz->success==false)
			{
				$wszystko_OK=false;
				$_SESSION['e_bot']="Potwierdź, że nie jesteś botem !";
			}		

		require_once "connect.php";
		mysqli_report(MYSQLI_REPORT_STRICT);
			try 
		{
			$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
			if ($polaczenie->connect_errno!=0)
				{
					throw new Exception(mysqli_connect_errno());
				}
			else
				{
						//Czy email już istnieje?
					$rezultat = $polaczenie->query("SELECT id FROM uzytkownicy WHERE email='$email'");
					if (!$rezultat) throw new Exception($polaczenie->error);
					$ile_takich_maili = $rezultat->num_rows;
					if($ile_takich_maili>0)
						{
							$wszystko_OK=false;
							$_SESSION['e_email']="Istnieje już konto przypisane do tego adresu e-mail!";
						}		

						//Czy nick jest już zarezerwowany?
					$rezultat = $polaczenie->query("SELECT id FROM uzytkownicy WHERE user='$nick'");
					if (!$rezultat) throw new Exception($polaczenie->error);
					$ile_takich_nickow = $rezultat->num_rows;
					if($ile_takich_nickow>0)
						{
							$wszystko_OK=false;
							$_SESSION['e_nick']="Istnieje już gracz o takim nicku! Wybierz inny.";
						}
					if ($wszystko_OK==true)
						{
									//Wszystkie testy zaliczone, dodanie gracza do bazy			
							if ($polaczenie->query("INSERT INTO uzytkownicy VALUES (NULL, '$nick', '$haslo_hash', '$email', 100, 100, 100, 14)"))
								{
									$_SESSION['udanarejestracja']=true;
									header('Location: witamy.php');
								}
							else
								{
									throw new Exception($polaczenie->error);
								}
						}
				
					$polaczenie->close();
				}	
		}
		catch(Exception $e)
		{
			echo '<span style="color:red;">Błąd serwera!</span>';
			echo '<br />Informacja developerska: '.$e;
		}
	}
?>

<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<b><title>Osadnicy-Rejstracja</title></b>
	<script src="https://www.google.com/recaptcha/api.js" async defer></script>
		
		<!-- style = css; kolor wyświetlanego komunikatu pod inputem -->	
	<style>
		.error
		{
			color:red;
			margin-top: 10px;
			margin-bottom: 10px;
		}
	</style>
</head>

<body>
	
	<form method="post">
		<br/><br/>
		
		<b>Nickname:</b><br /> <input type="text" name="nick"/><br/>
		<i><small>Od 3 do 20 znaków; litery i cyfry;<br/>bez polskich znaków</small></i><br/>
			<?php
				if (isset($_SESSION['e_nick']))
				{
					echo '<div class="error">'.$_SESSION['e_nick'].'</div>';
					unset($_SESSION['e_nick']);
				}
			?>
		<br/>

		<b>E-mail:</b> <br /> <input type="text" name="email"/><br />
		<i><small>Adres E-mail @</small></i>
		<br/>
			<?php
				if (isset($_SESSION['e_email']))
				{
					echo '<div class="error">'.$_SESSION['e_email'].'</div>';
					unset($_SESSION['e_email']);
				}
			?>
		<br/>	

		<b>Hasło:</b> <br /> <input type="password" name="haslo1"/> <br />
		<i><small>Od 8 do 20 znaków; litery i cyfry;<br/>bez polskich znaków</small></i><br/>
			<?php
				if (isset($_SESSION['e_haslo']))
				{
					echo '<div class="error">'.$_SESSION['e_haslo'].'</div>';
					unset($_SESSION['e_haslo']);
				}
			?>
		<br/>	

		<b>Powtórz Hasło:</b> <br/> <input type="password" name="haslo2" /> <br /><br />
			
		<label>
		<input type="checkbox" name="regulamin"/> <i><b>Akceptuj regulamin</b></i>
		</label>
			<?php
				if (isset($_SESSION['e_regulamin']))
				{
					echo '<div class="error">'.$_SESSION['e_regulamin'].'</div>';
					unset($_SESSION['e_regulamin']);
				}
			?>

		<br /><br />
			<!-- jawny klucz witryny-->
		<div class="g-recaptcha" data-sitekey="6Lf14aYZAAAAABJ1aUmWtrJJ29CnNY7vmnQd9hmU"></div>
			<?php
				if (isset($_SESSION['e_bot']))
				{
					echo '<div class="error">'.$_SESSION['e_bot'].'</div>';
					unset($_SESSION['e_bot']);
				}
			?>
		
		<br /><input type="submit" value="Zarejestruj się" />
	</form>

</body>
</html>