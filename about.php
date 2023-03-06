<?php
	$title = "About keksdee";
	include("header.php");
?>

<body>
	<nav>
		<a href="index.php">Home</a>
		<a href="cats.php">Cats</a>
		<a href="moomin.php">Moominvalley</a>
		<a href="memes.php">Memes</a>
		<a class="active" href="#">About</a>
		<?php
		if (isset($_SESSION['valid'])) { echo '<a id="logbtn" ">Log out</a>'; }
		else { echo '<a href="login.php">Log in</a>'; }
		?>
	</nav>
	<div class="content">
		<header>
			<img src="img/logo.svg" width="60px"><a style="font-size: 30px;"><b>Keksdee - for your comedic needs<b></a><br>
		</header>
		<div class="about">
		<img src="img/logo.svg" width="150px"><a style="font-size: 30px;">
		<a>Keksdee is a work-in-progress image sharing website. It is being worked on by Helena Veebel and Mark Samoilov from the Tallinn University of Technology. Future planned
                functionality includes image feedback such as comments and upvotes/downvotes, along with customizable user account profiles. There is also a dire need for a web
				designer.
            </a>
		</div>
	</div>
	<?php
	include("footer.php");
	?>
</body>
</html>