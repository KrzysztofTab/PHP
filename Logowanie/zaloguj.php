<?php

	session_start();

	if ((!isset($_POST['login'])) || (!isset($_POST['haslo'])))
		{
		header('Location: index.php');
		exit();
		}

				/*once- PHP sprawdza czy plik nie został dołączony wcześniej i nie zrobi tego drugi raz, zrobi to tylko raz
				połączenie z bazą danych w SQL*/
	require_once "connect.php";
	$polaczenie = @new mysqli($db_host, $db_user, $db_password, $db_name);
	if ($polaczenie -> connect_errno!=0)
		{
		echo "Error:".$polaczenie->connect_errno."Opis:".$polaczenie->connect_error;
		}
	else
		{
				//odczytywanie danych z pól logowania
		$login = $_POST['login'];
		$haslo = $_POST['haslo'];

				//przepuszczanie loginu i hasła przez encje
		$login = htmlentities($login,ENT_QUOTES,"UTF-8");
		$haslo = htmlentities($haslo,ENT_QUOTES,"UTF-8");

				//zapytanie do bazy danych
				//sprintf- wstawi w miejscu %s argumenty po przecinku $login-$haslo 
				//mysqli_real_escape_string - zapytania do SQLA zabespiecza przed wszczykiwaniem do SQLA
		if ($rezultat = $polaczenie->query(sprintf("SELECT * FROM uzytkownicy WHERE user='%s' AND pass='%s'",
			mysqli_real_escape_string($polaczenie,$login),
			mysqli_real_escape_string($polaczenie,$haslo))))
			{
			$ilu_userow = $rezultat->num_rows;
			if ($ilu_userow>0)
				{	
					//tworzenie sesji do korzystania przez inne podstrony
				$_SESSION['zalogowany']= true;
				$wiersz = $rezultat->fetch_assoc();
				$_SESSION['id'] = $wiersz['id'];
				$_SESSION['user'] = $wiersz['user'];
				$_SESSION['drewno'] = $wiersz['drewno'];
				$_SESSION['kamien'] = $wiersz['kamien'];
				$_SESSION['zboze'] = $wiersz['zboze'];
				$_SESSION['email'] = $wiersz['email'];
				$_SESSION['dnipremium'] = $wiersz['dnipremium'];

					//unset - usuń z sesi 
				unset($_SESSION['blad']);

					//czyszczenie rekordów zapytania z pamięci (!!! OBOWIAZKOWE !!!)
				$rezultat->close();

						//przekierowanie do gry 
				header('Location: gra.php');			
				}
			else 
				{
				$_SESSION['blad'] = '<span style="color:red">Nieprawidłowy login lub hasło!</span>';
				header('Location: index.php');
				}
			}
			$polaczenie->close();
		}		
?>