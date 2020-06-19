<?php

	session_start();
	
	if ((!isset($_POST['login'])) || (!isset($_POST['haslo'])))
	{
		header('Location: index.php');
		exit();
	}
			//once- PHP sprawdza czy plik nie został dołączony wcześniej i nie zrobi tego drugi raz, zrobi to tylko raz połączenie z bazą danych w SQL
	require_once "connect.php";

	$polaczenie = @new mysqli($host, $db_user, $db_password, $db_name);
	
	if ($polaczenie->connect_errno!=0)
	{
		echo "Error: ".$polaczenie->connect_errno;
	}
	else
	{		
			//odczytywanie danych z pól logowania
		$login = $_POST['login'];
		$haslo = $_POST['haslo'];
		
		$login = htmlentities($login, ENT_QUOTES, "UTF-8");

			//zapytanie do bazy danych
			//sprintf- wstawi w miejscu %s argumenty po przecinku $login-$haslo 
			//mysqli_real_escape_string - zapytania do SQLA zabespiecza przed wszczykiwaniem do SQLA
		if ($rezultat = @$polaczenie->query(
		sprintf("SELECT * FROM uzytkownicy WHERE user='%s'", mysqli_real_escape_string($polaczenie,$login))))
		{
			$ilu_userow = $rezultat->num_rows;
			if($ilu_userow>0)
			{
				$wiersz = $rezultat->fetch_assoc();
				
				if (password_verify($haslo, $wiersz['pass']))
				{	
						//tworzenie sesji z bzay SQL do korzystania przez inne podstrony
					$_SESSION['zalogowany'] = true;
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
					$rezultat->free_result();
						//przekierowanie do gry 
					header('Location: gra.php');
				}
				else 
				{		
						//Nieprawidłowy login lub hasło - przekierowanie do ponownego logowania
					$_SESSION['blad'] = '<span style="color:red">Nieprawidłowy login lub hasło!</span>';
					header('Location: index.php');
				}
				
			} else {
					//Nieprawidłowy login lub hasło - przekierowanie do ponownego logowania
				$_SESSION['blad'] = '<span style="color:red">Nieprawidłowy login lub hasło!</span>';
				header('Location: index.php');
				
			}
			
		}
		
		$polaczenie->close();
	}
	
?>