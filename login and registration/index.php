<?php

	session_start();
	
	if ((isset($_SESSION['zalogowany'])) && ($_SESSION['zalogowany']==true))
	{
		header('Location: udane.php');
		exit();
	}

?>
<?php


if (isset($_POST['email']))
{
	
	$wszystko_OK=true;
	
	
	$nick = $_POST['nick'];
	

	if ((strlen($nick)<3) || (strlen($nick)>20))
	{
		$wszystko_OK=false;
		$_SESSION['e_nick']="Nick musi posiadać od 3 do 20 znaków!";
	}
	
	if (ctype_alnum($nick)==false)
	{
		$wszystko_OK=false;
		$_SESSION['e_nick']="Nick może składać się tylko z liter i cyfr (bez polskich znaków)";
	}

	$email = $_POST['email'];
	$emailB = filter_var($email, FILTER_SANITIZE_EMAIL);
	
	if ((filter_var($emailB, FILTER_VALIDATE_EMAIL)==false) || ($emailB!=$email))
	{
		$wszystko_OK=false;
		$_SESSION['e_email']="Podaj poprawny adres e-mail!";
	}
	
	$haslo1 = $_POST['haslo1'];
	$haslo2 = $_POST['haslo2'];
	
	if ((strlen($haslo1)<8) || (strlen($haslo1)>20))
	{
		$wszystko_OK=false;
		$_SESSION['e_haslo']="Hasło musi posiadać od 8 do 20 znaków!";
	}
	
	if ($haslo1!=$haslo2)
	{
		$wszystko_OK=false;
		$_SESSION['e_haslo']="Podane hasła nie są identyczne!";
	}	

	$haslo_hash = password_hash($haslo1, PASSWORD_DEFAULT);
	

	
	$sekret = "6Lf_EJ4UAAAAAN6Xtab68hismYAAt7kkewgtxUiN";
	
	$sprawdz = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$sekret.'&response='.$_POST['g-recaptcha-response']);
	
	$odpowiedz = json_decode($sprawdz);
	
	if ($odpowiedz->success==false)
	{
		$wszystko_OK=false;
		$_SESSION['e_bot']="Potwierdź, że nie jesteś botem!";
	}		

	$_SESSION['fr_nick'] = $nick;
	$_SESSION['fr_email'] = $email;
	$_SESSION['fr_haslo1'] = $haslo1;
	$_SESSION['fr_haslo2'] = $haslo2;
	
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

			$rezultat = $polaczenie->query("SELECT id FROM uzytkownicy WHERE email='$email'");
			
			if (!$rezultat) throw new Exception($polaczenie->error);
			
			$ile_takich_maili = $rezultat->num_rows;
			if($ile_takich_maili>0)
			{
				$wszystko_OK=false;
				$_SESSION['e_email']="Istnieje już konto przypisane do tego adresu e-mail!";
			}		


			$rezultat = $polaczenie->query("SELECT id FROM uzytkownicy WHERE user='$nick'");
			
			if (!$rezultat) throw new Exception($polaczenie->error);
			
			$ile_takich_nickow = $rezultat->num_rows;
			if($ile_takich_nickow>0)
			{
				$wszystko_OK=false;
				$_SESSION['e_nick']="Ten login jest już zajęty! Wybierz inny.";
			}
			
			if ($wszystko_OK==true)
			{
				
				
				if ($polaczenie->query("INSERT INTO uzytkownicy VALUES (NULL, '$nick', '$haslo_hash', '$email')"))
				{
					$_SESSION['udanarejestracja']=true;
					header('Location: index.php');
					$_SESSION['udanoutworzyc'] = '<span style="color:green">Konto zostało pomyślnie utworzone. Możesz teraz się zalogować</span>';
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
		echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o rejestrację w innym terminie!</span>';
		//echo '<br />Informacja developerska: '.$e;
	}
	
}


?>

<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<script src='https://www.google.com/recaptcha/api.js'></script>
	<link href="style.css" rel="stylesheet" type="text/css" media="all" />
</head>
<body>
</br>
<div class="content">
    <div class="logowanie">
            <center><span style="font-size:35px; color:#333;"> LOGOWANIE</span></br></br></center>
        	<form action="zaloguj.php" method="post">
	
                    Login: <br /> <input type="text" name="login" /> <br />
                    Hasło: <br /> <input type="password" name="haslo" /> <br /><br />
                    <div class="low">
                    <input type="submit" value="Zaloguj się" /></div><br />
                </form>
				<?php
	if(isset($_SESSION['blad']))	echo $_SESSION['blad'];
?>
			</div>


<div class="rejestracja">
<?php
	if(isset($_SESSION['udanoutworzyc']))	echo $_SESSION['udanoutworzyc'];
?>
<center><span style="font-size:35px;color:#333;"> REJESTRACJA</span></br></center>
<form method="post">
	
		Login: <br /> <input type="text" value="<?php
			if (isset($_SESSION['fr_nick']))
			{
				echo $_SESSION['fr_nick'];
				unset($_SESSION['fr_nick']);
			}
		?>" name="nick" />
		
		<?php
			if (isset($_SESSION['e_nick']))
			{
				echo '<div class="error">'.$_SESSION['e_nick'].'</div>';
				unset($_SESSION['e_nick']);
			}
		?>
		
		E-mail: <br /> <input type="text" value="<?php
			if (isset($_SESSION['fr_email']))
			{
				echo $_SESSION['fr_email'];
				unset($_SESSION['fr_email']);
			}
		?>" name="email" />
		
		<?php
			if (isset($_SESSION['e_email']))
			{
				echo '<div class="error">'.$_SESSION['e_email'].'</div>';
				unset($_SESSION['e_email']);
			}
		?>
		
		Twoje hasło: <br /> <input type="password"  value="<?php
			if (isset($_SESSION['fr_haslo1']))
			{
				echo $_SESSION['fr_haslo1'];
				unset($_SESSION['fr_haslo1']);
			}
		?>" name="haslo1" />
		
		<?php
			if (isset($_SESSION['e_haslo']))
			{
				echo '<div class="error">'.$_SESSION['e_haslo'].'</div>';
				unset($_SESSION['e_haslo']);
			}
		?>		
		
		Powtórz hasło: <br /> <input type="password" value="<?php
			if (isset($_SESSION['fr_haslo2']))
			{
				echo $_SESSION['fr_haslo2'];
				unset($_SESSION['fr_haslo2']);
			}
		?>" name="haslo2" />
		</br>
		<center>
		<div class="g-recaptcha" data-sitekey="6Lf_EJ4UAAAAANGfB355gTTl9iIQRbTKTQV49Kfr"></div>
		
		<?php
			if (isset($_SESSION['e_bot']))
			{
				echo '<div class="error">'.$_SESSION['e_bot'].'</div>';
				unset($_SESSION['e_bot']);
			}
		?>	
		</center>
		<br />
		
		<input type="submit" value="Zarejestruj się" />
		
	</form>
	</div></div>
</body>
</html>