# Learning stage of PHP


```bash
✔ Template basics (done)
➡ Form + POST
➡ Security (escape)
➡ Mini MVC
➡ DB (PDO)
➡ Laravel
```



# PHP Quick Setup for Django Developers

Run PHP like Django - understand PHP through Django comparisons.

---

## 🐍 Django vs PHP Comparison

| Django | PHP | Why? |
|--------|-----|------|
| `python manage.py runserver` | `php -S localhost:8000` | Both start development server |
| `pip install package` | `sudo apt install php-extension` | Install dependencies |
| `python script.py` | `php script.php` | Run single file |
| Django templates | PHP files (mix HTML+PHP) | Render dynamic content |
| `settings.py` | `php.ini` | Configuration file |
| Gunicorn/uWSGI | Apache/Nginx+PHP-FPM | Production server |

---

## 📦 Install PHP (Like installing Python)

```bash
sudo apt update
sudo apt install php                # Like: sudo apt install python3
```

---

## 🚀 Run PHP File

### Method 1: Built-in Server (Like Django runserver)

```bash
# Create your PHP file
nano index.php
```

```php
<?php
// Like Django views.py
echo "<h1>Hello from PHP!</h1>";

// Like Django's request.META - shows all PHP info
phpinfo();
?>
```

```bash
# Start server (EXACTLY like Django runserver!)
php -S localhost:8000

# Access: http://localhost:8000
# Just like Django!
```

### Method 2: Apache (Like Django + Gunicorn)

```bash
# Install Apache + PHP (like installing Gunicorn)
sudo apt install apache2 libapache2-mod-php

# Move file to web root (like Django's static/media files)
sudo cp index.php /var/www/html/

# Access: http://localhost/index.php
```

---

## 📦 PHP Extensions (Like Django Apps/Packages)

### Why Extensions?

Django uses packages (`pip install`), PHP uses extensions (`apt install php-*`)

```bash
# Install common extensions
sudo apt install php-curl php-mbstring php-xml php-zip php-mysql php-gd
```

### Extension Mapping (Django → PHP)

