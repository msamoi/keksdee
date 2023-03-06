<?php
	$title = "keksdee cats";
	include("header.php");
?>
<body>
	<nav>
		<a href="index.php">Home</a>
		<a class="active" href="#">Cats</a>
		<a href="moomin.php">Moominvalley</a>
		<a href="memes.php">Memes</a>
		<a href="about.php">About</a>
		<?php
		if (isset($_SESSION['valid'])) { echo '<a id="logbtn" ">Log out</a>'; }
		else { echo '<a href="login.php">Log in</a>'; }
		?>
	</nav>
	<div class="content">
		<header>
		<img src="img/logo.svg" width="60px"><a style="font-size: 30px;"><b>Keksdee cats - the depression remedy<b></a>
		</header>
		<div>
			<?php
				include("scripts/displaycontent.php");
				$category = 'cats';
				displayPost($category);
			?>
		</div>
	</div>
	<?php
	include("footer.php");
	?>
</body>
</html>