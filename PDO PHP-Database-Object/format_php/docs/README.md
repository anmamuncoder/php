
# 🐘 PDO (PHP Data Objects) —
---

# অধ্যায় ১: PDO কী এবং কেন দরকার?

## ১.১ PDO-এর পূর্ণ অর্থ

**PDO = PHP Data Objects**

PDO হলো PHP-এর একটি built-in **extension** বা **library** যেটা দিয়ে আমরা PHP থেকে database-এর সাথে কথা বলতে পারি।

সহজ ভাষায় বলতে গেলে — আমাদের PHP code এবং database (যেমন MySQL) এর মাঝখানে একটি **দূত** হিসেবে কাজ করে PDO। PHP যা বলে, PDO সেটা database কে বোঝায়, এবং database যা বলে, PDO সেটা PHP-কে বোঝায়।

```
PHP Code  →  PDO  →  Database (MySQL/PostgreSQL/SQLite...)
                ↑
         (মাঝখানের দূত)
```

---

## ১.২ PDO আসার আগে কী ছিল?

PDO আসার আগে PHP-তে database-এর সাথে কাজ করার জন্য আলাদা আলাদা function ছিল:

- MySQL-এর জন্য: `mysql_connect()`, `mysql_query()` ইত্যাদি (এখন সম্পূর্ণ বাতিল)
- MySQLi-এর জন্য: `mysqli_connect()`, `mysqli_query()` ইত্যাদি (এখনও চলে)

**সমস্যা কী ছিল?**
যদি তুমি MySQL ব্যবহার করতে, কিন্তু পরে PostgreSQL-এ যেতে চাইতে — তাহলে পুরো code নতুন করে লিখতে হতো কারণ সব function আলাদা ছিল।

PDO এই সমস্যার সমাধান করেছে। PDO-তে একই code দিয়ে যেকোনো database ব্যবহার করা যায়।

---

## ১.৩ PDO কেন ব্যবহার করবো?

### ১) Database Independent (যেকোনো DB চলবে)

PDO একটিমাত্র interface দিয়ে ১২টিরও বেশি database সাপোর্ট করে। আজ MySQL, কাল PostgreSQL — code একই থাকবে, শুধু connection string পরিবর্তন করতে হবে।

### ২) SQL Injection Protection (নিরাপত্তা)

PDO-এর **Prepared Statement** feature SQL Injection attack থেকে রক্ষা করে। এটি PDO ব্যবহারের সবচেয়ে বড় কারণ।

### ৩) Clean Code

PDO Object-Oriented Programming (OOP) style এ লেখা যায়, তাই code দেখতে পরিষ্কার এবং professional মনে হয়।

### ৪) Error Handling

Exception ব্যবহার করে error সহজে ধরা যায়।

---

## ১.৪ PDO কোন কোন Database সাপোর্ট করে?

| Database | Driver নাম |
|---|---|
| MySQL / MariaDB | `pdo_mysql` |
| PostgreSQL | `pdo_pgsql` |
| SQLite | `pdo_sqlite` |
| Oracle | `pdo_oci` |
| Microsoft SQL Server | `pdo_sqlsrv` |
| IBM DB2 | `pdo_ibm` |
| Firebird | `pdo_firebird` |

---

# অধ্যায় ২: DSN (Data Source Name) বোঝা

## ২.১ DSN কী?

**DSN = Data Source Name**

DSN হলো একটি string যেখানে database connection-এর সব তথ্য থাকে। এটি PDO-কে বলে দেয়:
- কোন ধরনের database? (MySQL, PostgreSQL?)
- কোথায় আছে server? (localhost?)
- কোন database-এর সাথে connect করতে হবে?
- কোন character encoding ব্যবহার করতে হবে?

## ২.২ DSN-এর Format

```
driver:host=hostname;dbname=database_name;charset=encoding
```

**উদাহরণ বিশ্লেষণ:**

```php
$dsn = "mysql:host=localhost;dbname=school_db;charset=utf8mb4";
//      ↑     ↑              ↑               ↑
//    driver  server         database নাম    encoding
```

| অংশ | অর্থ |
|---|---|
| `mysql:` | MySQL database ব্যবহার করবো |
| `host=localhost` | Server একই machine-এ আছে |
| `dbname=school_db` | `school_db` নামের database ব্যবহার করবো |
| `charset=utf8mb4` | বাংলাসহ সব ভাষা সাপোর্ট করবে |

> **টিপ:** সবসময় `utf8mb4` ব্যবহার করো, শুধু `utf8` নয়। `utf8mb4` emoji এবং বাংলা সব ঠিকমতো store করতে পারে।

