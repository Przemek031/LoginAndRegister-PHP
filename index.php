<?php
session_start();

if (isset($_SESSION['zalogowany']) &&  ($_SESSION['zalogowany']==true))
{
	header('Location: ok.php');
	exit();
}
?>

<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title></title>
</head>

<body>
	<a href= "register.php">Rejestracja</a>
	<br /><br />

	Logowanie:<br /> <br />	
	
	<form action="login.php" method="post">
		Login: <br /> <input type="text" name="login" /> <br />
		Hasło: <br /> <input type="password" name="pass" /> <br /> <br />
		<input type="submit" value="Zaloguj się" />
		
	
	</form>
<?php
	
	if (isset($_SESSION['blad']))
	{
		echo $_SESSION['blad'];
	}
	?>
</body>
</html>
