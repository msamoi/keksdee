<?php
$title = "Logging out";
include("header.php");
?>

<div class="content">Logging out</div>

<?php
session_destroy();
header("Location: index.php");
include("footer.php")
?>