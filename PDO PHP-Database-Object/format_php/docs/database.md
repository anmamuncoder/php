# PHP PDO Database Class - Complete Explanation

Understanding Database connection class with Django comparisons.

---

## 📋 Table of Contents

1. [What is This File?](#1-what-is-this-file)
2. [Line-by-Line Breakdown](#2-line-by-line-breakdown)
3. [PDO Options Explained](#3-pdo-options-explained)
4. [How to Use This Class](#4-how-to-use-this-class)
5. [Complete Flow](#5-complete-flow)
6. [Django Comparison](#6-django-comparison)
7. [Best Practices](#7-best-practices)
8. [Common Errors](#8-common-errors)

---

## 1️⃣ What is This File?

### 🧠 এই File টা আসলে কী?

```php
<?php
class Database
{
    public static function connect()
    {
        return new PDO(
            "mysql:host=localhost;dbname=pdo_project;charset=utf8mb4",
            "root",
            "",
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    }
}
```

**Purpose:**
- 👉 একটা Database connection helper class
- 👉 কাজ একটাই: PDO connection বানিয়ে দেওয়া
- 👉 Reusable করার জন্য

### Django Comparison

```python
# settings.py
DATABASES = {
    'default': {
        'ENGINE': 'django.db.backends.mysql',
        'NAME': 'pdo_project',
        'USER': 'root',
        'PASSWORD': '',
        'HOST': 'localhost',
        'OPTIONS': {
            'charset': 'utf8mb4'
        }
    }
}
```

**Key Difference:**
- Django: Configuration file
- PHP: Class-based helper

---

## 2️⃣ Line-by-Line Breakdown

### Line 1: PHP Opening Tag

```php
<?php
```

**Meaning:**
- 👉 PHP code শুরু
- 👉 Django-তে যেমন `.py` file extension

**Django equivalent:** File starts with Python code directly

### Line 2: Class Declaration

```php
class Database
{
```

**Meaning:**
- 👉 `Database` নামে একটা class
- 👉 শুধু DB connect করার জন্য
- 👉 Encapsulation (সব DB logic এক জায়গায়)

**Django equivalent:**
```python
class Database:
    pass
```

### Line 3: Static Method

```php
public static function connect()
```

**এখানে 3টা keyword আছে:**

#### 🔹 `public`
- Anyone can access this method
- No restriction

#### 🔹 `static`
**VERY IMPORTANT!**
- Object বানানো ছাড়াই call করা যাবে
- Direct class call

**Usage:**
```php
// ✅ Static method - No object needed
$db = Database::connect();

// ❌ This is NOT needed
$database = new Database();
$db = $database->connect();
```

**Why static?**
- Convenient
- No need to create object
- Acts like a utility function

**Django equivalent:**
```python
# Django uses module-level connection
from django.db import connection
```

#### 🔹 `function`
- Declares a method
- Returns PDO object

### Line 4: Return PDO Object

```php
return new PDO(
```

**Meaning:**
- 👉 নতুন PDO object বানাচ্ছে
- 👉 এই object দিয়েই DB query হবে
- 👉 Connection established

**Django equivalent:**
```python
# Django ORM handles this automatically
from django.db import connection
# connection is already a database connection object
```

### Line 5: DSN (Data Source Name)

```php
"mysql:host=localhost;dbname=pdo_project;charset=utf8mb4",
```

**এটা 4টা info দেয়:**

| Part | Value | Meaning |
|------|-------|---------|
| `mysql` | Database type | MySQL driver |
| `host=localhost` | Server location | Local machine |
| `dbname=pdo_project` | Database name | Which database to use |
| `charset=utf8mb4` | Character encoding | Emoji safe, Unicode |

**Breakdown:**

#### mysql:
```php
mysql:
```
- Database type/driver
- Could be: `pgsql`, `sqlite`, `sqlsrv`

#### host=localhost
```php
host=localhost
```
- Database server address
- `localhost` = same computer
- Could be: `192.168.1.100`, `db.example.com`

#### dbname=pdo_project
```php
dbname=pdo_project
```
- Which database to connect to
- Must exist in MySQL

#### charset=utf8mb4
```php
charset=utf8mb4
```
- Character encoding
- `utf8mb4` = Full Unicode (includes emoji 😊)
- `utf8` = Limited Unicode (no emoji)

**Django equivalent:**
```python
DATABASES = {
    'default': {
        'ENGINE': 'django.db.backends.mysql',  # mysql
        'HOST': 'localhost',                    # host
        'NAME': 'pdo_project',                  # dbname
        'OPTIONS': {'charset': 'utf8mb4'}       # charset
    }
}
```

### Line 6-7: Credentials

```php
"root",
"",
```

**Line 6:** Username
```php
"root"
```
- 👉 MySQL username = `root`
- Default username on Ubuntu/XAMPP

**Line 7:** Password
```php
""
```
- 👉 Password = empty string
- Default on Ubuntu local MySQL
- ⚠️ **Production এ এটা bad practice**

**Security Warning:**
```php
// ❌ BAD (in production)
"root", ""

// ✅ GOOD (in production)
"app_user", "strong_password_here"
```

**Django equivalent:**
```python
DATABASES = {
    'default': {
        'USER': 'root',       # Username
        'PASSWORD': '',       # Password
    }
}
```

### Line 8-11: PDO Options (VERY IMPORTANT!)

```php
[
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]
```

**These are configuration options for PDO behavior**

---

## 3️⃣ PDO Options Explained

### Option 1: Error Mode

```php
PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
```

**🔹 What it does:**
- 👉 Error হলে exception throw করবে
- 👉 Silent fail হবে না
- 👉 Debugging easier

**Without this option:**
```php
$stmt = $db->query("INVALID SQL");
// ❌ No error shown, silently fails
```

**With this option:**
```php
$stmt = $db->query("INVALID SQL");
// ✅ Exception thrown:
// Fatal error: Uncaught PDOException: SQLSTATE[42000]: Syntax error
```

**Error Mode Options:**

| Mode | Meaning |
|------|---------|
| `PDO::ERRMODE_SILENT` | No errors (default) |
| `PDO::ERRMODE_WARNING` | PHP warnings |
| `PDO::ERRMODE_EXCEPTION` | Throws exceptions ✅ |

**Best practice:** Always use `EXCEPTION` mode

**Django equivalent:**
```python
# Django always throws exceptions on database errors
try:
    User.objects.get(id=999)
except User.DoesNotExist:
    print("User not found")
```

### Option 2: Fetch Mode

```php
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
```

**🔹 What it does:**
- 👉 Data **array** আকারে আসবে (associative)
- 👉 Key = column name
- 👉 Clean, readable

**Fetch Mode Options:**

#### PDO::FETCH_ASSOC (✅ Recommended)
```php
[
  'id' => 1,
  'name' => 'Mamun',
  'email' => 'mamun@example.com'
]
```

#### PDO::FETCH_NUM (Not recommended)
```php
[
  0 => 1,
  1 => 'Mamun',
  2 => 'mamun@example.com'
]
// Hard to read, must remember column order
```

#### PDO::FETCH_BOTH (Wasteful)
```php
[
  'id' => 1,
  0 => 1,              // Duplicate!
  'name' => 'Mamun',
  1 => 'Mamun',        // Duplicate!
  'email' => 'mamun@example.com',
  2 => 'mamun@example.com'  // Duplicate!
]
// Double the data, waste of memory
```

#### PDO::FETCH_OBJ (Object style)
```php
stdClass Object
(
    [id] => 1
    [name] => Mamun
    [email] => mamun@example.com
)
// Access: $user->name
```

**Django equivalent:**
```python
# Django QuerySet returns dict-like objects
user = User.objects.get(id=1)
# {'id': 1, 'name': 'Mamun', 'email': 'mamun@example.com'}
```

### Line 12-13: Closing Braces

```php
    }
}
```

**Line 12:** `}` closes `connect()` function  
**Line 13:** `}` closes `Database` class

---

## 4️⃣ How to Use This Class

### Step 1: Include the File

```php
<?php
require_once 'config/database.php';
```

### Step 2: Get Connection

```php
$db = Database::connect();
```

**What happens:**
- Static method called
- PDO object created
- Connection established
- Returns PDO object

### Step 3: Execute Queries

#### Example 1: Select All Users

```php
<?php
require_once 'config/database.php';

$db = Database::connect();

$stmt = $db->prepare("SELECT * FROM users");
$stmt->execute();
$users = $stmt->fetchAll();

foreach ($users as $user) {
    echo $user['name'] . "\n";
}
?>
```

#### Example 2: Insert User

```php
<?php
require_once 'config/database.php';

$db = Database::connect();

$stmt = $db->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
$stmt->execute(['John Doe', 'john@example.com']);

echo "User created!";
?>
```

#### Example 3: Find Single User

```php
<?php
require_once 'config/database.php';

$db = Database::connect();

$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([1]);
$user = $stmt->fetch();  // Single row

if ($user) {
    echo "Name: " . $user['name'];
} else {
    echo "User not found";
}
?>
```

#### Example 4: Update User

```php
<?php
require_once 'config/database.php';

$db = Database::connect();

$stmt = $db->prepare("UPDATE users SET name = ? WHERE id = ?");
$stmt->execute(['New Name', 1]);

echo "User updated!";
?>
```

#### Example 5: Delete User

```php
<?php
require_once 'config/database.php';

$db = Database::connect();

$stmt = $db->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([1]);

echo "User deleted!";
?>
```

---

## 5️⃣ Complete Flow

### 🔁 Flow Diagram

```
Database::connect()
      ↓
  PDO object
      ↓
  prepare()
      ↓
  execute()
      ↓
  fetchAll() / fetch()
      ↓
  PHP array
```

### Detailed Step-by-Step

#### Step 1: Call Static Method
```php
$db = Database::connect();
```
- Static method called
- No object instantiation needed

#### Step 2: PDO Object Created
```php
return new PDO(...);
```
- Connection established
- PDO object returned

#### Step 3: Prepare Query
```php
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
```
- SQL query prepared
- Placeholder `?` for parameter
- **Prevents SQL injection** ✅

#### Step 4: Execute with Parameters
```php
$stmt->execute([1]);
```
- Replaces `?` with `1`
- Query executed
- Safe from SQL injection

#### Step 5: Fetch Results
```php
$users = $stmt->fetchAll();  // All rows
// OR
$user = $stmt->fetch();      // Single row
```
- Data returned as array
- Thanks to `FETCH_ASSOC` option

---

## 6️⃣ Django Comparison

### Full Side-by-Side

| Task | PHP (PDO) | Django (ORM) |
|------|-----------|--------------|
| **Configuration** | `Database::connect()` | `settings.py DATABASES` |
| **Get connection** | `$db = Database::connect()` | Automatic |
| **Select all** | `$stmt->fetchAll()` | `User.objects.all()` |
| **Select one** | `$stmt->fetch()` | `User.objects.get(id=1)` |
| **Insert** | `prepare() + execute()` | `User.objects.create()` |
| **Update** | `prepare() + execute()` | `user.save()` |
| **Delete** | `prepare() + execute()` | `user.delete()` |
| **SQL Injection** | Manual (prepared) | Auto-protected |

### Example Comparison

#### PHP (PDO):
```php
$db = Database::connect();
$stmt = $db->prepare("SELECT * FROM users WHERE age > ?");
$stmt->execute([18]);
$users = $stmt->fetchAll();
```

#### Django (ORM):
```python
users = User.objects.filter(age__gt=18)
```

**Django advantage:** No SQL, cleaner syntax  
**PHP advantage:** Full SQL control, flexibility

---

## 7️⃣ Best Practices

### ✅ Do's

#### 1. Use Prepared Statements (Always!)

```php
// ✅ GOOD - Prepared statement
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);

// ❌ BAD - SQL injection risk
$stmt = $db->query("SELECT * FROM users WHERE id = $id");
```

#### 2. Use Environment Variables for Credentials

```php
// ✅ GOOD - Production
$db = new PDO(
    "mysql:host=" . getenv('DB_HOST'),
    getenv('DB_USER'),
    getenv('DB_PASS')
);

// ❌ BAD - Hardcoded
$db = new PDO("mysql:host=localhost", "root", "password");
```

#### 3. Try-Catch for Connection

```php
// ✅ GOOD - Handle errors
try {
    $db = Database::connect();
} catch (PDOException $e) {
    error_log($e->getMessage());
    die("Database connection failed");
}

// ❌ BAD - No error handling
$db = Database::connect();
```

#### 4. Close Connection (Optional)

```php
// Connection automatically closes when script ends
// But you can manually close:
$db = null;
```

### ❌ Don'ts

#### 1. Don't Use String Concatenation

```php
// ❌ NEVER do this - SQL injection!
$query = "SELECT * FROM users WHERE name = '$name'";
$stmt = $db->query($query);
```

#### 2. Don't Forget Error Mode

```php
// ❌ BAD - Silent errors
$db = new PDO($dsn, $user, $pass);

// ✅ GOOD - Throw exceptions
$db = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);
```

#### 3. Don't Use Root in Production

```php
// ❌ BAD - Security risk
"root", ""

// ✅ GOOD - Dedicated user
"app_user", "strong_password"
```

---

## 8️⃣ Common Errors

### Error 1: Connection Failed

```
PDOException: SQLSTATE[HY000] [2002] Connection refused
```

**Cause:** MySQL not running

**Solution:**
```bash
sudo systemctl start mysql
```

### Error 2: Unknown Database

```
PDOException: SQLSTATE[HY000] [1049] Unknown database 'pdo_project'
```

**Cause:** Database doesn't exist

**Solution:**
```sql
CREATE DATABASE pdo_project;
```

### Error 3: Access Denied

```
PDOException: SQLSTATE[HY000] [1045] Access denied for user 'root'@'localhost'
```

**Cause:** Wrong username/password

**Solution:** Check credentials

### Error 4: Syntax Error

```
PDOException: SQLSTATE[42000]: Syntax error
```

**Cause:** Invalid SQL

**Solution:** Check SQL syntax

---

## 📝 Super Short Note

### Database Class Summary

```php
class Database {
    public static function connect() {
        return new PDO(
            "mysql:host=localhost;dbname=pdo_project;charset=utf8mb4",
            "root",
            "",
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    }
}
```

**Key Points:**
- ✅ Central DB connection
- ✅ Uses PDO (secure)
- ✅ Static `connect()` method
- ✅ Returns PDO object
- ✅ Exception mode enabled
- ✅ Associative array fetch

**Usage:**
```php
$db = Database::connect();
$stmt = $db->prepare("SELECT * FROM users");
$stmt->execute();
$users = $stmt->fetchAll();
```

---

## 💡 Summary

| Concept | Explanation |
|---------|-------------|
| **Purpose** | Central database connection |
| **Static method** | Call without creating object |
| **PDO** | PHP Data Objects (database abstraction) |
| **DSN** | Database connection string |
| **Exception mode** | Throw errors instead of silent fail |
| **Fetch mode** | Return associative arrays |
| **Prepared statements** | Prevent SQL injection |

### Django vs PHP

| Feature | Django | PHP PDO |
|---------|--------|---------|
| Configuration | `settings.py` | Class method |
| Query | ORM (no SQL) | SQL with PDO |
| Security | Auto | Manual prepared statements |
| Syntax | Pythonic | SQL + PHP |

---

এখন তুমি PHP PDO Database class সম্পূর্ণ বুঝে গেছো! 💪

**Django ORM = No SQL needed**  
**PHP PDO = Full SQL control**

Both are secure when used properly! 🔐🚀

**Updated:** February 2026  
**Type:** PHP PDO Database Connection Guide