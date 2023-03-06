<?php
	$title = "keksdee login";
	include("header.php");
    include ("scripts/sqlprep.php");

    $error = '';
    $email = '';
    $username = '';
    $password = '';
    if (isset($_POST["signup"])) // form handling for user account creation
    {
        if (empty($_POST["email"]))
        {
            $error .= "E-mail cannot be empty!<br>";
        }
        else
        {
            $email = cleanInp($_POST["email"]);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) // email validation
            {
                $error .= "Invalid E-mail address!<br>";
            }
        }

        if (empty(trim($_POST["username"])))
        {
            $error .= "Username cannot be empty!<br>";
        }
        else
        {
            $username = cleanInp($_POST["username"]);
            if (!preg_match('/[A-Za-z0-9_-]{4,10}$/', $username)) // username check
            {
                $error .= "Username does not match format!<br>";
            }
        }

        if (empty(trim($_POST["password"])))
        {
            $error .= "Password cannot be empty!<br>";
        }
        else
        {
            if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/', cleanInp($_POST["password"]))) // password check
            {
                $error .= "Username does not match format!<br>";
            }
            else $password = password_hash(cleanInp($_POST["password"]), PASSWORD_DEFAULT); // if password is okay, hash it
        }
        if ($error == '') { $error = 'success';}
    }
?>
<body>
	<nav>
		<a href="index.php">Home</a>
		<a href="cats.php">Cats</a>
		<a href="moomin.php">Moominvalley</a>
		<a href="memes.php">Memes</a>
		<a href="about.php">About</a>
	</nav>
	<div class="content">
		<header>
			<img src="img/logo.svg" width="60px"><a style="font-size: 30px;"><b>Log in<b></a><br>
		</header>
		<div>
			<br>
			<form method="post" action="signup.php" id="loginform">
				<label for="email">E-mail address:</label><br>
				<input type="email" id="email" name="email" required><br>
				<label for="username">Username (letters, numbers, dashes and underscores only. 4-10 characters):</label><br>
				<input type="text" id="username" name="username" pattern="[A-Za-z0-9_-]{4,10}$" required><br>
				<label for="password">Enter a password at least 8 characters in length, with at least 1 letter and 1 number:</label><br>
				<input type="password" id="password" name="password" pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$" required><br>
				<input type="submit" id="signup" name="signup" value="Sign up">
			</form>
			<br>
		</div>
	</div>

	<?php
    if ($error == 'success')
    {
        $conn = connectSQL();
        $stmt = $conn->prepare("INSERT INTO project_users (email, username, password) VALUES (?, ?, ?)"); // prepared statement due to user input
        if ($stmt === false)
        {
            die('<div id="error">prepare() failed: ' . htmlspecialchars($conn->error));
        }
        $stmt->bind_param("sss", $email, $username, $password);
        $rc = $stmt->execute();
        if ($rc === false)
        {
            die('<div id="error">Account creation failed: ' . htmlspecialchars($conn->error)); // if it fails for whatever reason (like account already exists), error
        }
		$stmt->close();

		$stmt = $conn->prepare("INSERT INTO project_userdata (username) VALUES (?)"); // also create entry into project_userdata for the account
		if ($stmt === false)
        {
            die('<div id="error">prepare() failed: ' . htmlspecialchars($conn->error));
        }
		$stmt->bind_param("s", $username);
		$rc = $stmt->execute();
		if ($rc === false)
        {
            die('<div id="error">Account creation failed: ' . htmlspecialchars($conn->error));
        }
        else
        {
        printf('<div id="success">Welcome %s, your account has been made.</div>', $username);
        header("Location: login.php");
        }
        $stmt->close();
        $conn->close();
    }

    else if ($error != '')
    {
        printf('<div id="error">Your account could not be made due to the following errors:<br>%s</div>', $error); // if errors occurred, print them
    }
?>

<?php
	include("footer.php");
?>
</body>
</html>