<?php
	session_start();

		//zamkniecie sesji gry
	session_unset();
	
		//przekierowanie na stroną główną
	header('Location: index.php');
?>