## ২.৩ বিভিন্ন Database-এর DSN Format

```php
// MySQL
$dsn = "mysql:host=localhost;dbname=mydb;charset=utf8mb4";

// PostgreSQL
$dsn = "pgsql:host=localhost;dbname=mydb";

// SQLite (file-based, কোনো server লাগে না)
$dsn = "sqlite:/path/to/database.db";

// SQL Server
$dsn = "sqlsrv:Server=localhost;Database=mydb";
```

---

# অধ্যায় ৩: Database Connection তৈরি করা

## ৩.১ Basic Connection

```php
<?php
// ধাপ ১: DSN তৈরি করো
$dsn      = "mysql:host=localhost;dbname=school_db;charset=utf8mb4";
$username = "root";      // database user
$password = "";          // database password (XAMPP-এ সাধারণত খালি)

try {
    // ধাপ ২: PDO object তৈরি করো
    $pdo = new PDO($dsn, $username, $password);
    
    // ধাপ ৩: Error mode সেট করো
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Database connection সফল হয়েছে!";
    
} catch (PDOException $e) {
    // connection ব্যর্থ হলে এখানে আসবে
    echo "Connection ব্যর্থ: " . $e->getMessage();
}
?>
```

**এই code কীভাবে কাজ করে:**

1. `new PDO(...)` — একটি PDO object তৈরি করে এবং database-এ connect করার চেষ্টা করে
2. `setAttribute(...)` — PDO-কে বলছি, কোনো error হলে exception throw করো
3. `try { }` — এখানে যদি কোনো error হয়, সরাসরি `catch` block-এ চলে যাবে
4. `catch (PDOException $e) { }` — error ধরে এবং message দেখায়

## ৩.২ Connection আলাদা File-এ রাখা (Best Practice)

Real project-এ connection code আলাদা file-এ রাখা উচিত।

**`config/database.php` ফাইল:**

```php
<?php
function getConnection() {
    $dsn      = "mysql:host=localhost;dbname=school_db;charset=utf8mb4";
    $username = "root";
    $password = "";
    
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    try {
        return new PDO($dsn, $username, $password, $options);
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}
?>
```

**অন্য যেকোনো file-এ ব্যবহার:**

```php
<?php
require_once 'config/database.php';
$pdo = getConnection();
// এখন $pdo ব্যবহার করো
?>
```

---

# অধ্যায় ৪: SQL Injection কী এবং কেন বিপজ্জনক?

PDO-এর সবচেয়ে গুরুত্বপূর্ণ feature বোঝার আগে SQL Injection বুঝতে হবে।

## ৪.১ SQL Injection কী?

SQL Injection হলো এমন একটি attack যেখানে hacker input field-এ SQL code ঢুকিয়ে দেয় এবং database-এ অননুমোদিত কাজ করতে পারে।

## ৪.২ SQL Injection-এর উদাহরণ

ধরো তোমার login form আছে। তুমি লিখলে:

```php
// ❌ বিপজ্জনক code
$email = $_POST['email'];  // user যা দিয়েছে
$sql = "SELECT * FROM users WHERE email = '$email'";
$result = $pdo->query($sql);
```

স্বাভাবিক ক্ষেত্রে query হয়:
```sql
SELECT * FROM users WHERE email = 'test@gmail.com'
```

কিন্তু hacker যদি email field-এ এটি দেয়:
```
' OR '1'='1
```

তাহলে query হয়ে যায়:
```sql
SELECT * FROM users WHERE email = '' OR '1'='1'
```

যেহেতু `'1'='1'` সবসময় `true`, তাই এটি **সব user-এর তথ্য** দিয়ে দেবে!

আরও ভয়ংকর attack:
```
'; DROP TABLE users; --
```

এতে `users` table সম্পূর্ণ মুছে যেতে পারে!

## ৪.৩ PDO কীভাবে রক্ষা করে?

PDO-এর **Prepared Statement** user-এর input কে SQL code হিসেবে treat করে না। এটি data এবং SQL query-কে আলাদা রাখে।

```php
// ✅ নিরাপদ code — PDO prepared statement
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
```

এখন hacker যদি `' OR '1'='1` দেয়, PDO সেটাকে হুবহু একটি email address হিসেবে দেখবে, SQL code হিসেবে নয়। তাই কোনো match হবে না।

---

# অধ্যায় ৫: Prepared Statement বিস্তারিত

## ৫.১ Prepared Statement কীভাবে কাজ করে?

Prepared Statement দুই ধাপে কাজ করে:

