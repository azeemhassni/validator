<?php
require_once( "../src/Validator.php" );
use azi\Validator;
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Server side form validation</title>
</head>
<body>

<form method="post" action="view.php">
    <p>
        <label>Name :
            <input type="text" name="name">
        </label>
        <?= Validator::error('name') ? Validator::error('name') : ""; ?>
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

