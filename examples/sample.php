<?php
require_once( "../src/azi/Validator.php" );
use azi\Validator;
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Validator - the Server side form validation library</title>
</head>
<body>

<form method="post" action="view.php">
    <p>
        <label>Email :
            <input type="text" name="email">
        </label>
        <?= Validator::error('email') ? Validator::error('email') : ""; ?>
    </p>
    <p>
        <label>Age :
            <input type="text" name="age">
        </label>
        <?= Validator::error('age') ? Validator::error('age') : ""; ?>
    </p>
    <p>
        <input type="submit" name="submit">
    </p>
</form>

</body>
</html>