**ধাপ ১: Prepare** — SQL template তৈরি করো (placeholder দিয়ে)
```php
$stmt = $pdo->prepare("INSERT INTO students (name, age) VALUES (?, ?)");
//                                                               ↑  ↑
//                                                    placeholder (পরে মান বসবে)
```

**ধাপ ২: Execute** — প্রকৃত মান দিয়ে চালাও
```php
$stmt->execute(["রাহুল", 20]);
```

PDO ভেতরে ভেতরে এভাবে কাজ করে:
```
SQL Template:  INSERT INTO students (name, age) VALUES (?, ?)
Actual Values: "রাহুল", 20
Final Query:   INSERT INTO students (name, age) VALUES ('রাহুল', 20)
```
মান গুলো আলাদাভাবে পাঠানো হয়, তাই SQL Injection সম্ভব না।

## ৫.২ দুই ধরনের Placeholder

### ধরন ১: Positional Placeholder (`?`)

`?` দিয়ে placeholder বোঝায়। execute()-এ array-এর মান ক্রম অনুযায়ী বসে।

```php
// প্রথম ? → $name, দ্বিতীয় ? → $email, তৃতীয় ? → $age
$stmt = $pdo->prepare("INSERT INTO users (name, email, age) VALUES (?, ?, ?)");
$stmt->execute([$name, $email, $age]);
```

> **সাবধান:** ক্রম মিলাতে হবে। `?` এর ক্রম এবং array-এর মানের ক্রম একই হতে হবে।

### ধরন ২: Named Placeholder (`:নাম`)

`:` দিয়ে শুরু করে নাম দেওয়া যায়। execute()-এ key-value pair দিতে হয়।

```php
$stmt = $pdo->prepare("INSERT INTO users (name, email, age) VALUES (:name, :email, :age)");
$stmt->execute([
    'name'  => $name,
    'email' => $email,
    'age'   => $age
]);
```

> Named placeholder বেশি readable। Column সংখ্যা বেশি হলে এটি ব্যবহার করো।

### কোনটি ব্যবহার করবো?

| পরিস্থিতি | Recommendation |
|---|---|
| ২-৩টি column | `?` ব্যবহার করো (সহজ) |
| ৪+ column | `:নাম` ব্যবহার করো (পড়তে সহজ) |
| Form data থেকে | `:নাম` ব্যবহার করো |
| Array থেকে | `?` ব্যবহার করো |

## ৫.৩ bindParam() এবং bindValue() দিয়ে আরও নিয়ন্ত্রণ

```php
$stmt = $pdo->prepare("INSERT INTO products (name, price, stock) VALUES (?, ?, ?)");

// bindValue() — সরাসরি মান bind করে
$stmt->bindValue(1, "Apple Phone", PDO::PARAM_STR);
$stmt->bindValue(2, 75000.00, PDO::PARAM_STR);
$stmt->bindValue(3, 50, PDO::PARAM_INT);

$stmt->execute();
```

**Data Type Constants:**

| Constant | ব্যবহার |
|---|---|
| `PDO::PARAM_STR` | String / Text |
| `PDO::PARAM_INT` | Integer (সংখ্যা) |
| `PDO::PARAM_BOOL` | Boolean (true/false) |
| `PDO::PARAM_NULL` | NULL value |

---

# অধ্যায় ৬: Data Fetch করা (SELECT)

## ৬.১ fetch() — একটি row নিয়ে আসা

```php
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([5]);

$student = $stmt->fetch(PDO::FETCH_ASSOC);
// এখন $student একটি array, যেমন:
// ['id' => 5, 'name' => 'রাহুল', 'age' => 20]

if ($student) {
    echo "নাম: " . $student['name'];
    echo "বয়স: " . $student['age'];
} else {
    echo "কোনো student পাওয়া যায়নি";
}
```

## ৬.২ fetchAll() — সব row একসাথে নিয়ে আসা

```php
$stmt = $pdo->query("SELECT * FROM students");
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
// এখন $students একটি 2D array

foreach ($students as $student) {
    echo $student['name'] . " - " . $student['age'] . "<br>";
}
```

## ৬.৩ Fetch Modes বিস্তারিত

Fetch mode বলে দেয় data কোন format-এ আসবে।

### PDO::FETCH_ASSOC (সবচেয়ে বেশি ব্যবহৃত)

Column নাম key হিসেবে আসে।

```php
$row = $stmt->fetch(PDO::FETCH_ASSOC);
// ফলাফল: ['id' => 1, 'name' => 'রাহুল', 'email' => 'rahul@gmail.com']

echo $row['name'];   // রাহুল
echo $row['email'];  // rahul@gmail.com
```

