<?php
$title ="Post";
	include("header.php");
	include("scripts/displaycontent.php");

    function showPost()
    {
        $foundpost = $_POST['postID'];
        if ($foundpost) {
            $conn = connectSQL();
            $select = "SELECT category, user, title, image, project_posts.postID, a.rating
                        FROM project_posts
                        LEFT JOIN(
                            SELECT project_posts.postID, sum(rating) as rating
                            FROM project_posts
                            LEFT JOIN project_rating ON project_posts.postID = project_rating.postID
                            GROUP BY postID
                            ) a ON project_posts.postID = a.postID
                        WHERE project_posts.postID = $foundpost";
            $sql = mysqli_query($conn, $select);
            $row = mysqli_fetch_assoc($sql);
            $conn->close();
        }
        return $row;
    }

    if (isset($_POST['comments'])) $row = showPost(); // function to find the post based on its post ID
    else if (isset($_POST["sndcom"])) // function to add comments to posts
    {
        $row = showPost();
        $foundpost = $row['postID'];
        $comment = cleanInp($_POST["comment"]);
        if(!preg_match("/^[A-Za-z\d.,:;!?' \\-(\r\n|\r|\n)]{1,500}$/", $comment))
        {
            printf('<div id="error">Comment does not match format!</div>');
        }
        else if (!$username) echo '<div id="error">Must be logged in to comment!</div>';
        else if (!$foundpost) echo '<div id="error">Post id not found!</div>';
        else
        {
            $comment = nl2br($comment);
            $postID = intval($foundpost); // does not work for some reason, is NULL as of right now
            $conn = connectSQL();
            $update = "INSERT INTO project_comments (userID, comment, postID)
                        VALUES ((SELECT userID FROM project_users WHERE username = ?), ?, ?)";
            $stmt = $conn->prepare($update);
            if ($stmt === false)
            {
                die('<div id="error">prepare() failed: ' . htmlspecialchars($conn->error));
            }
            $stmt->bind_param("ssi", $username, $comment, $postID);
            $rc = $stmt->execute();
            if ($rc === false)
            {
                die('<div id="error">Error adding comment: ' . htmlspecialchars($conn->error));
            }
            else printf('<div id="success">Comment added!</div>');
            $stmt->close();
            $conn->close();
        }
    }
    else echo '<div id="error">Post not found!</div>';
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
        <h1>Post</h1>
		<?php // displaying the post itself
        echo '<div class="containmeme">
                <a class="title">'.$row['title'].'</a><br>
                <a>Posted by: </a>
                <form action="user.php" method="post">
                <a href="user.php"><input type="submit" name="user" value="'.$row['user'].'"></a>
                <input type="hidden" name="username" value="'.$row['user'].'"></form><br>
                <img src='.$row['image'].' class="meme" alt="an amusing image"><br>
                <form class="rate" method="post" enctype="multipart/form-data">
                <a class="upvote">&#8679</a><br>
                <a class="rating">'.$row['rating'].'</a><br>
                <a class="downvote">&#8681</a>
                <input type="hidden" name="postID" value="'.$row['postID'].'">
                </form></div>';

        $selectcomm = "SELECT project_comments.userID, comment, postID, username
        FROM project_comments
        LEFT JOIN project_users ON project_comments.userID = project_users.userID
        WHERE postID =".$row['postID'];
        $conn = connectSQL();
        $sqlcomm = mysqli_query($conn, $selectcomm);
        while ($rowcomm = mysqli_fetch_assoc($sqlcomm)) // displaying comments under post
        {
            echo '<div class="containmeme">
            <form action="user.php" method="post">
            <a href="user.php"><input type="submit" class="user" name="user" value="'.$rowcomm['username'].'"></a><br>
            <input type="hidden" name="username" value="'.$rowcomm['username'].'"></form><br>
            <a>'.$rowcomm['comment'].'</a></div><br>';
        }
        $conn->close();
		?>
            <form action="" method="post" class="containmeme">
                <input type="text" id="comment" name="comment" placeholder="Write a comment!" pattern="^[A-Za-z0-9.,:;!?' \\-]{1,200}$">
                <input type="submit" id="sndcom" name="sndcom">
                <input type="hidden" name="postID" value="<?php echo $row['postID']; ?>">
            </form>
	</div>
	<?php
	include("footer.php");
	?>
</body>
</html>