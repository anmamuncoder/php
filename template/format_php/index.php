<?php
$username = "AN Mamun";
$is_logged_in = true;
$users = ["Ali", "Rahim", "Karim"];
?>

<!DOCTYPE html>
<html>
<head>
    <title>PHP Home</title>
</head>
<body>

<h1>Welcome <?= $username ?></h1>

<?php if ($is_logged_in): ?>
    <p>You are logged in</p>
<?php else: ?>
    <p>Please login</p>
<?php endif; ?>

<h3>User List</h3>
<ul>
    <?php foreach ($users as $user): ?>
        <li><?= $user ?></li>
    <?php endforeach; ?>
</ul>

</body>
</html>