### PDO::FETCH_NUM

Index number key হিসেবে আসে।

```php
$row = $stmt->fetch(PDO::FETCH_NUM);
// ফলাফল: [0 => 1, 1 => 'রাহুল', 2 => 'rahul@gmail.com']

echo $row[0];  // 1 (id)
echo $row[1];  // রাহুল (name)
```

### PDO::FETCH_BOTH (default)

উভয় — column নাম এবং index দুটোই key হিসেবে আসে।

```php
$row = $stmt->fetch(PDO::FETCH_BOTH);
// উভয় দিয়ে access করা যাবে:
echo $row['name'];  // রাহুল
echo $row[1];       // রাহুল (একই জিনিস)
```

### PDO::FETCH_OBJ

Object হিসেবে আসে।

```php
$row = $stmt->fetch(PDO::FETCH_OBJ);
// ফলাফল: stdClass object

echo $row->name;   // রাহুল
echo $row->email;  // rahul@gmail.com
```

### PDO::FETCH_CLASS

Custom class-এর object হিসেবে আসে।

```php
class Student {
    public $id;
    public $name;
    public $age;
    
    public function introduce() {
        return "আমি {$this->name}, বয়স {$this->age}";
    }
}

$stmt->setFetchMode(PDO::FETCH_CLASS, 'Student');
$student = $stmt->fetch();

echo $student->introduce();  // আমি রাহুল, বয়স 20
```

---

# অধ্যায় ৭: CRUD — সম্পূর্ণ উদাহরণ

CRUD = **C**reate, **R**ead, **U**pdate, **D**elete

ধরো আমাদের `students` নামে একটি table আছে:

```sql
CREATE TABLE students (
    id    INT AUTO_INCREMENT PRIMARY KEY,
    name  VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    age   INT,
    grade VARCHAR(10)
);
```

## ৭.১ Create — নতুন data যোগ করা (INSERT)

```php
<?php
require_once 'config/database.php';
$pdo = getConnection();

$name  = "সায়েম আহমেদ";
$email = "sayem@example.com";
$age   = 22;
$grade = "A+";

try {
    $stmt = $pdo->prepare(
        "INSERT INTO students (name, email, age, grade) 
         VALUES (:name, :email, :age, :grade)"
    );
    
    $stmt->execute([
        'name'  => $name,
        'email' => $email,
        'age'   => $age,
        'grade' => $grade
    ]);
    
    $newId = $pdo->lastInsertId();  // সদ্য insert হওয়া row-এর ID
    echo "Student যোগ করা হয়েছে। ID: " . $newId;
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
```

**`lastInsertId()` কী করে?**
Insert করার পর নতুন row-এর auto-generated ID টি দেয়। এটি তখন কাজে লাগে যখন insert করার পরপরই সেই record-এর ID দরকার হয় (যেমন related table-এ সেই ID ব্যবহার করতে)।

## ৭.২ Read — data পড়া (SELECT)

### সব student-এর তালিকা দেখানো:

```php
<?php
$stmt = $pdo->query("SELECT * FROM students ORDER BY name ASC");
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($students) > 0) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>নাম</th><th>Email</th><th>বয়স</th><th>গ্রেড</th></tr>";
    
    foreach ($students as $student) {
        echo "<tr>";
        echo "<td>" . $student['id']    . "</td>";
        echo "<td>" . $student['name']  . "</td>";
        echo "<td>" . $student['email'] . "</td>";
        echo "<td>" . $student['age']   . "</td>";
        echo "<td>" . $student['grade'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "কোনো student নেই।";
}
?>
```

### নির্দিষ্ট একজন student:

```php
<?php
$id = 5;  // যার তথ্য চাই

$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if ($student) {
    echo "নাম: " . $student['name']  . "<br>";
    echo "Email: " . $student['email'] . "<br>";
    echo "বয়স: " . $student['age']   . "<br>";
} else {
    echo "ID $id এর কোনো student পাওয়া যায়নি।";
}
?>
```

### Search করা:

```php
<?php
$searchName = "সায়েম";  // যা খুঁজছো

$stmt = $pdo->prepare("SELECT * FROM students WHERE name LIKE ?");
$stmt->execute(['%' . $searchName . '%']);
// '%' মানে যেকোনো কিছু থাকতে পারে — আগেও, পরেও

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($results as $row) {
    echo $row['name'] . "<br>";
}
?>
```

## ৭.৩ Update — data পরিবর্তন করা

