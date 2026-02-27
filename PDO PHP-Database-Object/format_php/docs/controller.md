# PHP UserController - Complete Explanation

Understanding MVC Controller with Django views.py comparison.

---

## 📋 Table of Contents

1. [Big Picture](#1-big-picture)
2. [Complete Code](#2-complete-code)
3. [Line-by-Line Breakdown](#3-line-by-line-breakdown)
4. [index() Method (GET)](#4-index-method-get)
5. [store() Method (POST)](#5-store-method-post)
6. [Request Flow](#6-request-flow)
7. [Django Comparison](#7-django-comparison)
8. [Security Notes](#8-security-notes)
9. [MVC Pattern](#9-mvc-pattern)

---

## 1️⃣ Big Picture

### 🧠 এই File টা কী?

**এই file টা হলো Controller**

📌 **Django-তে যেটা `views.py`**

### Controller-এর কাজ

```
1. Request handle করা
2. Model কে call করা
3. View (template) load করা
```

### Visual Flow

```
Browser Request
      ↓
  Controller
      ↓
    Model (Database)
      ↓
    View (Template)
      ↓
Browser Response
```

---

## 2️⃣ Complete Code

```php
<?php
require_once __DIR__ . '/../models/User.php';

class UserController
{
    public function index()
    {
        $user = new User();
        $users = $user->all();

        require __DIR__ . '/../views/users.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name  = trim($_POST['name']);
            $email = trim($_POST['email']);

            if ($name && $email) {
                $user = new User();
                $user->create($name, $email);
            }

            header("Location: /");
            exit;
        }
    }
}
```

---

## 3️⃣ Line-by-Line Breakdown

### Line 1: PHP Opening Tag

```php
<?php
```

**Meaning:**
- PHP code শুরু

### Line 2: Model Include

```php
require_once __DIR__ . '/../models/User.php';
```

**Breaking it down:**

#### `require_once`
- Load a file
- `once` = একবারই load হবে (duplicate prevent)

#### `__DIR__`
- Current directory
- Magic constant

#### `/../models/User.php`
- Go up one level (`..`)
- Enter `models` folder
- Load `User.php`

**Directory structure:**
```
project/
├── controllers/
│   └── UserController.php  ← We are here
└── models/
    └── User.php            ← Loading this
```

**Django equivalent:**
```python
from .models import User
```

**Comparison:**

| PHP | Django |
|-----|--------|
| `require_once` | `import` |
| File path | Module path |
| Manual include | Auto-imported |

### Line 4: Controller Class

```php
class UserController
{
```

**Meaning:**
- Create a class named `UserController`
- Contains methods to handle requests
- Each public method = different action

**Django equivalent:**
```python
# views.py
# No class needed, just functions
```

**Why class in PHP?**
- Organization
- Group related actions
- Reusability
- OOP structure

---

## 4️⃣ index() Method (GET)

### Complete Method

```php
public function index()
{
    $user = new User();
    $users = $user->all();

    require __DIR__ . '/../views/users.php';
}
```

### 🧠 এই Method এর কাজ

**Purpose:**
- 👉 `/` URL এর জন্য
- 👉 User list দেখানোর কাজ করে
- 👉 GET request handle

**Django equivalent:**
```python
def index(request):
    users = User.objects.all()
    return render(request, "users.html", {"users": users})
```

### Step 1: Create Model Object

```php
$user = new User();
```

**Meaning:**
- 👉 User model instantiate করা
- 👉 DB logic এখানেই আছে

**Django equivalent:**
```python
# Django doesn't need object creation
# User.objects is already available
```

### Step 2: Fetch Data

```php
$users = $user->all();
```

**Meaning:**
- 👉 Database থেকে সব user আনা হচ্ছে
- 👉 `$users` = array of users
- 👉 `all()` method Model-এ defined

**What `$users` contains:**
```php
[
    ['id' => 1, 'name' => 'Ali', 'email' => 'ali@example.com'],
    ['id' => 2, 'name' => 'Rahim', 'email' => 'rahim@example.com'],
    ['id' => 3, 'name' => 'Karim', 'email' => 'karim@example.com']
]
```

**Django equivalent:**
```python
users = User.objects.all()
# Returns QuerySet
```

**Comparison:**

| PHP | Django |
|-----|--------|
| `$user->all()` | `User.objects.all()` |
| Returns array | Returns QuerySet |
| Manual model instantiation | Auto-available |

### Step 3: Load View

```php
require __DIR__ . '/../views/users.php';
```

**Meaning:**
- 👉 Template include করা হচ্ছে
- 👉 `$users` variable view-তে available থাকবে
- 👉 PHP scope = variables automatically passed

**Directory structure:**
```
project/
├── controllers/
│   └── UserController.php  ← We are here
└── views/
    └── users.php           ← Loading this
```

**Django equivalent:**
```python
return render(request, "users.html", {"users": users})
```

**Comparison:**

| PHP | Django |
|-----|--------|
| `require` file | `render()` function |
| Variables auto-available | Explicit context dict |
| Same PHP scope | Separate template scope |

### Complete Flow Diagram

```
Browser: GET /
      ↓
index() method called
      ↓
new User()
      ↓
$users = $user->all()
      ↓
PDO → Database
      ↓
Data returned
      ↓
require users.php
      ↓
HTML rendered
      ↓
Browser displays
```

---

## 5️⃣ store() Method (POST)

### Complete Method

```php
public function store()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name  = trim($_POST['name']);
        $email = trim($_POST['email']);

        if ($name && $email) {
            $user = new User();
            $user->create($name, $email);
        }

        header("Location: /");
        exit;
    }
}
```

### 🧠 এই Method এর কাজ

**Purpose:**
- 👉 Form submit handle করে
- 👉 POST request এর জন্য
- 👉 Data insert করে database-এ

**Django equivalent:**
```python
def store(request):
    if request.method == "POST":
        name = request.POST.get("name").strip()
        email = request.POST.get("email").strip()
        
        if name and email:
            User.objects.create(name=name, email=email)
        
        return redirect("/")
```

### Step 1: Request Method Check

```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
```

**Meaning:**
- 👉 নিশ্চিত করা হচ্ছে POST কিনা
- 👉 GET request এলে কিছু হবে না

**$_SERVER superglobal:**
```php
$_SERVER['REQUEST_METHOD']
// Returns: "GET", "POST", "PUT", "DELETE", etc.
```

**Django equivalent:**
```python
if request.method == "POST":
```

**Comparison:**

| PHP | Django |
|-----|--------|
| `$_SERVER['REQUEST_METHOD']` | `request.method` |
| String comparison | String comparison |
| Manual check | Same concept |

### Step 2: Get Form Data

```php
$name  = trim($_POST['name']);
$email = trim($_POST['email']);
```

**Breaking it down:**

#### `$_POST['name']`
- Get form field named `name`
- Superglobal array
- Contains all POST data

#### `trim()`
- Remove whitespace from start/end
- Clean user input
- **Important for validation**

**Example:**
```php
// User types: "  John  "
$name = $_POST['name'];        // "  John  "
$name = trim($_POST['name']);  // "John"
```

**Django equivalent:**
```python
name = request.POST.get("name").strip()
email = request.POST.get("email").strip()
```

**Comparison:**

| PHP | Django |
|-----|--------|
| `$_POST['name']` | `request.POST.get("name")` |
| `trim()` | `.strip()` |
| Direct access | `.get()` method safer |

### Step 3: Basic Validation

```php
if ($name && $email) {
```

**Meaning:**
- 👉 Check if both fields have values
- 👉 Empty string = `false` in PHP
- 👉 না হলে DB insert হবে না

**Truth table:**
```php
$name = "John";  $email = "john@example.com";  // true
$name = "";      $email = "john@example.com";  // false
$name = "John";  $email = "";                  // false
$name = "";      $email = "";                  // false
```

**Django equivalent:**
```python
if name and email:
```

**Better validation (production):**
```php
if ($name && $email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // Valid email format
}
```

### Step 4: Create User

```php
$user = new User();
$user->create($name, $email);
```

**Meaning:**
- 👉 Model এর `create()` method call
- 👉 PDO দিয়ে DB-তে insert হয়
- 👉 SQL injection safe (prepared statements)

**What happens inside Model:**
```php
// User.php
public function create($name, $email) {
    $db = Database::connect();
    $stmt = $db->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
    $stmt->execute([$name, $email]);
}
```

**Django equivalent:**
```python
User.objects.create(name=name, email=email)
```

**Comparison:**

| PHP | Django |
|-----|--------|
| Manual object creation | Manager method |
| Explicit method call | ORM handles everything |
| PDO prepared statements | ORM uses prepared statements |

### Step 5: Redirect (Post/Redirect/Get Pattern)

```php
header("Location: /");
exit;
```

**Meaning:**
- 👉 Form submit শেষে redirect
- 👉 Page refresh করলে duplicate insert হবে না
- 👉 **PRG Pattern** (Post/Redirect/Get)

**Why redirect?**

**Without redirect:**
```
User submits form
     ↓
POST /store
     ↓
Data inserted
     ↓
Show same page
     ↓
User presses F5 (refresh)
     ↓
POST /store AGAIN
     ↓
DUPLICATE data inserted! ❌
```

**With redirect:**
```
User submits form
     ↓
POST /store
     ↓
Data inserted
     ↓
Redirect to GET /
     ↓
Show page
     ↓
User presses F5 (refresh)
     ↓
GET / (safe to refresh) ✅
```

**Django equivalent:**
```python
return redirect("/")
```

**Comparison:**

| PHP | Django |
|-----|--------|
| `header("Location: /")` | `redirect("/")` |
| `exit;` required | Auto-handled |
| Manual redirect | Framework function |

---

## 6️⃣ Request Flow

### 🔁 GET Request (/)

```
Browser: GET /
      ↓
index.php routes to UserController
      ↓
UserController@index
      ↓
new User()
      ↓
$users = $user->all()
      ↓
PDO query: SELECT * FROM users
      ↓
Data returned as array
      ↓
require views/users.php
      ↓
HTML with user list
      ↓
Browser displays
```

### 🔁 POST Request (Form Submit)

```
Browser: POST /store
      ↓
Form data in $_POST
      ↓
index.php routes to UserController
      ↓
UserController@store
      ↓
Check REQUEST_METHOD === POST
      ↓
Get $_POST['name'] and $_POST['email']
      ↓
Validate: if ($name && $email)
      ↓
new User()
      ↓
$user->create($name, $email)
      ↓
PDO query: INSERT INTO users...
      ↓
Data inserted
      ↓
header("Location: /")
      ↓
Browser redirects to GET /
      ↓
Shows updated user list
```

### Complete Cycle

```
┌──────────────────────────────────┐
│  Browser                         │
│  User fills form                 │
└─────────────┬────────────────────┘
              │ POST
              ↓
┌──────────────────────────────────┐
│  Controller: store()             │
│  - Validate input                │
│  - Call Model                    │
└─────────────┬────────────────────┘
              │
              ↓
┌──────────────────────────────────┐
│  Model: create()                 │
│  - PDO prepare()                 │
│  - execute()                     │
│  - INSERT into database          │
└─────────────┬────────────────────┘
              │
              ↓
┌──────────────────────────────────┐
│  Database                        │
│  - New record saved              │
└─────────────┬────────────────────┘
              │
              ↓
┌──────────────────────────────────┐
│  Controller                      │
│  - Redirect to /                 │
└─────────────┬────────────────────┘
              │ 303 Redirect
              ↓
┌──────────────────────────────────┐
│  Browser: GET /                  │
│  Shows updated list              │
└──────────────────────────────────┘
```

---

## 7️⃣ Django Comparison

### Side-by-Side Code

#### PHP UserController

```php
<?php
class UserController
{
    public function index()
    {
        $user = new User();
        $users = $user->all();
        require __DIR__ . '/../views/users.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name  = trim($_POST['name']);
            $email = trim($_POST['email']);

            if ($name && $email) {
                $user = new User();
                $user->create($name, $email);
            }

            header("Location: /");
            exit;
        }
    }
}
```

#### Django views.py

```python
from django.shortcuts import render, redirect
from .models import User

def index(request):
    users = User.objects.all()
    return render(request, 'users.html', {'users': users})

def store(request):
    if request.method == 'POST':
        name = request.POST.get('name', '').strip()
        email = request.POST.get('email', '').strip()
        
        if name and email:
            User.objects.create(name=name, email=email)
        
        return redirect('/')
```

### Feature Comparison Table

| Feature | PHP | Django |
|---------|-----|--------|
| **File structure** | Controller class | Functions in views.py |
| **Import model** | `require_once` | `from .models import` |
| **List data** | `$user->all()` | `User.objects.all()` |
| **Load template** | `require view.php` | `render()` |
| **Get POST data** | `$_POST['field']` | `request.POST.get('field')` |
| **Validation** | Manual | Forms or manual |
| **Create record** | `$user->create()` | `User.objects.create()` |
| **Redirect** | `header()` + `exit` | `redirect()` |
| **CSRF** | Manual | Auto (`{% csrf_token %}`) |

### Syntax Comparison

| Task | PHP | Django |
|------|-----|--------|
| **Check POST** | `$_SERVER['REQUEST_METHOD'] === 'POST'` | `request.method == 'POST'` |
| **Get input** | `$_POST['name']` | `request.POST.get('name')` |
| **Trim** | `trim($value)` | `value.strip()` |
| **Validation** | `if ($name && $email)` | `if name and email:` |
| **Redirect** | `header("Location: /"); exit;` | `return redirect("/")` |

---

## 8️⃣ Security Notes

### ✅ What's Good

#### 1. PDO Used (SQL Injection Safe)
```php
// Inside User model
$stmt = $db->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
$stmt->execute([$name, $email]);
// ✅ Safe - prepared statements
```

#### 2. trim() Used (Clean Input)
```php
$name = trim($_POST['name']);
// ✅ Removes extra whitespace
```

#### 3. Basic Validation
```php
if ($name && $email) {
    // ✅ Checks for empty values
}
```

#### 4. Post/Redirect/Get Pattern
```php
header("Location: /");
exit;
// ✅ Prevents duplicate submissions
```

### ⚠️ What's Missing

#### 1. CSRF Protection
```php
// ❌ Missing CSRF token validation
// Should add:
if (!verify_csrf_token($_POST['csrf_token'])) {
    die('CSRF validation failed');
}
```

#### 2. Output Escaping in View
```php
// In views/users.php
// ❌ Should escape output
<?= htmlspecialchars($user['name']) ?>
```

#### 3. Email Validation
```php
// ❌ Should validate email format
if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // Insert
}
```

#### 4. Error Handling
```php
// ❌ No try-catch
try {
    $user->create($name, $email);
} catch (PDOException $e) {
    // Handle error
}
```

### Improved Version

```php
public function store()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // ✅ CSRF check
        verify_csrf_token($_POST['csrf_token']);
        
        // ✅ Get and sanitize input
        $name  = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');

        // ✅ Better validation
        if ($name && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            try {
                $user = new User();
                $user->create($name, $email);
                
                // ✅ Success message
                $_SESSION['success'] = 'User created successfully!';
            } catch (PDOException $e) {
                // ✅ Error handling
                $_SESSION['error'] = 'Failed to create user';
                error_log($e->getMessage());
            }
        } else {
            // ✅ Validation error
            $_SESSION['error'] = 'Invalid name or email';
        }

        header("Location: /");
        exit;
    }
}
```

---

## 9️⃣ MVC Pattern

### What is MVC?

```
Model      → Database logic
View       → Presentation (HTML)
Controller → Request handling
```

### This File's Role

```
UserController = Controller

Responsibilities:
1. Receive request
2. Call Model (User)
3. Load View (users.php)
4. Handle redirect
```

### Complete MVC Flow

```
┌─────────────┐
│   Browser   │
└──────┬──────┘
       │ Request
       ↓
┌─────────────────────┐
│   Controller        │
│  (UserController)   │
│                     │
│  - index()          │
│  - store()          │
└──────┬──────────────┘
       │
       ↓
┌─────────────────────┐
│     Model           │
│    (User)           │
│                     │
│  - all()            │
│  - create()         │
└──────┬──────────────┘
       │
       ↓
┌─────────────────────┐
│    Database         │
│     (MySQL)         │
└─────────────────────┘
       │
       ↓ (data)
┌─────────────────────┐
│      View           │
│   (users.php)       │
│                     │
│  - Display HTML     │
└──────┬──────────────┘
       │
       ↓
┌─────────────┐
│   Browser   │
└─────────────┘
```

---

## 📝 Short Note Summary

```markdown
# UserController (PHP)

## index() - GET request
- Handle GET request
- Fetch data from Model: $user->all()
- Load View: require users.php

## store() - POST request
- Handle POST request
- Validate input: trim() + check empty
- Insert data via Model: $user->create()
- Redirect after submit: header("Location: /")

## Django Equivalent
views.py with functions:
- index(request)
- store(request)

## MVC Pattern
Controller → Model → Database
           ↓
        View → Browser
```

---

## 💡 Summary

### ✅ What You Learned

1. **Controller role** - Request handler
2. **GET vs POST** - Different methods for different actions
3. **Django ↔ PHP mapping** - views.py = Controller
4. **Mini MVC flow** - Controller → Model → View
5. **PRG pattern** - Post/Redirect/Get prevents duplicates

### Key Takeaways

| Concept | PHP | Django |
|---------|-----|--------|
| **Controller** | Class with methods | Functions in views.py |
| **GET list** | `$user->all()` | `User.objects.all()` |
| **POST create** | `$user->create()` | `User.objects.create()` |
| **Template** | `require view.php` | `render()` |
| **Redirect** | `header()` | `redirect()` |

---

এখন তুমি PHP Controller সম্পূর্ণ বুঝে গেছো! 💪

**Django views.py = PHP Controller**  
**Same concept, different syntax!** 🚀

**Updated:** February 2026  
**Type:** PHP MVC Controller Guide