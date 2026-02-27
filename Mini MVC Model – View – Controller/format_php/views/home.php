<!-- in django templates/home.html -->

<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
</head>
<body>

<h1>Welcome <?= htmlspecialchars($username) ?></h1>

<?php if ($is_logged_in): ?>
    <p>You are logged in</p>
<?php else: ?>
    <p>Please login</p>
<?php endif; ?>

<ul>
<?php foreach ($users as $u): ?>
    <li><?= htmlspecialchars($u) ?></li>
<?php endforeach; ?>
</ul>

</body>
</html>
