# Browser Script Control & CSP (Content Security Policy)

Complete guide to controlling JavaScript execution and preventing XSS attacks.

---

## 📋 Table of Contents

1. [Can You Turn Off JavaScript?](#1-can-you-turn-off-javascript)
2. [Content Security Policy (CSP)](#2-content-security-policy-csp)
3. [HttpOnly Cookies](#3-httponly-cookies)
4. [CSP Implementation](#4-csp-implementation)
5. [CSP Directives Explained](#5-csp-directives-explained)
6. [Inline Script Control](#6-inline-script-control)
7. [Never Trust User HTML](#7-never-trust-user-html)
8. [Django vs PHP Security](#8-django-vs-php-security)
9. [Defense in Depth](#9-defense-in-depth)
10. [Complete Example](#10-complete-example)

---

## 1️⃣ Can You Turn Off JavaScript?

### 🔴 Important Truth

> **❗ JavaScript পুরোপুরি off করা যায় না**

**কিন্তু...**

> **✅ কোন script চলবে, কোনটা চলবে না — সেটা তুমি control করতে পারো**

### এই Control-এর নাম

👉 **Content Security Policy (CSP)**

### Why You Can't Turn Off JavaScript Completely

Modern web applications need JavaScript:
- ✅ Form validation
- ✅ Dynamic content loading
- ✅ User interactions
- ✅ API calls
- ✅ Single Page Applications (React, Vue, etc.)

**But you CAN:**
- ✅ Block inline scripts
- ✅ Allow only your scripts
- ✅ Block external malicious scripts
- ✅ Prevent user-injected scripts

---

## 2️⃣ Content Security Policy (CSP)

### 🧠 CSP কী?

**সহজ ভাষায়:**

> "Browser-কে বলা: আমার site-এ শুধু আমি যেসব script allow করেছি সেগুলাই চলবে, user input থেকে কোনো script চলবে না।"

### How CSP Works

**Without CSP:**
```html
<!-- Attacker injects -->
<script>alert('Hacked!')</script>
<!-- Browser executes it ❌ -->
```

**With CSP:**
```html
<!-- Attacker injects -->
<script>alert('Hacked!')</script>
<!-- Browser BLOCKS it ✅ -->
<!-- Console shows: "Refused to execute inline script" -->
```

### CSP is Like a Bouncer

```
┌──────────────────────────┐
│   Your Website           │
│                          │
│  ┌────────────────┐      │
│  │   CSP Bouncer  │      │
│  │   "Who are you?"│      │
│  └────────────────┘      │
│         │                │
│    ✅   │   ❌            │
│  Your   │  Attacker's    │
│  Script │  Script        │
└──────────────────────────┘
```

---

## 3️⃣ HttpOnly Cookies

### ✅ Step 1: Cookie চুরি ঠেকানো

### PHP Implementation

```php
<?php
// Set session cookie as HttpOnly and Secure
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);      // Only over HTTPS
ini_set('session.cookie_samesite', 'Strict');
session_start();
?>
```

### Or Set Individual Cookies

```php
<?php
setcookie('session_id', $value, [
    'expires' => time() + 3600,
    'path' => '/',
    'domain' => 'example.com',
    'secure' => true,        // HTTPS only
    'httponly' => true,      // JavaScript can't access
    'samesite' => 'Strict'   // CSRF protection
]);
?>
```

### 📌 Result

**Without HttpOnly:**
```javascript
// Attacker can do this
console.log(document.cookie);
// Output: "session_id=abc123; user_id=456"
```

**With HttpOnly:**
```javascript
// Attacker tries this
console.log(document.cookie);
// Output: "" (empty - cookie hidden from JavaScript)
```

### Django Equivalent

```python
# settings.py
SESSION_COOKIE_HTTPONLY = True
SESSION_COOKIE_SECURE = True
SESSION_COOKIE_SAMESITE = 'Strict'
CSRF_COOKIE_HTTPONLY = True
CSRF_COOKIE_SECURE = True
```

👉 **Django এটা default করে দেয়** ✅

---

## 4️⃣ CSP Implementation

### ✅ Step 2: Content Security Policy (সবচেয়ে Powerful)

### Basic CSP Header (PHP)

```php
<?php
header("Content-Security-Policy: default-src 'self'; script-src 'self'; object-src 'none'; base-uri 'self';");
?>
```

### Put at Top of Every PHP File

```php
<?php
// security.php
function set_security_headers() {
    header("Content-Security-Policy: default-src 'self'; script-src 'self'; object-src 'none';");
    header("X-Content-Type-Options: nosniff");
    header("X-Frame-Options: DENY");
    header("X-XSS-Protection: 1; mode=block");
}

set_security_headers();
?>
```

### Include in All Pages

```php
<?php
require_once 'security.php';
?>
<!DOCTYPE html>
<html>
...
```

---

## 5️⃣ CSP Directives Explained

### 🧠 এর মানে কী?

| Rule | Meaning | Example |
|------|---------|---------|
| `default-src 'self'` | শুধু নিজের site থেকে resource load | Images, CSS, fonts from your domain only |
| `script-src 'self'` | Inline/injected script চলবে না | Blocks `<script>alert()</script>` |
| `object-src 'none'` | Flash, plugin block | No `<object>`, `<embed>` tags |
| `base-uri 'self'` | Base tag attack block | Prevents `<base>` hijacking |

### Detailed Directive Examples

#### 1. default-src

```php
// Allow resources only from your domain
"default-src 'self'"

// Result:
✅ <img src="/images/logo.png">        // Your domain
❌ <img src="http://evil.com/image.png"> // External domain
```

#### 2. script-src

```php
// Only scripts from your domain
"script-src 'self'"

// Result:
✅ <script src="/js/app.js"></script>   // Your domain
❌ <script>alert('XSS')</script>        // Inline script
❌ <script src="http://evil.com/bad.js"> // External
```

#### 3. style-src

```php
// Only styles from your domain
"style-src 'self'"

// Result:
✅ <link href="/css/style.css">         // Your domain
❌ <style>body{background:red}</style>  // Inline style
```

#### 4. img-src

```php
// Allow images from your domain + CDN
"img-src 'self' https://cdn.example.com"

// Result:
✅ <img src="/images/logo.png">
✅ <img src="https://cdn.example.com/pic.jpg">
❌ <img src="http://random-site.com/image.png">
```

#### 5. object-src

```php
// Block Flash and plugins
"object-src 'none'"

// Result:
❌ <object data="flash.swf"></object>
❌ <embed src="plugin.pdf"></embed>
```

### Common CSP Configurations

#### Strict (Most Secure)
```php
header("Content-Security-Policy: 
    default-src 'self'; 
    script-src 'self'; 
    style-src 'self'; 
    img-src 'self'; 
    font-src 'self'; 
    object-src 'none'; 
    base-uri 'self';
");
```

#### Moderate (Allow CDN)
```php
header("Content-Security-Policy: 
    default-src 'self'; 
    script-src 'self' https://cdn.jsdelivr.net; 
    style-src 'self' https://cdn.jsdelivr.net; 
    img-src 'self' data: https:; 
    font-src 'self' https://fonts.googleapis.com;
");
```

#### Development (Report Only)
```php
// Test CSP without blocking
header("Content-Security-Policy-Report-Only: 
    default-src 'self'; 
    report-uri /csp-report
");
```

---

## 6️⃣ Inline Script Control

### ✅ Step 3: Inline JS Disable করা (Strict Mode)

### আরো Strict চাইলে

```php
<?php
header("Content-Security-Policy: script-src 'self'");
?>
```

### What Gets Blocked

```html
<!-- ❌ Blocked: Inline script -->
<script>alert('Hello')</script>

<!-- ❌ Blocked: Inline event handler -->
<button onclick="doSomething()">Click</button>
<div onload="init()">Content</div>

<!-- ❌ Blocked: javascript: protocol -->
<a href="javascript:void(0)">Link</a>

<!-- ✅ Allowed: External script from same domain -->
<script src="/js/app.js"></script>
```

### 🔥 Try This Attack After CSP

**Attacker injects:**
```html
<script>alert("hack")</script>
```

**Result:**
```
➡️ Browser console error:
"Refused to execute inline script because it violates the following Content Security Policy directive: script-src 'self'"

➡️ Script execute হবে না 😎
```

### Allow Inline Scripts (If Needed)

#### Option 1: Nonce (Random Token)
```php
<?php
$nonce = base64_encode(random_bytes(16));
header("Content-Security-Policy: script-src 'self' 'nonce-$nonce'");
?>

<!-- Only scripts with matching nonce will execute -->
<script nonce="<?= $nonce ?>">
    // This script is allowed
    console.log('Allowed!');
</script>
```

#### Option 2: Hash
```php
<?php
header("Content-Security-Policy: script-src 'self' 'sha256-abc123...'");
?>

<!-- Only scripts with matching hash will execute -->
<script>console.log('Allowed');</script>
```

---

## 7️⃣ Never Trust User HTML

### ✅ Step 4: Always Escape User Input

### ❌ এটা করো না

```php
<?php
$comment = $_POST['comment'];
echo $comment;  // DANGEROUS!
?>
```

### ✅ এটা করো

```php
<?php
$comment = $_POST['comment'];
echo htmlspecialchars($comment, ENT_QUOTES, 'UTF-8');
?>
```

### Create Helper Function

```php
<?php
function e($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

// Usage
echo e($comment);
echo e($username);
echo e($title);
?>
```

### Even With CSP, Escape is Essential

**Why?**
- CSP blocks execution, but HTML can still break layout
- CSP might be disabled or misconfigured
- Defense in depth = multiple layers

**Example:**
```php
// CSP blocks script execution
// But without escaping, HTML breaks:
$comment = "</div><h1>Fake Title</h1><div>";
echo $comment; // Breaks your page layout!

// With escaping:
echo e($comment); // Shows as text, safe!
```

---

## 8️⃣ Django vs PHP Security

### 🔁 Full Comparison

| Security Feature | Django | PHP |
|------------------|--------|-----|
| **Auto escape** | ✅ Yes | ❌ Manual |
| **CSRF protection** | ✅ Yes | ❌ Manual |
| **HttpOnly cookie** | ✅ Default | ⚠️ Manual setup |
| **Secure cookie** | ✅ Default | ⚠️ Manual setup |
| **CSP** | ⚠️ Optional (django-csp) | ⚠️ Manual header |
| **XSS protection** | ✅ Auto | ❌ Manual |
| **SQL injection** | ✅ ORM (auto) | ⚠️ Prepared statements |
| **Session security** | ✅ Built-in | ⚠️ Configure yourself |

### Django Security Settings

```python
# settings.py

# Auto-escaping (default: True)
TEMPLATES = [{
    'OPTIONS': {
        'autoescape': True,
    }
}]

# CSRF protection (default: enabled)
MIDDLEWARE = [
    'django.middleware.csrf.CsrfViewMiddleware',
]

# Secure cookies
SESSION_COOKIE_SECURE = True
SESSION_COOKIE_HTTPONLY = True
CSRF_COOKIE_SECURE = True
CSRF_COOKIE_HTTPONLY = True

# CSP (install django-csp)
CSP_DEFAULT_SRC = ("'self'",)
CSP_SCRIPT_SRC = ("'self'",)
```

### PHP Equivalent Setup

```php
<?php
// security-config.php

// Session security
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');

// CSP headers
function set_security_headers() {
    header("Content-Security-Policy: default-src 'self'; script-src 'self'");
    header("X-Content-Type-Options: nosniff");
    header("X-Frame-Options: DENY");
    header("X-XSS-Protection: 1; mode=block");
    header("Referrer-Policy: strict-origin-when-cross-origin");
}

// Escape helper
function e($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

// CSRF token generation
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// CSRF token verification
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && 
           hash_equals($_SESSION['csrf_token'], $token);
}
?>
```

---

## 9️⃣ Defense in Depth

### 🧠 Golden Security Rule

> **🔐 Defense in Depth = Multiple Layers**

```
Escape + CSP + Secure Cookie + CSRF = Strong Protection 💪
```

### Security Layers

```
Layer 1: Input Validation
    ↓
Layer 2: Output Escaping
    ↓
Layer 3: CSP Headers
    ↓
Layer 4: HttpOnly Cookies
    ↓
Layer 5: CSRF Tokens
    ↓
Layer 6: HTTPS
```

### Why Multiple Layers?

**If one fails, others protect:**

| Layer | Fails | Other Layers Save You |
|-------|-------|----------------------|
| Forget escape | ❌ | CSP blocks execution |
| CSP misconfigured | ❌ | Escaping prevents injection |
| Cookie exposed | ❌ | HttpOnly prevents JS access |
| CSRF token bypassed | ❌ | SameSite cookie blocks |

### Complete Protection Checklist

```php
<?php
// ✅ 1. Secure session
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
session_start();

// ✅ 2. CSP headers
header("Content-Security-Policy: default-src 'self'; script-src 'self'");

// ✅ 3. Additional headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");

// ✅ 4. CSRF token
$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;

// ✅ 5. Escape output
function e($v) { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }

// ✅ 6. Validate input
$username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);

// ✅ 7. Prepared statements (database)
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
?>
```

---

## 🔟 Complete Example

### Secure PHP Application

```php
<?php
// config/security.php
session_start();

// Security headers
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'nonce-" . $_SESSION['nonce'] . "'");
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");

// Secure session
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');

// Helper functions
function e($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf() {
    if (!isset($_POST['csrf_token']) || 
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('CSRF validation failed');
    }
}

// Generate nonce for inline scripts
if (!isset($_SESSION['nonce'])) {
    $_SESSION['nonce'] = base64_encode(random_bytes(16));
}
?>
```

```php
<?php
// index.php
require_once 'config/security.php';

$name = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = $_POST['name'] ?? '';
    $message = "Hello, " . e($name) . "!";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Secure Form</title>
</head>
<body>
    <?php if ($message): ?>
        <p><?= $message ?></p>
    <?php endif; ?>
    
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <input type="text" name="name" value="<?= e($name) ?>">
        <button type="submit">Submit</button>
    </form>
    
    <script nonce="<?= $_SESSION['nonce'] ?>">
        // This inline script is allowed because of nonce
        console.log('Secure app running');
    </script>
</body>
</html>
```

### Security Features Included

✅ CSP with nonce  
✅ HttpOnly cookies  
✅ CSRF protection  
✅ Output escaping  
✅ Secure headers  
✅ XSS prevention  

---

## 📝 Security Checklist (Save This)

```markdown
# XSS Protection Checklist

## Must Have
✔ Escape output (htmlspecialchars)
✔ HttpOnly cookies
✔ Content Security Policy
✔ CSRF tokens
✔ Never trust user input

## CSP Header Example
Content-Security-Policy:
    default-src 'self';
    script-src 'self';
    style-src 'self';
    img-src 'self' data: https:;
    font-src 'self';
    object-src 'none';
    base-uri 'self';

## Cookie Settings
session.cookie_httponly = 1
session.cookie_secure = 1
session.cookie_samesite = Strict

## Additional Headers
X-Frame-Options: DENY
X-Content-Type-Options: nosniff
X-XSS-Protection: 1; mode=block
```

---

## 💡 Summary

### Key Points

1. **Can't turn off JS completely** - But can control what runs
2. **CSP = Powerful protection** - Blocks unauthorized scripts
3. **HttpOnly cookies** - Prevents cookie theft
4. **Defense in depth** - Multiple security layers
5. **Django does most automatically** - PHP requires manual setup

### Protection Layers

```
✅ Input Validation
✅ Output Escaping (htmlspecialchars)
✅ CSP Headers
✅ HttpOnly Cookies
✅ CSRF Tokens
✅ Secure Cookies (HTTPS + SameSite)
```

### Final Rule

> **Escape + CSP + HttpOnly + CSRF = Maximum Security 🔐**

---

এখন তুমি browser script control সম্পূর্ণ বুঝে গেছো! 💪🔐

**Django করে automatically, PHP-তে manually setup করতে হয়।**

Security = Responsibility! 🚀

**Updated:** February 2026  
**Type:** Browser Script Control & CSP Guide