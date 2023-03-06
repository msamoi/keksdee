<footer>
    <?php
        if (isset($_SESSION['valid']) and $_SESSION['valid'])
        {
            echo '<div id="userdata">Logged in as ', $_SESSION['username'], ' <a href="profile.php">Profile</a><a href="upload.php">Upload an image!</div>';
        }
        else { echo '<div id="userdata">Not logged in</div>'; }
    ?>
</footer>
