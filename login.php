<?php
function findUser($username, $conn) // function to find existing user from database and return its password hash
{
	$password = false;
	$sql = "SELECT password FROM project_users WHERE username=?";
	$stmt = $conn->prepare($sql);
	if ($stmt === false)
	{
		die('prepare() failed: ' . htmlspecialchars($conn->error));
	}
	$stmt->bind_param("s", $username);
	$rc = $stmt->execute();
	if ($rc === false)
	{
		die('execute() failed: ' . htmlspecialchars($conn->error));
	}
	$result = $stmt->get_result();
	if ($result) // if user was found, find the associated password
	{
		$row = $result->fetch_assoc();
		$password = $row['password'];
	}
	return $password; // return password
}

$title = "keksdee login";
include("header.php");
include("scripts/sqlprep.php");

$error = '';
$username = '';
$password = '';
if (isset($_POST["login"])) // checking user input for login
{   
    if (empty(trim($_POST["username"])))
    {
        $error .= "Username cannot be empty!<br>";
    }
    else
    {
        $username = cleanInp($_POST["username"]);
        if (!preg_match('/[A-Za-z0-9_-]{4,10}$/', $username)) // name format checks
        {
                $error .= "Username does not match format!";
        }
    }

    if (empty(trim($_POST["password"])))
    {
        $error .= "Password cannot be empty!<br>";
    }
    else
    {
		$password = cleanInp($_POST["password"]);
        if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/', $password)) // password format checks
        {
            $error .= "Username does not match format!";
        }
    }

    if ($error == '')
	{
		$error = 'success';
        $conn = connectSQL();

		if (password_verify($password, findUser($username, $conn))) // compare entered password hash to one in database with php standard function
		{
			echo '<div id="success">User found!</div>'; // if user found, start session and write username to session variables
			$_SESSION['valid'] = true;
			$_SESSION['timeout'] = time();
			$_SESSION['username'] = $username;
			header('Location: profile.php');
			exit;
		}
		else echo '<div id="error">User not found!</div>';
		$conn->close();
	}
	else echo '<div id="error">', $error, '</div>';
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
			<img src="img/logo.svg" width="60px"><a style="font-size: 30px;"><b>Log in<b></a><br>
		</header>
		<div>
			<br>
			<form id="loginform" action="login.php" method="post">
				<label for="username">Username:</label><br>
				<input type="text" id="username" name="username" pattern="[A-Za-z0-9_-]{4,10}$" required><br>
				<label for="password">Password:</label><br>
				<input type="password" id="password" name="password" pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$" required><br>
				<input type="submit" id="login" name="login" value="Log in">
			</form>
			<br>
			<a>Not registered?</a><a href="signup.php">Sign up</a>
		</div>
<?php
	include("footer.php");
?>
</body>
</html>