```php
<?php
$id      = 5;         // কোন student update করবো
$newName = "সায়েম ইসলাম";  // নতুন নাম
$newAge  = 23;        // নতুন বয়স

$stmt = $pdo->prepare(
    "UPDATE students SET name = :name, age = :age WHERE id = :id"
);

$stmt->execute([
    'name' => $newName,
    'age'  => $newAge,
    'id'   => $id
]);

$affectedRows = $stmt->rowCount();  // কতটি row পরিবর্তন হয়েছে

if ($affectedRows > 0) {
    echo "Student আপডেট হয়েছে। পরিবর্তিত row: " . $affectedRows;
} else {
    echo "কোনো পরিবর্তন হয়নি। হয়তো ID ভুল।";
}
?>
```

**`rowCount()` কী করে?**
INSERT, UPDATE, DELETE-এর পর কতটি row affected হয়েছে তা জানায়। যদি `0` হয়, কিছু ঘটেনি মানে।

## ৭.৪ Delete — data মুছে ফেলা

```php
<?php
$id = 5;  // কোন student মুছবো

$stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
$stmt->execute([$id]);

if ($stmt->rowCount() > 0) {
    echo "Student সফলভাবে মুছে ফেলা হয়েছে।";
} else {
    echo "ID $id খুঁজে পাওয়া যায়নি।";
}
?>
```

---

# অধ্যায় ৮: Error Handling (সঠিকভাবে Error ধরা)

## ৮.১ Error Mode কী?

PDO তিনটি mode-এ error দেখাতে পারে:

### Mode ১: ERRMODE_SILENT (নীরব — default)

Error হলে কিছু বলে না। নিজেকে `errorCode()` দিয়ে check করতে হবে।

```php
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);

$stmt = $pdo->query("SELECT * FROM non_existing_table");

if ($stmt === false) {
    $error = $pdo->errorInfo();
    echo "Error: " . $error[2];  // error message
}
```

> এটি ব্যবহার করা ঠিক না, কারণ silently fail হলে বুঝতে পারা কঠিন।

### Mode ২: ERRMODE_WARNING (সতর্কতা)

PHP Warning দেয়, কিন্তু code চলতে থাকে।

```php
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
// Warning দেখাবে কিন্তু execution বন্ধ হবে না
```

### Mode ৩: ERRMODE_EXCEPTION (Exception — ✅ সবচেয়ে ভালো)

Exception throw করে, যা try-catch দিয়ে ধরা যায়।

```php
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
```

## ৮.২ try-catch দিয়ে সঠিক Error Handling

```php
<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=mydb", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // ইচ্ছাকৃত ভুল — table নেই
    $stmt = $pdo->query("SELECT * FROM non_existing_table");
    
} catch (PDOException $e) {
    // Error সম্পর্কে বিস্তারিত তথ্য পাওয়া যাবে:
    echo "Error Message: " . $e->getMessage() . "<br>";
    echo "Error Code: "    . $e->getCode()    . "<br>";
    echo "Error File: "    . $e->getFile()    . "<br>";
    echo "Error Line: "    . $e->getLine()    . "<br>";
}
?>
```

## ৮.৩ Production-এ Error Handle করা

Production server-এ user-কে actual error দেখানো উচিত না (security risk):

```php
<?php
try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    // Error টি log file-এ লিখে রাখো
    error_log("Database Error: " . $e->getMessage(), 3, "logs/db_errors.log");
    
    // User-কে শুধু generic message দেখাও
    die("দুঃখিত, কিছু একটা সমস্যা হয়েছে। আবার চেষ্টা করুন।");
}
?>
```

---

# অধ্যায় ৯: Transaction — একাধিক Query একসাথে

## ৯.১ Transaction কী?

Transaction মানে হলো — হয় সব query সফল হবে, না হয় কোনোটাই হবে না।

**বাস্তব উদাহরণ:** ব্যাংক transfer
- Account A থেকে টাকা কাটো
- Account B-তে টাকা যোগ করো

যদি প্রথমটি সফল হয় কিন্তু দ্বিতীয়টি fail করে — তাহলে টাকা হারিয়ে যাবে! Transaction ব্যবহার করলে দুটো এক সাথে হবে, নয়তো দুটোই বাতিল হবে।

## ৯.২ Transaction-এর তিনটি মূল Method

| Method | কাজ |
|---|---|
| `beginTransaction()` | Transaction শুরু করো |
| `commit()` | সব change স্থায়ীভাবে save করো |
| `rollBack()` | সব change বাতিল করো (পূর্বের অবস্থায় ফিরে যাও) |

