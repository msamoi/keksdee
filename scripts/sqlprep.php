<?php
    function connectSQL() // function to not duplicate the same SQL connection procedure everywhere
    {
        $servername = '';
        $servuser = '';
        $servpw = '';
        $dbname = '';
    
        $conn = new mysqli($servername, $servuser, $servpw, $dbname);
        if ($conn ->connect_error)
        {
            die("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    }

    function cleanInp($inp) // function to clean up user input for multiple pages
    {
        $inp = trim($inp);
        $inp = stripslashes($inp);
        $inp = htmlspecialchars($inp);
        return $inp;
    }
    ?>