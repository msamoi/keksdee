<?php
	$title = "keksdee home";
	include("header.php");
?>
<body>
	<nav>
		<a class="active" href="#">Home - top posts</a>
		<a href="cats.php">Cats</a>
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
			<img src="img/logo.svg" width="60px"><a style="font-size: 30px;"><b>Keksdee - for your comedic needs<b></a><br>
		</header>
		<?php
				include("scripts/displaycontent.php");
				$category = '';
				displayPost($category); // use displayPost function to display all relevant posts in order of rating
			?>
	</div>
	<?php
	include("footer.php");
	?>
</body>
</html>