## ৯.৩ Transaction উদাহরণ

```php
<?php
try {
    $pdo->beginTransaction();  // Transaction শুরু
    
    // Query ১: Account A থেকে ৫০০০ টাকা কাটো
    $stmt1 = $pdo->prepare("UPDATE accounts SET balance = balance - ? WHERE id = ?");
    $stmt1->execute([5000, 1]);  // Account 1 থেকে কাটো
    
    // এখানে ইচ্ছাকৃত ভুল করলে দেখা যাবে rollback কাজ করছে
    // throw new Exception("ইচ্ছাকৃত error");
    
    // Query ২: Account B-তে ৫০০০ টাকা যোগ করো
    $stmt2 = $pdo->prepare("UPDATE accounts SET balance = balance + ? WHERE id = ?");
    $stmt2->execute([5000, 2]);  // Account 2-তে যোগ করো
    
    $pdo->commit();  // সব ঠিকঠাক, স্থায়ীভাবে save করো
    echo "Transfer সফল হয়েছে!";
    
} catch (Exception $e) {
    $pdo->rollBack();  // কোনো error হলে সব বাতিল
    echo "Transfer ব্যর্থ! কারণ: " . $e->getMessage();
}
?>
```

**Flow বোঝা:**

```
beginTransaction()
    ↓
Query ১ চলে → সাময়িক পরিবর্তন
    ↓
Query ২ চলে → সাময়িক পরিবর্তন
    ↓
কোনো error? → না → commit() → চিরস্থায়ী
              → হ্যাঁ → rollBack() → সব বাতিল
```

---

# অধ্যায় ১০: rowCount() এবং lastInsertId()

## ১০.১ rowCount()

Query-তে কতটি row affected হয়েছে তা জানায়।

```php
// INSERT
$stmt = $pdo->prepare("INSERT INTO logs (message) VALUES (?)");
$stmt->execute(["User logged in"]);
echo $stmt->rowCount();  // 1 (একটি row insert হয়েছে)

// UPDATE
$stmt = $pdo->prepare("UPDATE users SET status = 'inactive' WHERE last_login < ?");
$stmt->execute(['2023-01-01']);
echo $stmt->rowCount();  // যতজন inactive হয়েছে

// DELETE
$stmt = $pdo->prepare("DELETE FROM temp_data WHERE created_at < ?");
$stmt->execute(['2024-01-01']);
echo $stmt->rowCount();  // যতটি row মুছেছে
```

> **সতর্কতা:** SELECT query-তে `rowCount()` সব database-এ কাজ নাও করতে পারে। SELECT-এর জন্য `count($stmt->fetchAll())` ব্যবহার করো।

## ১০.২ lastInsertId()

সর্বশেষ INSERT করা row-এর auto_increment ID দেয়।

```php
$stmt = $pdo->prepare("INSERT INTO orders (product, quantity) VALUES (?, ?)");
$stmt->execute(["Laptop", 2]);

$orderId = $pdo->lastInsertId();
echo "নতুন Order ID: " . $orderId;

// এই ID দিয়ে order_details table-এ data ঢুকানো যাবে
$stmt2 = $pdo->prepare("INSERT INTO order_details (order_id, detail) VALUES (?, ?)");
$stmt2->execute([$orderId, "Delivery address: Dhaka"]);
```

---

# অধ্যায় ১১: Pagination (পেজিনেশন)

অনেক data থাকলে সব একবারে না দেখিয়ে page ভাগ করে দেখানো হয়। এটিকে Pagination বলে।

```php
<?php
$page     = isset($_GET['page']) ? (int)$_GET['page'] : 1;  // কোন page?
$perPage  = 10;                                               // প্রতি page-এ কতটি?
$offset   = ($page - 1) * $perPage;                          // কতটি skip করবো?

// মোট কতটি record আছে?
$totalStmt = $pdo->query("SELECT COUNT(*) FROM students");
$total     = $totalStmt->fetchColumn();  // শুধু একটি মান নিয়ে আসে
$totalPages = ceil($total / $perPage);   // মোট পেজ সংখ্যা

// নির্দিষ্ট page-এর data
$stmt = $pdo->prepare("SELECT * FROM students LIMIT ? OFFSET ?");
$stmt->execute([$perPage, $offset]);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Display
foreach ($students as $student) {
    echo $student['name'] . "<br>";
}

// Page navigation
for ($i = 1; $i <= $totalPages; $i++) {
    echo "<a href='?page=$i'>$i</a> ";
}
?>
```

---

# অধ্যায় ১২: PDO দিয়ে একটি সম্পূর্ণ Real-World Project

