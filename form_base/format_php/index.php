<?php
$username = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (empty($_POST["username"])) {
        $error = "Username is required";
    } else {
        $username = $_POST["username"];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Username Form</title>
</head>
<body>

<form method="post">
    <label for="username">Username:</label>
    <input
        type="text"
        id="username"
        name="username"
        placeholder="Enter username"
        value="<?php echo htmlspecialchars($username); ?>"
    />
    <button type="submit">Submit</button>
</form>

<?php if ($error): ?>
    <p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?>

<?php if ($username && !$error): ?>
    <h2>Hello <?php echo htmlspecialchars($username); ?></h2>
<?php endif; ?>

</body>
</html>
