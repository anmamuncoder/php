<!DOCTYPE html>
<html>
<head>
    <title>User List</title>
</head>
<body>

<h2>Add User</h2>

<form method="post">
    <input name="name" placeholder="Name" required>
    <input name="email" placeholder="Email" required>
    <button>Add</button>
</form>

<h3>Users</h3>
<ul>
<?php foreach ($users as $u): ?>
    <li>
        <?= htmlspecialchars($u['name']) ?> –
        <?= htmlspecialchars($u['email']) ?>
    </li>
<?php endforeach; ?>
</ul>

</body>
</html>
