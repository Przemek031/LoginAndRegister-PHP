<?php

	session_start();
	
	if (!isset($_SESSION['zalogowany']))
	{
		header('Location: index.php');
		exit();
	}
	
?>
<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>Zalogowano</title>
</head>

<body>
</br>
<?php

	echo '<p>[<a href="logout.php">Wyloguj</a> ]</p>';
	echo "<p>Login: ".$_SESSION['user'];
	echo "<p><b>E-mail:</b>: ".$_SESSION['email'];
	
?>

</body>
</html>