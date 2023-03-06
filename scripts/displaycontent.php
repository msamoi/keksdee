<?php
    include ("sqlprep.php");
    include ("../header.php");

    if (isset($_POST['vote']))
    {
        $postID = intval($_POST['postID']);
        $rating = intval($_POST['rating']);
        ratePost($postID, $rating, $username);
    }

    function ratePost($postID, $rating, $username) // function that rates a post based on given input
    {
        if ($rating < 2 && $rating > -2)
        {
            $conn = connectSQL();
            $select = "SELECT userID FROM project_users WHERE username='$username'"; // finding user under which to post the rating
            $sql = mysqli_query($conn, $select);
            $row = mysqli_fetch_assoc($sql);
            $userID = intval($row['userID']);
            if ($userID == 0) die('<div id="error">Must be logged in to vote!</div>'); // can not rate if user not found

            $check = mysqli_query($conn, "SELECT * FROM project_rating WHERE postID= $postID AND userID= $userID"); // checking if rating exists to not rate twice
            if (mysqli_num_rows($check) != 0)
            {
                $rate = "UPDATE project_rating SET rating=$rating WHERE postID= $postID AND userID= $userID"; // if exists, update old record
            }
            else $rate = "INSERT INTO project_rating (postID, rating, userID) VALUES ($postID, $rating, $userID)"; // if it doesn't, make new record

            if ($conn->query($rate) === TRUE)
            {
                echo '<div id="success">Record updated successfully</div>';
            }
            else
            {
                echo '<div id="error">Error updating record: ' . $conn->error;
            }
        }
        else  echo '<div id="error">Invalid rating!</div>';
        $conn->close();
    }

    function displayPost($category) // function to display posts dynamically based on category
    {
        $conn = connectSQL();
        if ($category != '') $select = "SELECT category, user, title, image, project_posts.postID, a.rating
        FROM project_posts
        LEFT JOIN(
          SELECT project_posts.postID, sum(rating) as rating
          FROM project_posts
          LEFT JOIN project_rating ON project_posts.postID = project_rating.postID
          GROUP BY postID
        ) a ON project_posts.postID = a.postID
        WHERE category='$category'
        ORDER BY rating DESC";

        // two separate SQL statements, one for when category isn't specified (single, dynamic statement was buggy)

        else $select = "SELECT category, user, title, image, project_posts.postID, a.rating
        FROM project_posts
        LEFT JOIN(
          SELECT project_posts.postID, sum(rating) as rating
          FROM project_posts
          LEFT JOIN project_rating ON project_posts.postID = project_rating.postID
          GROUP BY postID
        ) a ON project_posts.postID = a.postID
        ORDER BY rating DESC";

        $sql = mysqli_query($conn, $select);
        if (mysqli_num_rows($sql)==0) echo '<div id="error">No posts found in this category!</div>';
        else{
            while($row = mysqli_fetch_assoc($sql)) // displaying the post records from the SQL database as posts
            {
                echo '<div class="containmeme">
                <a class="title">'.$row['title'].'</a><br>
                <a>Posted by: </a>
                <form action="user.php" method="post">
                <a href="user.php"><input type="submit" class="user" name="user" value="'.$row['user'].'"></a>
                <input type="hidden" name="username" value="'.$row['user'].'"></form><br>
                <img src='.$row['image'].' class="meme" alt="an amusing image"><br>
                <form class="rate" method="post" enctype="multipart/form-data">
                <a class="upvote">&#8679</a><br>
                <a class="rating">'.$row['rating'].'</a><br>
                <a class="downvote">&#8681</a>
                <input type="hidden" name="postID" value="'.$row['postID'].'">
                </form>
                <form class="comment" method="post" action="post.php">
                <input type="submit" name="comments" value="Comments">
                <input type="hidden" name="postID" value="'.$row['postID'].'">
                </form>
                </div>';
            }
        }
        $conn->close();
    }

    function userPost($founduser) // same principle as displaying posts based on category, but based on selected user instead.
    {
        $conn = connectSQL();
        $select = "SELECT category, user, title, image, project_posts.postID, a.rating
        FROM project_posts
        LEFT JOIN(
          SELECT project_posts.postID, sum(rating) as rating
          FROM project_posts
          LEFT JOIN project_rating ON project_posts.postID = project_rating.postID
          GROUP BY postID
        ) a ON project_posts.postID = a.postID
        WHERE user='$founduser'
        ORDER BY rating DESC";

        $sql = mysqli_query($conn, $select);
        if (mysqli_num_rows($sql)==0) echo '<div id="error">No posts found in this category!</div>';
        else{
            while($row = mysqli_fetch_assoc($sql))
            {
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
                </form>
                <form class="comment" method="post" action="post.php">
                <input type="submit" name="comments" value="Comments">
                <input type="hidden" name="postID" value="'.$row['postID'].'">
                </form>
                </div>';
            }
        }
        $conn->close();
    }
?>