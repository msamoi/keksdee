<?php
$title = "Upload an image";
include("header.php");
if (!isset($_SESSION['valid']))
{
    header('Location: index.php');
    exit;
}
include("scripts/sqlprep.php");

$error = '';
$imagefile = $_FILES["fileToUpload"]["tmp_name"];
$imageFileType = strtolower(pathinfo(basename($_FILES["fileToUpload"]["name"]),PATHINFO_EXTENSION));
$goodcat = ['cats', 'moomin', 'memes'];
if(isset($_POST["imgup"])) // checking uploaded image format, size and filetype, also if category and title exist
{
    $check = getimagesize($imagefile);
    if($check === false)
    {
        $error .= 'File is not an image.<br>';
    }

    if ($_FILES["fileToUpload"]["size"] > 5000000)
    {
        $error .= 'File too large.<br>';
    }
    
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" )
    {
        $error .= 'Wrong file type. jpg, png, jpeg or gif only.<br>';
    }

    if (!isset($_POST['category']))
    {
        $error .= 'Category must not be empty!';
    }
    else
    {
        $category = $_POST['category'];
        if (!in_array($category, $goodcat))
        {
            $error .= 'Category must match options!';
        }
    }

    if (!isset($_POST['title']))
    {
        $error .= 'Title must not be empty!';
    }
    else
    {
        $title = cleanInp($_POST['title']);
        if (!preg_match("/^[A-Za-z.,:;!?()' \\-]{3,100}$/", $title))
        {
            $error .= 'Title must match format!';
        }
    }

    if ($error == '')
    {
        $tmpfile = base64_encode(file_get_contents($imagefile)); // encoding image to base64 to store as string in SQL database
        $image = 'data:image/'.$imageFileType.';base64,'.$tmpfile;
        $conn = connectSQL();
        $stmt = $conn->prepare("INSERT INTO project_posts (category, user, title, image) VALUES (?, ?, ?, ?)"); // creating record in posts table
        if ($stmt === false)
        {
            die('<div id="error">prepare() failed: ' . htmlspecialchars($conn->error));
        }
        $stmt->bind_param("ssss", $category, $username, $title, $image);
        $rc = $stmt->execute();
        if ($rc === false)
        {
            die('<div id="error">execute() failed: ' . htmlspecialchars($conn->error));
        }
        $postID = intval($conn->insert_id);

        // also adding a rating from the poster themselves to the ratings table to prevent null ratings
        $stmt = $conn->prepare("INSERT INTO project_rating (postID, rating, userID) VALUES (?, ?, (SELECT userID from project_users WHERE username=?))");
        if ($stmt === false)
        {
            die('<div id="error">prepare() failed: ' . htmlspecialchars($conn->error));
        }
        $rating = 1; // setting a variable because bind_param does not accept values as arguments
        $stmt->bind_param("iis", $postID, $rating, $username);
        $rc = $stmt->execute();
        if ($rc === false)
        {
            die('<div id="error">execute() failed: ' . htmlspecialchars($conn->error));
        }
        else echo '<div id="success">'."The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded.</div>";
		$stmt->close();
        $conn->close();
    }
    else echo '<div id="error">'.$error.'</div>';
}
?>

<body>
	<nav>
		<a href="index.php">Home</a>
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
			<img src="img/logo.svg" width="60px"><a style="font-size: 30px;"><b>Upload an image<b></a><br>
		</header>
		<div>
			<br>
			<form id="postimg" action="upload.php" method="post" enctype="multipart/form-data">
                <label for="fileToUpload">Select image to upload:</label><br>
                <input type="file" name="fileToUpload" id="fileToUpload" required><br>
                <label for="category">Select post category:</label><br>
                <input type="radio" id="cats" name="category" value="cats" required>
                <label for="cats">Cats</label><br>
                <input type="radio" id="moomin" name="category" value="moomin" required>
                <label for="moomin">Moomin</label><br>
                <input type="radio" id="memes" name="category" value="memes" required>
                <label for="memes">Memes</label><br>
                <label for="title">Think of a witty title for your post!</label><br>
                <input type="text" id="title" name="title" pattern="^[A-Za-z.,:;!?'() \\-]{3,100}$" required><br>
                <input type="submit" value="Upload Image" name="imgup" id="imgup">
			</form>
			<br>
		</div>
<?php
	include("footer.php");
?>
</body>
</html>