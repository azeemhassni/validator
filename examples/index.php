<?php
require_once( "../src/azi/Validator.php" );
use azi\Validator;
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Validator - the Server side form validation library</title>
    <style>
        body {
            font-family: "Segoe UI";
        }
        input[type=text] {
            padding: 5px 6px;
            border: 1px solid #ccc;
            display: block;
        }

        .error {
            color: red;
        }

        p {
            background: ghostwhite;
            padding: 20px;
        }

        input[type=submit] {
            background: cadetblue;
            border: 1px solid #fff;
            padding: 7px 20px;
            color: #fff;
            cursor: pointer;
        }

        h2 {
            font-size: 20px;
            text-align: center;
            background: #333;
            color: #fff;
            padding: 10px 0;
        }

    </style>
</head>
<body>
<h2>Validator - the server side form validation library </h2>
<form method="post" action="submit.php">
    <p>
        <label>Email :
            <input type="text" name="email">
        </label>
        <?= Validator::error('email') ? Validator::error('email', '<span class="error">:message</span>') : ""; ?>
    </p>
    <p>
        <label>Password :
            <input type="text" name="password">
        </label>
        <?= Validator::error('password') ? Validator::error('password', '<span class="error">:message</span>') : ""; ?>
    </p>
    <p>
        <label>Confirm Password :
            <input type="text" name="confirm_password">
        </label>
        <?= Validator::error('confirm_password') ? Validator::error('confirm_password', '<span class="error">:message</span>') : ""; ?>
    </p>
    <p>
        <input type="submit" name="submit">
    </p>
</form>

</body>
</html>

