<?php
	$title = "keksdee memes";
	include("header.php")
?>
<body>
	<nav>
		<a href="index.php">Home</a>
		<a href="cats.php">Cats</a>
		<a href="moomin.php">Moominvalley</a>
		<a class="active" href="#">Memes - misc</a>
		<a href="about.php">About</a>
		<?php
		if (isset($_SESSION['valid'])) { echo '<a id="logbtn" ">Log out</a>'; }
		else { echo '<a href="login.php">Log in</a>'; }
		?>
	</nav>
	<div class="content">
		<header>
		<img src="img/logo.svg" width="60px"><a style="font-size: 30px;"><b>Keksdee miscellaneous<b></a>
		</header>
		<div>
			<?php
				include("scripts/displaycontent.php");
				$category = 'memes';
				displayPost($category);
			?>
		</div>
	</div>
	<?php
	include("footer.php");
	?>
</body>
</html>