| Django Package | PHP Extension | Purpose |
|----------------|---------------|---------|
| `requests` | `php-curl` | Make HTTP requests (API calls) |
| `Pillow` | `php-gd` | Image processing (upload, resize) |
| `mysqlclient` | `php-mysql` | MySQL database connection |
| `psycopg2` | `php-pgsql` | PostgreSQL database |
| Built-in | `php-mbstring` | Handle Unicode/UTF-8 (like Django's default) |
| `lxml` | `php-xml` | Parse XML/HTML |
| `zipfile` | `php-zip` | Create/extract ZIP files |
| `python-json` | `php-json` | JSON encoding/decoding (built-in both) |

### Extension Details

**php-curl** (Like `requests` library)
```php
// Django: import requests; requests.get('url')
// PHP:
$ch = curl_init('https://api.example.com');
curl_exec($ch);
```

**php-mysql** (Like `mysqlclient` or Django ORM)
```php
// Django: from django.db import connection
// PHP:
$conn = new mysqli('localhost', 'user', 'pass', 'db');
$result = $conn->query("SELECT * FROM users");
```

**php-gd** (Like `Pillow`)
```php
// Django: from PIL import Image
// PHP:
$img = imagecreatefromjpeg('photo.jpg');
imagejpeg($img, 'output.jpg', 90);
```

**php-mbstring** (Like Django's Unicode support)
```php
// Django handles UTF-8 by default
// PHP needs mbstring for proper Unicode
$length = mb_strlen('হ্যালো'); // Bangla text
```

---

## 🔍 Verify Installation

```bash
php -v                    # Like: python --version
php -m                    # Like: pip list (shows installed modules)
php -i                    # Like: django-admin --version (detailed info)
```

---

## 📝 Django-Style Project Structure

### Django Project
```
myproject/
├── manage.py
├── myproject/
│   ├── settings.py
│   ├── urls.py
│   └── views.py
└── templates/
    └── index.html
```

### PHP Equivalent
```
myapp/
├── index.php           # Entry point (like urls.py + views.py combined)
├── config.php          # Settings (like settings.py)
├── database.php        # DB connection (like Django ORM)
└── templates/          # HTML templates (same concept!)
    └── header.php
```

---

## 🛑 Stop Server

**Django:** `Ctrl + C`  
**PHP:** `Ctrl + C` (same!)

Apache: `sudo systemctl stop apache2` (like stopping Gunicorn)

---

## 💡 Quick Commands Comparison

| Django | PHP | Purpose |
|--------|-----|---------|
| `python manage.py runserver` | `php -S localhost:8000` | Start dev server |
| `python manage.py runserver 8080` | `php -S localhost:8080` | Custom port |
| `python manage.py runserver 0:8000` | `php -S 0.0.0.0:8000` | Network access |
| `python script.py` | `php script.php` | Run single file |
| `python -c "print('test')"` | `php -r "echo 'test';"` | Inline code |
| `python -i` | `php -a` | Interactive shell |

---

## 🎯 Key Differences for Django Developers

### 1. No ORM by Default
**Django:** Built-in ORM  
**PHP:** Need to write SQL or use libraries (Laravel has ORM)

### 2. Templates
**Django:** Separate template files  
**PHP:** Mix HTML + PHP in same file (like old Django templates)

### 3. URL Routing
**Django:** `urls.py` with patterns  
**PHP:** File-based routing (file.php = /file.php)

### 4. Settings
**Django:** `settings.py`  
**PHP:** `php.ini` (system-wide) or `config.php` (app-specific)

### 5. Development Server
**Django:** `python manage.py runserver`  
**PHP:** `php -S localhost:8000` (VERY similar!)

---

## 🔧 Example: Django View → PHP

**Django (views.py):**
```python
from django.http import HttpResponse
import json

def index(request):
    data = {'message': 'Hello', 'status': 'success'}
    return HttpResponse(json.dumps(data), content_type='application/json')
```

**PHP (index.php):**
```php
<?php
header('Content-Type: application/json');

$data = ['message' => 'Hello', 'status' => 'success'];
echo json_encode($data);
?>
```

**Both do the same thing!**

---

## 📚 Quick Start (Django Developer Way)

```bash
# 1. Install PHP (like pip install django)
sudo apt install php php-mysql php-curl php-gd

# 2. Create index.php
echo "<?php echo '<h1>Hello World</h1>'; ?>" > index.php

# 3. Run server (like manage.py runserver)
php -S localhost:8000

# 4. Open browser
# http://localhost:8000
```


---


## 📊 PHP Commands Reference

| Task | Command |
|------|---------|
| **Install PHP** | `sudo apt install php` |
| **Check version** | `php -v` |
| **Run PHP file** | `php file.php` |
| **Interactive shell** | `php -a` |
| **Run code inline** | `php -r "echo 'test';"` |
| **Start web server** | `php -S localhost:8000` |
| **Check extensions** | `php -m` |
| **Show PHP info** | `php -i` |
| **Find php.ini** | `php --ini` |
| **Check syntax** | `php -l file.php` |

---


---

## 💡 Pro Tips for Django Devs

1. **PHP doesn't have virtual environments** - Extensions install system-wide
2. **No migrations** - Manual SQL or use frameworks (Laravel)
3. **File = Route** - `about.php` is automatically `/about.php`
4. **Mix code & HTML** - Like Jinja2 but more messy
5. **Global variables** - `$_GET`, `$_POST` (like Django's `request.GET`)
6. **Use Composer** - Like `pip` for PHP libraries

---

**Bottom line:** PHP's built-in server works EXACTLY like Django's runserver!

Just `php -S localhost:8000` and you're running! 🚀
  