ধরো আমরা একটি **Student Management System** বানাবো।

## ১২.১ Database Setup

```sql
CREATE DATABASE student_management;
USE student_management;

CREATE TABLE students (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    email      VARCHAR(150) UNIQUE NOT NULL,
    phone      VARCHAR(15),
    age        INT,
    department VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## ১২.২ Database Config (`config/db.php`)

```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'student_management');
define('DB_USER', 'root');
define('DB_PASS', '');

function getPDO(): PDO {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    return new PDO($dsn, DB_USER, DB_PASS, $options);
}
?>
```

## ১২.৩ Student Class (`classes/Student.php`)

```php
<?php
require_once 'config/db.php';

class StudentManager {
    private PDO $pdo;
    
    public function __construct() {
        $this->pdo = getPDO();
    }
    
    // সব student দেখাও
    public function getAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM students ORDER BY name");
        return $stmt->fetchAll();
    }
    
    // একজন student দেখাও
    public function getById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM students WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    // নতুন student যোগ করো
    public function create(array $data): int {
        $stmt = $this->pdo->prepare(
            "INSERT INTO students (name, email, phone, age, department)
             VALUES (:name, :email, :phone, :age, :department)"
        );
        $stmt->execute([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'phone'      => $data['phone'] ?? null,
            'age'        => $data['age']   ?? null,
            'department' => $data['department'] ?? null,
        ]);
        return (int) $this->pdo->lastInsertId();
    }
    
    // student আপডেট করো
    public function update(int $id, array $data): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE students SET name = :name, email = :email,
             phone = :phone, age = :age, department = :department
             WHERE id = :id"
        );
        $stmt->execute([
            'id'         => $id,
            'name'       => $data['name'],
            'email'      => $data['email'],
            'phone'      => $data['phone'] ?? null,
            'age'        => $data['age']   ?? null,
            'department' => $data['department'] ?? null,
        ]);
        return $stmt->rowCount() > 0;
    }
    
    // student মুছো
    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM students WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
    
    // নাম দিয়ে খোঁজো
    public function search(string $query): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM students WHERE name LIKE ? OR email LIKE ?"
        );
        $searchTerm = '%' . $query . '%';
        $stmt->execute([$searchTerm, $searchTerm]);
        return $stmt->fetchAll();
    }
    
    // department অনুযায়ী সংখ্যা গোনো
    public function countByDepartment(): array {
        $stmt = $this->pdo->query(
            "SELECT department, COUNT(*) as total 
             FROM students 
             GROUP BY department 
             ORDER BY total DESC"
        );
        return $stmt->fetchAll();
    }
}
?>
```

## ১২.৪ ব্যবহার (`index.php`)

```php
<?php
require_once 'classes/Student.php';

$manager = new StudentManager();

// নতুন student যোগ করো
$newId = $manager->create([
    'name'       => 'আরিফ হোসেন',
    'email'      => 'arif@example.com',
    'phone'      => '01712345678',
    'age'        => 21,
    'department' => 'CSE'
]);
echo "নতুন ID: $newId<br>";

// সব student দেখাও
$students = $manager->getAll();
foreach ($students as $s) {
    echo $s['name'] . " (" . $s['department'] . ")<br>";
}

// Search করো
$results = $manager->search('আরিফ');
echo "খোঁজা পেয়েছি: " . count($results) . "জন";

// আপডেট করো
$updated = $manager->update($newId, [
    'name'       => 'আরিফ হোসেন খান',
    'email'      => 'arif@example.com',
    'age'        => 22,
    'department' => 'EEE'
]);
echo $updated ? "আপডেট সফল" : "আপডেট ব্যর্থ";

// মুছে ফেলো
$deleted = $manager->delete($newId);
echo $deleted ? "মুছে ফেলা হয়েছে" : "মুছতে পারেনি";
?>
```

---

# অধ্যায় ১৩: PDO vs MySQLi — পার্থক্য

| বিষয় | PDO | MySQLi |
|---|---|---|
| **Database Support** | ১২+ database | শুধু MySQL |
| **Named Parameters** | ✅ আছে (`:name`) | ❌ নেই |
| **OOP Style** | ✅ পুরোপুরি OOP | ✅ OOP আছে |
| **Procedural Style** | ❌ নেই | ✅ আছে |
| **Prepared Statement** | ✅ চমৎকার | ✅ আছে |
| **Error Handling** | Exception | Error number |
| **Framework Support** | Laravel, Symfony ব্যবহার করে | কম |
| **Recommended For** | সব প্রজেক্ট | শুধু MySQL প্রজেক্ট |

**সিদ্ধান্ত:** নতুন প্রজেক্টে সবসময় **PDO** ব্যবহার করো।

---

# অধ্যায় ১৪: Best Practices এবং গুরুত্বপূর্ণ টিপস

## ১৪.১ সবসময় Prepared Statement ব্যবহার করো

```php
// ❌ কখনো এভাবে করো না
$pdo->query("SELECT * FROM users WHERE id = " . $_GET['id']);

