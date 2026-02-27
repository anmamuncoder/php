# PHP vs Django - Complete Side-by-Side Comparison

A comprehensive guide for Django developers learning PHP.

 

## 1️⃣ Request Method Check

### PHP
```php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Handle POST request
}
```

### Django
```python
if request.method == "POST":
    # Handle POST request
```

**📌 Same concept:** Check if form was submitted

---

## 2️⃣ Form Data Read

### PHP
```php
$username = $_POST["username"];
$email = $_POST["email"];
```

### Django
```python
username = request.POST.get("username")
email = request.POST.get("email")
```

**📌 Difference:**
- Django safer by default (returns `None` if not found)
- PHP throws error if key doesn't exist (use `$_POST["username"] ?? ""`)

---

## 3️⃣ Validation (Required Field)

### PHP (Manual)
```php
$errors = [];

if (empty($_POST["username"])) {
    $errors[] = "Username is required";
}

if (empty($_POST["email"])) {
    $errors[] = "Email is required";
}

if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format";
}
```

### Django (Manual)
```python
errors = []

if not request.POST.get("username"):
    errors.append("Username is required")

if not request.POST.get("email"):
    errors.append("Email is required")
```

### Django (Recommended - Forms)
```python
from django import forms

class UserForm(forms.Form):
    username = forms.CharField(required=True, max_length=100)
    email = forms.EmailField(required=True)

# In view
form = UserForm(request.POST)
if form.is_valid():
    username = form.cleaned_data['username']
    email = form.cleaned_data['email']
```

**📌 Key Difference:**
- Django forms = built-in validation
- PHP = manual validation (or use libraries)

---

## 4️⃣ Keep Old Input (Form Re-populate)

### PHP
```php
<input type="text" name="username" 
       value="<?php echo htmlspecialchars($username ?? ''); ?>">
```

### Django (Manual)
```python
<input type="text" name="username" 
       value="{{ request.POST.username }}">
```

### Django (With Forms)
```python
{{ form.username }}
```

**📌 Key Points:**
- Django auto-escapes output
- PHP requires manual `htmlspecialchars()`
- Django forms handle everything automatically

---

## 5️⃣ Output Data Safely (XSS Protection)

### PHP (Manual Escape)
```php
<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>

<!-- Shorthand -->
<?= htmlspecialchars($username) ?>
```

### Django (Auto-Escape)
```python
{{ username }}

<!-- Manual escape off (dangerous!) -->
{{ username|safe }}
```

**📌 Critical Difference:**
- Django auto-escapes by default ✅
- PHP requires developer to remember ❌
- Forgetting = XSS vulnerability

---

## 6️⃣ Conditional Rendering

### PHP
```php
<?php if ($error): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if (count($errors) > 0): ?>
    <ul>
        <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
```

### Django
```python
{% if error %}
    <p class="error">{{ error }}</p>
{% endif %}

{% if errors %}
    <ul>
        {% for error in errors %}
            <li>{{ error }}</li>
        {% endfor %}
    </ul>
{% endif %}
```

**📌 Comparison:**
- Syntax different, logic same
- Django template syntax cleaner
- Both support loops, conditions

---

## 7️⃣ Full Flow Mapping

| Step | PHP | Django |
|------|-----|--------|
| **Form Submit** | `<form method="post">` | Same |
| **Request Check** | `$_SERVER["REQUEST_METHOD"]` | `request.method` |
| **Read Data** | `$_POST["field"]` | `request.POST.get("field")` |
| **Validation** | Manual or library | Forms / manual |
| **Escape Output** | `htmlspecialchars()` | Auto ({{ }}) |
| **Template** | PHP itself | Template engine |
| **CSRF Protection** | Manual token | Auto (`{% csrf_token %}`) |
| **Database** | PDO / mysqli | ORM |
| **Routing** | File-based | urls.py patterns |

---

## 8️⃣ Quick Reference

### Request & Response

| Task | PHP | Django |
|------|-----|--------|
| **Check POST** | `$_SERVER["REQUEST_METHOD"] === "POST"` | `request.method == "POST"` |
| **Get POST data** | `$_POST["name"]` | `request.POST.get("name")` |
| **Get GET data** | `$_GET["id"]` | `request.GET.get("id")` |
| **Check if key exists** | `isset($_POST["name"])` | `"name" in request.POST` |
| **Redirect** | `header("Location: /page")` | `redirect("/page")` |
| **JSON response** | `echo json_encode($data)` | `JsonResponse(data)` |

### Validation

| Task | PHP | Django |
|------|-----|--------|
| **Required** | `empty($value)` | `forms.CharField(required=True)` |
| **Email** | `filter_var($email, FILTER_VALIDATE_EMAIL)` | `forms.EmailField()` |
| **Min length** | `strlen($value) >= 3` | `forms.CharField(min_length=3)` |
| **Max length** | `strlen($value) <= 100` | `forms.CharField(max_length=100)` |
| **Number** | `is_numeric($value)` | `forms.IntegerField()` |
| **In choices** | `in_array($value, $choices)` | `forms.ChoiceField(choices=...)` |

### Output & Security

| Task | PHP | Django |
|------|-----|--------|
| **Escape HTML** | `htmlspecialchars($var)` | `{{ var }}` (auto) |
| **Raw HTML** | `$var` (no escape) | `{{ var\|safe }}` |
| **CSRF token** | Manual implementation | `{% csrf_token %}` |
| **Session** | `$_SESSION["key"]` | `request.session["key"]` |
| **Cookies** | `$_COOKIE["name"]` | `request.COOKIES["name"]` |

---

## 9️⃣ Key Differences

### Django Advantages ✅

1. **Auto-escaping** - XSS protection by default
2. **CSRF protection** - Built-in token system
3. **ORM** - No SQL needed
4. **Forms** - Validation + rendering
5. **Admin panel** - Auto-generated
6. **Migrations** - Database version control
7. **Safe defaults** - Security first

### PHP Advantages ✅

1. **Full control** - No magic
2. **Simple deployment** - Just upload files
3. **File-based routing** - Easy to understand
4. **Mix HTML + code** - No template language needed
5. **Lightweight** - No framework overhead
6. **Shared hosting** - Works everywhere

### Why Django is Safer 🔐

```php
// PHP - UNSAFE if you forget htmlspecialchars
echo $_POST["username"]; // XSS vulnerability!

// Django - SAFE by default
{{ username }} <!-- Auto-escaped -->
```

```php
// PHP - UNSAFE if you forget CSRF token
<form method="post">

// Django - SAFE by default
<form method="post">
    {% csrf_token %}
```

---

**Updated:** February 2026  
**Type:** PHP vs Django Comparison Guide