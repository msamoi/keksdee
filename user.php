<?php
    $title ="Your profile";
	include("header.php");
	include("scripts/displaycontent.php");

    if (isset($_POST['user']))
    {
        $founduser = $_POST['username'];
        if ($founduser)
        {
            $conn = connectSQL();
            $select = "SELECT * FROM project_userdata WHERE username='$founduser'";
            $sql = mysqli_query($conn, $select);
            $row = mysqli_fetch_assoc($sql);
        }
        else echo '<div id="error">User not found!</div>';
    }
    else header("Location: index.php");
?>

<body>
	<nav>
		<a href="index.php">Home - top posts</a>
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
        <h1>Profile of <?php echo $row['username']; ?></h1>
		<img id="profpic" src=

        <?php
		if ($row['profpic']) echo $row['profpic'];
		else echo '"img/placeholder.png"';
		?>

		height="300" width="300" alt="This user's profile picture">
		<br>
		<?php
			if ($row['about']) echo '<div class="about">' . $row['about'] . "</div>";
			else echo '<div class="about">This user has not written about themselves yet!</div>';
			echo '<br><div class="containmeme"><a class="title">'.$row['username']."'s posts:</a></div>";
			userPost($row["username"]);
		?>
	</div>
	<?php
	include("footer.php");
	?>
</body>
</html>