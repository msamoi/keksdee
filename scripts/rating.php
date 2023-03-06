<?php
    require("sqlprep.php");
    if( empty($_POST) ){ $_POST = json_decode(file_get_contents('php://input', true)); }
    if (isset($_POST))
    {
        $postID = intval($_POST);
        $conn = connectSQL();
        $select = "SELECT sum(rating) as rating FROM project_rating WHERE postID=$postID";
        $sql = mysqli_query($conn, $select);
        $row = mysqli_fetch_assoc($sql);
        $rating = $row['rating'];
        $conn->close();
        echo "$rating";
    }
?>