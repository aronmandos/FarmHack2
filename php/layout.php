<!DOCTYPE HTML>
<?php
function getHeader() {
	session_start ();
	?>
<head>
<title>QuakePoint</title>
<meta charset="utf-8">
<style>
#logo {
	float: center;
	height: 50px;
	width: 100px;
}
</style>
<!-- Google Fonts -->
<link
	href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300|Playfair+Display:400italic'
	rel='stylesheet' type='text/css' />
<!-- CSS Files -->
<link rel="stylesheet" type="text/css" media="screen"
	href="../css/style.css">
<link rel="stylesheet" type="text/css" media="screen"
	href="../menu/css/simple_menu.css">
<!-- JS Files -->
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script>
  $(function () {
      $("#prod_nav ul").tabs("#panes > div", {
          effect: 'fade',
          fadeOutSpeed: 400
      });
  });
  </script>
<script>
  $(document).ready(function () {
      $(".pane-list li").click(function () {
          window.location = $(this).find("a").attr("href");
          return false;
      });
  });
  </script>
</head>
<body>
	<div class="header">
		<?php getLoginWindow ()?>
		<!-- <div id="site_title"><a href="index.php"><img src="../img/logo.png" id="logo"></a></div> -->
		<!-- Main Menu -->
		<ol id="menu">
			<li class="active_menu_item"><a href="index.php">Home</a></li>
			<li><a href="FAQ.php">FAQ</a>
			<li><a href="aardbevingen.php">Alle Aardbevingen</a></li>
			<?php
	if (isset ( $_SESSION ['gebruikerID'], $_SESSION ['medewerker'] )) {
		if ($_SESSION ['medewerker'] == 1) {
			?>
					<li><a href="werkpagina.php?p=Werkpagina">Werkpagina</a> <!-- sub menu -->
				<ol>
					<li><a href="accountbeheer.php?">Accountbeheer</a></li>
					<li><a href="functies.php?">Functies</a></li>
					<li><a href="werkpagina.php?p=Helpdesk">Helpdesk</a></li>
				</ol></li>
				<?php
		}
	}
	?>
			<!-- END sub menu -->
			<li><a href="info.php">Over ons</a></li>
		</ol>
	</div>
<?php
}
function getFooter() {
	?>
  <div id="footer">
  		<!-- First Column -->
		<div class="one-fourth">
			<h3>Informatie</h3>
			Team Frituurpan: team@frituurpan.nl<br>
			Noord-Nederland
			0800-3943 0239
		</div>
		<!-- Second Column -->
		<div class="one-fourth">
			<p> </p>
		</div>
		<!-- Third Column -->
		<div class="one-fourth">
			<p> </p>
		</div>
		<!-- Fourth Column -->
		<div class="one-fourth last">
			<h3>Socialize</h3>
			<a
				href="https://www.facebook.com/pages/Team-Frituurpan/1614796205458249?ref=aymt_homepage_panel">
				<img src="../img/icon_fb.png" alt="Facebook">
			</a> <a
				href="https://twitter.com/TeamFrituurpan">
				<img src="../img/icon_twitter.png" alt="Twitter"> 
			</a>
		</div>
		<div style="clear: both"></div>
	</div>
<?php
}
function getLoginWindow() {
	if (isset ( $_SESSION ['gebruikerID'], $_SESSION ['email'], $_SESSION ['login_string'] )) {
		echo '
			<div id="login">
				<table class="logintable">
					<td>
						Ingelogd als <strong>' . $_SESSION ['email'] . '</strong>
					</td>
					<td>
							<a href="profiel.php">Profiel</a>
							<br>
							<a href="logout.php">Uitloggen</a>
					</td>
				</table>
			</div>
		</div>';
	} else {
		echo '
			<div id="login">
				<form action="loginprocess.php" method="post">
					<table class="logintable">
						<td>
							E-mail: <br> 
							Wachtwoord:
						</td>
						<td>
							<input type="text" name="email"> <br> <input type="password" name="wachtwoord">
						</td>
						<td>
							<a href="registerandlogin.php">Registreren</a><br>
							<input type="submit" value="Inloggen"></td>
					</table>
				</form>
			</div>
			';
	}
}
?>
<!-- END footer -->
</body>
</html>