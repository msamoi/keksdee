<?php
	session_start();
	$username = $_SESSION['username'];
?>
	
 <!DOCTYPE html>
<html lang="en">

<head>
	<title><?php echo $title; ?></title>
	<meta name="author" content="muumi">
	<link rel="stylesheet" href="styles/style.css">
    <link rel="icon" type="image/x-icon" href="/img/logo.png">
	<script type="module" src="scripts/main.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>