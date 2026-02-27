# PHP Template vs Django Template - Complete Guide

Step-by-step explanation of PHP templating for Django developers.

---

## 📋 Table of Contents

1. [Data Section](#1-data-section)
2. [Print Variables](#2-print-variables)
3. [Conditional Rendering](#3-conditional-rendering)
4. [Loops](#4-loops)
5. [Security & Escaping](#5-security--escaping)
6. [Complete Examples](#6-complete-examples)
7. [Quick Reference](#7-quick-reference)
8. [Key Differences](#8-key-differences)
9. [Next Steps](#9-next-steps)

---

## 1️⃣ Data Section

### PHP
```php
<?php
// Data at top of file
$username = "AN Mamun";
$is_logged_in = true;
$users = ["Ali", "Rahim", "Karim"];
?>
```

### Django (views.py)
```python
def my_view(request):
    context = {
        "username": "AN Mamun",
        "is_logged_in": True,
        "users": ["Ali", "Rahim", "Karim"]
    }
    return render(request, 'template.html', context)
```

**📌 Key Difference:**
- PHP: Data and template in **same file**
- Django: Data in **views.py**, template **separate**

---

## 2️⃣ Print Variables

### PHP - Method 1 (Short Echo)
```php
<h1>Welcome <?= $username ?></h1>
```

### PHP - Method 2 (Full Echo)
```php
<h1>Welcome <?php echo $username; ?></h1>
```

### Django Template
```html
<h1>Welcome {{ username }}</h1>
```

**📌 Comparison:**
| PHP | Django | Notes |
|-----|--------|-------|
| `<?= $var ?>` | `{{ var }}` | Short form (preferred) |
| `<?php echo $var; ?>` | `{{ var }}` | Full form |
| `$var` | `var` | No $ in Django |

---

## 3️⃣ Conditional Rendering (if / else)

### PHP - Style 1 (Alternative Syntax)
```php
<?php if ($is_logged_in): ?>
    <p>You are logged in</p>
<?php else: ?>
    <p>Please login</p>
<?php endif; ?>
```

### PHP - Style 2 (Curly Braces)
```php
<?php if ($is_logged_in) { ?>
    <p>You are logged in</p>
<?php } else { ?>
    <p>Please login</p>
<?php } ?>
```

### Django Template
```html
{% if is_logged_in %}
    <p>You are logged in</p>
{% else %}
    <p>Please login</p>
{% endif %}
```

**📌 PHP Conditional Syntax:**
```php
// Alternative syntax (recommended for templates)
if ($condition):
    // code
endif;

// Standard syntax
if ($condition) {
    // code
}
```

### More Conditional Examples

#### PHP: elseif
```php
<?php if ($role === "admin"): ?>
    <p>Admin Panel</p>
<?php elseif ($role === "user"): ?>
    <p>User Dashboard</p>
<?php else: ?>
    <p>Guest View</p>
<?php endif; ?>
```

#### Django: elif
```html
{% if role == "admin" %}
    <p>Admin Panel</p>
{% elif role == "user" %}
    <p>User Dashboard</p>
{% else %}
    <p>Guest View</p>
{% endif %}
```

#### PHP: Multiple Conditions
```php
<?php if ($is_logged_in && $is_verified): ?>
    <p>Welcome verified user!</p>
<?php endif; ?>

<?php if ($age >= 18 || $has_permission): ?>
    <p>Access granted</p>
<?php endif; ?>
```

#### Django: Multiple Conditions
```html
{% if is_logged_in and is_verified %}
    <p>Welcome verified user!</p>
{% endif %}

{% if age >= 18 or has_permission %}
    <p>Access granted</p>
{% endif %}
```

---

## 4️⃣ Loops

### PHP: foreach Loop
```php
<ul>
<?php foreach ($users as $user): ?>
    <li><?= $user ?></li>
<?php endforeach; ?>
</ul>
```

### Django: for Loop
```html
<ul>
{% for user in users %}
    <li>{{ user }}</li>
{% endfor %}
</ul>
```

### Loop with Index

#### PHP: foreach with Key
```php
<?php foreach ($users as $index => $user): ?>
    <li><?= $index + 1 ?>. <?= $user ?></li>
<?php endforeach; ?>
```

#### Django: forloop.counter
```html
{% for user in users %}
    <li>{{ forloop.counter }}. {{ user }}</li>
{% endfor %}
```

### Loop with Array/Object

#### PHP: Associative Array
```php
<?php
$user_data = [
    "name" => "Ali",
    "age" => 25,
    "city" => "Dhaka"
];
?>

<?php foreach ($user_data as $key => $value): ?>
    <p><?= $key ?>: <?= $value ?></p>
<?php endforeach; ?>
```

#### Django: Dictionary
```python
# views.py
user_data = {
    "name": "Ali",
    "age": 25,
    "city": "Dhaka"
}
```

```html
<!-- template.html -->
{% for key, value in user_data.items %}
    <p>{{ key }}: {{ value }}</p>
{% endfor %}
```

### Loop with Objects

#### PHP: Array of Objects
```php
<?php
$users = [
    ["name" => "Ali", "age" => 25],
    ["name" => "Rahim", "age" => 30],
    ["name" => "Karim", "age" => 28]
];
?>

<?php foreach ($users as $user): ?>
    <div>
        <h3><?= $user["name"] ?></h3>
        <p>Age: <?= $user["age"] ?></p>
    </div>
<?php endforeach; ?>
```

#### Django: List of Dictionaries
```python
# views.py
users = [
    {"name": "Ali", "age": 25},
    {"name": "Rahim", "age": 30},
    {"name": "Karim", "age": 28}
]
```

```html
<!-- template.html -->
{% for user in users %}
    <div>
        <h3>{{ user.name }}</h3>
        <p>Age: {{ user.age }}</p>
    </div>
{% endfor %}
```

### Empty Check in Loop

#### PHP: Check Empty Array
```php
<?php if (empty($users)): ?>
    <p>No users found</p>
<?php else: ?>
    <?php foreach ($users as $user): ?>
        <li><?= $user ?></li>
    <?php endforeach; ?>
<?php endif; ?>
```

#### Django: for...empty
```html
<ul>
{% for user in users %}
    <li>{{ user }}</li>
{% empty %}
    <li>No users found</li>
{% endfor %}
</ul>
```

**📌 Django Advantage:** Built-in `{% empty %}` tag!

---

## 5️⃣ Security & Escaping

### Why Escaping Matters

**Without escaping (DANGEROUS!):**
```php
// If $username = "<script>alert('XSS')</script>"
<?= $username ?> 
<!-- Browser executes the script! -->
```

### PHP: Manual Escaping

#### htmlspecialchars() Function
```php
<!-- SAFE -->
<h1><?= htmlspecialchars($username) ?></h1>
<li><?= htmlspecialchars($user) ?></li>
<p><?= htmlspecialchars($comment, ENT_QUOTES, 'UTF-8') ?></p>
```

**Parameters:**
```php
htmlspecialchars(
    $string,           // String to escape
    ENT_QUOTES,        // Escape both " and '
    'UTF-8'            // Character encoding
)
```

### Django: Auto-Escaping

```html
<!-- SAFE by default -->
<h1>{{ username }}</h1>
<li>{{ user }}</li>
<p>{{ comment }}</p>

<!-- Disable escaping (DANGEROUS!) -->
<div>{{ html_content|safe }}</div>
```

### Comparison Table

| Feature | PHP | Django |
|---------|-----|--------|
| **Default behavior** | No escaping ❌ | Auto-escape ✅ |
| **Safe output** | `htmlspecialchars($var)` | `{{ var }}` |
| **Unsafe output** | `$var` | `{{ var\|safe }}` |
| **Developer responsibility** | High | Low |

**📌 Critical:** In PHP, forgetting `htmlspecialchars()` = XSS vulnerability!

---

## 6️⃣ Complete Examples

### Example 1: User Profile Page

#### PHP Version
```php
<?php
// Data section
$username = "AN Mamun";
$email = "mamun@example.com";
$is_verified = true;
$posts = ["First Post", "Second Post", "Third Post"];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile - <?= htmlspecialchars($username) ?></title>
</head>
<body>
    <h1>Profile: <?= htmlspecialchars($username) ?></h1>
    <p>Email: <?= htmlspecialchars($email) ?></p>
    
    <?php if ($is_verified): ?>
        <span style="color: green;">✓ Verified</span>
    <?php else: ?>
        <span style="color: red;">✗ Not Verified</span>
    <?php endif; ?>
    
    <h2>Posts</h2>
    <?php if (empty($posts)): ?>
        <p>No posts yet</p>
    <?php else: ?>
        <ul>
        <?php foreach ($posts as $index => $post): ?>
            <li><?= $index + 1 ?>. <?= htmlspecialchars($post) ?></li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>
```

#### Django Version

**views.py:**
```python
def profile(request):
    context = {
        "username": "AN Mamun",
        "email": "mamun@example.com",
        "is_verified": True,
        "posts": ["First Post", "Second Post", "Third Post"]
    }
    return render(request, 'profile.html', context)
```

**profile.html:**
```html
<!DOCTYPE html>
<html>
<head>
    <title>Profile - {{ username }}</title>
</head>
<body>
    <h1>Profile: {{ username }}</h1>
    <p>Email: {{ email }}</p>
    
    {% if is_verified %}
        <span style="color: green;">✓ Verified</span>
    {% else %}
        <span style="color: red;">✗ Not Verified</span>
    {% endif %}
    
    <h2>Posts</h2>
    <ul>
    {% for post in posts %}
        <li>{{ forloop.counter }}. {{ post }}</li>
    {% empty %}
        <li>No posts yet</li>
    {% endfor %}
    </ul>
</body>
</html>
```

### Example 2: Product Listing

#### PHP Version
```php
<?php
$products = [
    ["name" => "Laptop", "price" => 50000, "stock" => 5],
    ["name" => "Mouse", "price" => 500, "stock" => 0],
    ["name" => "Keyboard", "price" => 1200, "stock" => 10]
];
?>

<h1>Products</h1>
<table>
    <tr>
        <th>Name</th>
        <th>Price</th>
        <th>Status</th>
    </tr>
    <?php foreach ($products as $product): ?>
    <tr>
        <td><?= htmlspecialchars($product["name"]) ?></td>
        <td>৳<?= number_format($product["price"]) ?></td>
        <td>
            <?php if ($product["stock"] > 0): ?>
                <span style="color: green;">In Stock</span>
            <?php else: ?>
                <span style="color: red;">Out of Stock</span>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
```

#### Django Version

**views.py:**
```python
def products(request):
    products = [
        {"name": "Laptop", "price": 50000, "stock": 5},
        {"name": "Mouse", "price": 500, "stock": 0},
        {"name": "Keyboard", "price": 1200, "stock": 10}
    ]
    return render(request, 'products.html', {'products': products})
```

**products.html:**
```html
<h1>Products</h1>
<table>
    <tr>
        <th>Name</th>
        <th>Price</th>
        <th>Status</th>
    </tr>
    {% for product in products %}
    <tr>
        <td>{{ product.name }}</td>
        <td>৳{{ product.price|floatformat:0|intcomma }}</td>
        <td>
            {% if product.stock > 0 %}
                <span style="color: green;">In Stock</span>
            {% else %}
                <span style="color: red;">Out of Stock</span>
            {% endif %}
        </td>
    </tr>
    {% endfor %}
</table>
```

---

## 7️⃣ Quick Reference

### Print Variable

| Task | PHP | Django |
|------|-----|--------|
| **Simple print** | `<?= $var ?>` | `{{ var }}` |
| **With function** | `<?= strtoupper($var) ?>` | `{{ var\|upper }}` |
| **Default value** | `<?= $var ?? 'default' ?>` | `{{ var\|default:"default" }}` |
| **Escape HTML** | `<?= htmlspecialchars($var) ?>` | `{{ var }}` (auto) |

### Conditions

| Task | PHP | Django |
|------|-----|--------|
| **If statement** | `<?php if ($x): ?>` | `{% if x %}` |
| **If-else** | `<?php if ($x): ?> ... <?php else: ?>` | `{% if x %} ... {% else %}` |
| **Elseif** | `<?php elseif ($y): ?>` | `{% elif y %}` |
| **End if** | `<?php endif; ?>` | `{% endif %}` |
| **AND condition** | `<?php if ($x && $y): ?>` | `{% if x and y %}` |
| **OR condition** | `<?php if ($x \|\| $y): ?>` | `{% if x or y %}` |

### Loops

| Task | PHP | Django |
|------|-----|--------|
| **For loop** | `<?php foreach ($items as $item): ?>` | `{% for item in items %}` |
| **End loop** | `<?php endforeach; ?>` | `{% endfor %}` |
| **Loop counter** | `<?php foreach ($items as $i => $item): ?>` | `{{ forloop.counter }}` |
| **Empty check** | `<?php if (empty($items)): ?>` | `{% empty %}` |
| **Loop first** | - | `{% if forloop.first %}` |
| **Loop last** | - | `{% if forloop.last %}` |

---

## 8️⃣ Key Differences

### File Structure

**PHP:**
```
index.php          ← Data + HTML in same file
about.php          ← Another page
contact.php        ← Another page
```

**Django:**
```
views.py           ← Python logic (data)
templates/
  ├── index.html   ← HTML only
  ├── about.html   ← HTML only
  └── contact.html ← HTML only
```

### Philosophy

| Aspect | PHP | Django |
|--------|-----|--------|
| **Separation** | Mixed (code + HTML) | Separated (views + templates) |
| **Security** | Manual | Automatic |
| **Learning curve** | Lower | Higher |
| **Maintainability** | Lower (mixed code) | Higher (clean separation) |
| **Template language** | PHP itself | Django template language |

### Why Django Separates Logic from Presentation

**PHP (Mixed):**
```php
<?php
// Database query
$users = mysqli_query($conn, "SELECT * FROM users");

// Business logic
if (count($users) > 10) {
    $message = "Many users";
}
?>

<!-- HTML mixed with PHP -->
<h1><?= $message ?></h1>
```

**Django (Separated):**
```python
# views.py - Pure Python
def user_list(request):
    users = User.objects.all()
    message = "Many users" if len(users) > 10 else "Few users"
    return render(request, 'users.html', {'message': message})
```

```html
<!-- users.html - Pure HTML -->
<h1>{{ message }}</h1>
```

**📌 Advantage:** Frontend developers can edit templates without knowing Python!

---

## 9️⃣ Big Picture (Important)

### PHP Approach
> **Mix logic + HTML in same file**

**Pros:**
- ✅ Simple to start
- ✅ No learning curve
- ✅ Quick prototyping

**Cons:**
- ❌ Hard to maintain
- ❌ Security risks (forgot escape)
- ❌ Frontend/Backend coupling

### Django Approach
> **Separate logic (views.py) and presentation (templates)**

**Pros:**
- ✅ Clean separation
- ✅ Auto-escaping (secure)
- ✅ Easy to maintain
- ✅ Team-friendly

**Cons:**
- ❌ More files
- ❌ Learning curve
- ❌ Template language syntax

### Modern PHP (Laravel)

Laravel brings Django-like separation to PHP:

```php
// routes/web.php
Route::get('/', [UserController::class, 'index']);

// app/Http/Controllers/UserController.php
class UserController {
    public function index() {
        $users = User::all();
        return view('users.index', ['users' => $users]);
    }
}
```

```blade
<!-- resources/views/users/index.blade.php -->
<h1>Users</h1>
@foreach ($users as $user)
    <li>{{ $user->name }}</li>
@endforeach
```

**📌 Laravel Blade syntax ≈ Django templates!**

---

## 🔟 Next Steps

Now you understand PHP templating! Next logical steps:

### 1️⃣ **Mini MVC Structure in PHP**
Separate PHP code from HTML (Django-like structure)

### 2️⃣ **Laravel Blade Templates**
Modern PHP templating (exactly like Django)

### 3️⃣ **Database Loop (PDO vs Django ORM)**
Fetch data from database and display

### 4️⃣ **CSRF Protection**
Implement Django-like CSRF tokens in PHP

---

## 📝 Practice Exercise

Create this in both PHP and Django:

**Requirements:**
- Show list of books
- Each book has: title, author, price, available
- Display "Available" in green or "Sold Out" in red
- If no books, show "No books available"
- Number each book (1, 2, 3...)

**Bonus:** Add total number of books at top

---

## 💡 Summary

| Concept | PHP | Django | Winner |
|---------|-----|--------|--------|
| **Print variable** | `<?= $var ?>` | `{{ var }}` | Tie |
| **Conditions** | `<?php if: ?>` | `{% if %}` | Django (cleaner) |
| **Loops** | `<?php foreach: ?>` | `{% for %}` | Django (cleaner) |
| **Security** | Manual | Auto | Django ✅ |
| **Separation** | Mixed | Separated | Django ✅ |
| **Learning** | Easy | Medium | PHP |
| **Production** | Need framework | Ready | Django ✅ |

### Bottom Line:

**Raw PHP:** Good for learning fundamentals  
**Django:** Production-ready with best practices  
**Laravel:** PHP with Django-like features

---

তুমি এখন PHP templating পুরোটা বুঝতে পারছো! 💪

Next যা বলবে সেটা শিখবো 🚀

**Updated:** February 2026  
**Type:** PHP vs Django Template Comparison