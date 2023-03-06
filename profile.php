<?php
    $title ="Your profile";
    include("header.php");
	if (!isset($_SESSION['valid']))
	{
		header('Location: index.php');
		exit;
	}
	include("scripts/displaycontent.php");

	$conn = connectSQL();
	$select = "SELECT * FROM project_userdata WHERE username='$username'"; // getting user data from sql db
	$sql = mysqli_query($conn, $select);
	$row = mysqli_fetch_assoc($sql);
	$conn->close();

	if (isset($_POST["update"])) // form checking to update user aboutme text
	{
		$about = cleanInp($_POST["about"]);
		if(!preg_match("/^[A-Za-z0-9.,:;!?' \\-(\r\n|\r|\n)]{1,500}$/", $about))
		{
			printf('<div id="error">About text does not match format!</div>');
		}
		else
		{
			$about = nl2br($about); // converting newline chars to <br>s to allow paragraphs in aboutme
			$conn = connectSQL();
			$update = "UPDATE project_userdata SET about=? WHERE username=?";
			$stmt = $conn->prepare($update); // prepared statements whenever user input is taken
			if ($stmt === false)
			{
				die('<div id="error">prepare() failed: ' . htmlspecialchars($conn->error));
			}
			$stmt->bind_param("ss", $about, $username);
			$rc = $stmt->execute();
			if ($rc === false)
			{
				die('<div id="error">Error updating profile: ' . htmlspecialchars($conn->error));
			}
			else printf('<div id="success">Profile updated!</div>');
			$stmt->close();
			$conn->close();
			header("Refresh:1"); // refresh page to load new aboutme text
		}
	}

	$error = '';
	if(isset($_POST["imgup"])) // handling the form to upload user profile picture
	{
		$imagefile = $_FILES["fileToUpload"]["tmp_name"];
		$imageFileType = strtolower(pathinfo(basename($_FILES["fileToUpload"]["name"]),PATHINFO_EXTENSION));
		$check = getimagesize($imagefile); // using getimagesize to check if file is an image
		if(!$check)
		{
			$error .= 'File is not an image.<br>';
		}

		if ($_FILES["fileToUpload"]["size"] > 500000)
		{
			$error .= 'File too large.<br>';
		}
		
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" )
		{
			$error .= 'Wrong file type. jpg, png, jpeg or gif only.<br>';
		}

		if ($error == '')
		{
			$tmpfile = base64_encode(file_get_contents($imagefile)); // encoding image in base64 to store as string in SQL
			$image = 'data:image/'.$imageFileType.';base64,'.$tmpfile;
			$conn = connectSQL();
			$update = "UPDATE project_userdata SET profpic=? WHERE username=?";
			$stmt = $conn->prepare($update);
			if ($stmt === false)
			{
				die('<div id="error">prepare() failed: ' . htmlspecialchars($conn->error));
			}
			$stmt->bind_param("ss", $image, $username);
			$rc = $stmt->execute();
			if ($rc === false)
			{
				die('<div id="error">Error updating profile: ' . htmlspecialchars($conn->error));
			}
			else printf('<div id="success">Profile updated!</div>');
			$stmt->close();
			$conn->close();
		}
		else echo '<div id="error">'.$error.'</div>';
	}
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
        <h1>Profile of <?php echo $_SESSION["username"]; ?></h1>
		<img id="profpic" src=

        <?php
		if ($row['profpic']) echo $row['profpic'];
		else echo '"img/placeholder.png"';
		?>

		height="300" width="300" alt="Your profile picture">
		<form action="profile.php" method="post" enctype="multipart/form-data">
  			<label for="fileToUpload">Select image to upload:</label><br>
  			<input type="file" name="fileToUpload" id="fileToUpload" required><br>
  			<input type="submit" value="Upload Image" name="imgup" id="imgup">
		</form>
		<br>

		<?php
			if ($row['about']) echo '<div class="about">' . $row['about'] . "</div>";
			else echo '<div class="about">Whatever you choose to display goes here!</div>';
		?>

		<br><form action="profile.php" method="post">
			<label for="about">Write about yourself!</label><br>
			<textarea rows="5" cols="50" id="about" name="about" pattern="^[A-Za-z.,:;!?' \\-]{1,500}$"></textarea><br>
			<input type="submit" id="update" name="update" value="Update">
		</form>

		<?php
			echo '<br><div class="containmeme"><a class="title">'.$row['username']."'s posts:</a></div>";
			userPost($row["username"]);
		?>
	</div>
	<?php
	include("footer.php");
	?>
</body>
</html>