// ✅ সবসময় এভাবে করো
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_GET['id']]);
```

## ১৪.২ Connection একবার তৈরি করো, বারবার ব্যবহার করো

```php
// ❌ ভুল — প্রতিটি function-এ নতুন connection
function getUser($id) {
    $pdo = new PDO(...);  // এটি বারবার connection খোলে
    // ...
}

// ✅ সঠিক — একটি connection পুরো script-এ ব্যবহার করো
$pdo = new PDO(...);
function getUser($id, $pdo) {
    // পাস করা $pdo ব্যবহার করো
}
```

## ১৪.৩ ATTR_EMULATE_PREPARES বন্ধ করো

```php
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
```
এটি সত্যিকারের prepared statement ব্যবহার করে, নকল নয়। এটি বন্ধ রাখলে আরও নিরাপদ।

## ১৪.৪ Sensitive তথ্য .env file-এ রাখো

```php
// ❌ সরাসরি code-এ password লিখো না
$pdo = new PDO("mysql:...", "root", "my_secret_password");

// ✅ .env file বা config file ব্যবহার করো
$password = getenv('DB_PASSWORD');
$pdo = new PDO("mysql:...", "root", $password);
```

## ১৪.৫ Default Fetch Mode সেট করো

```php
// Connection তৈরির সময়ই সেট করো
$options = [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // এখন প্রতিটি fetch()-এ আলাদা করে বলতে হবে না
];
```

---

# অধ্যায় ১৫: সম্পূর্ণ Quick Reference

## Connection Template

```php
<?php
$pdo = new PDO(
    "mysql:host=localhost;dbname=DBNAME;charset=utf8mb4",
    "USERNAME",
    "PASSWORD",
    [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]
);
?>
```

## CRUD One-Liners

```php
// Insert
$pdo->prepare("INSERT INTO t (a,b) VALUES (?,?)")->execute([$a, $b]);

// Insert এবং ID পাও
$pdo->prepare("INSERT INTO t (a) VALUES (?)")->execute([$a]);
$id = $pdo->lastInsertId();

// Select all
$rows = $pdo->query("SELECT * FROM t")->fetchAll();

// Select with condition
$stmt = $pdo->prepare("SELECT * FROM t WHERE id=?");
$stmt->execute([$id]);
$row = $stmt->fetch();

// Update
$pdo->prepare("UPDATE t SET a=? WHERE id=?")->execute([$a, $id]);

// Delete
$pdo->prepare("DELETE FROM t WHERE id=?")->execute([$id]);

// Count
$count = $pdo->query("SELECT COUNT(*) FROM t")->fetchColumn();
```

---

## সারসংক্ষেপ

| বিষয় | মনে রাখার কথা |
|---|---|
| PDO কী | PHP-এর database access layer |
| কেন ব্যবহার | Security, multi-DB support, clean code |
| Prepared Statement | SQL Injection রোধ করে, সবসময় ব্যবহার করো |
| `?` vs `:name` | ২-৩ column → `?`, বেশি column → `:name` |
| Error Mode | সবসময় `ERRMODE_EXCEPTION` ব্যবহার করো |
| Transaction | হয় সব, নয়তো কিছুই না |
| Fetch Mode | সাধারণত `FETCH_ASSOC` সবচেয়ে ভালো |

---

> ✅ **মূল কথা:** PDO শেখা মানে শুধু code লেখা না — এটি **নিরাপদ, professional, এবং scalable** web application তৈরির ভিত্তি। Laravel সহ বেশিরভাগ modern PHP framework-এর ভেতরে PDO কাজ করে।


---

## ⚙️ Common PDO Methods

| Method | Purpose |
|---|---|
| `prepare()` | SQL query কে prepare করে |
| `execute()` | Prepared query run করে |
| `query()` | সরাসরি query run করে |
| `fetch()` | একটি single row return করে |
| `fetchAll()` | সব rows একসাথে return করে |
| `lastInsertId()` | সর্বশেষ inserted row এর ID দেয় |
| `rowCount()` | কতটি row affected হয়েছে জানায় |

---