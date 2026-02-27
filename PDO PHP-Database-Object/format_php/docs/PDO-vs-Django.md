# 🐘 PDO vs 🐍 Django ORM — সম্পূর্ণ তুলনামূলক গাইড

> তুমি Django developer, তাই প্রতিটি PDO concept-কে Django-র সাথে তুলনা করে বোঝানো হয়েছে।

---

# প্রথমেই বড় ছবি দেখি

```
Django ORM  →  SQL তৈরি করে দেয় (তুমি SQL লেখো না)
PDO         →  SQL তুমি নিজে লেখো, PDO শুধু নিরাপদে পাঠায়
```

Django-তে তুমি `Student.objects.all()` লিখলে Django নিজেই SQL বানায়।
PHP-তে PDO দিয়ে তোমাকে নিজেই `SELECT * FROM students` লিখতে হয়, PDO শুধু সেটা নিরাপদে চালায়।

---

# অধ্যায় ১: Setup এবং Connection

## Django-তে যেভাবে করো

`settings.py`-তে একবার লিখলেই হয়, Django বাকি সব handle করে:

```python
# settings.py
DATABASES = {
    'default': {
        'ENGINE': 'django.db.backends.mysql',
        'NAME': 'school_db',
        'USER': 'root',
        'PASSWORD': '',
        'HOST': 'localhost',
        'PORT': '3306',
    }
}
```

তারপর কোথাও connection নিয়ে ভাবতে হয় না। Django নিজেই connection pool manage করে।

## PHP PDO-তে যেভাবে করো

প্রতিটি script-এ (বা একটি shared config file-এ) connection তৈরি করতে হয়:

```php
<?php
// config/db.php
function getPDO(): PDO {
    $dsn = "mysql:host=localhost;dbname=school_db;charset=utf8mb4";
    
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    return new PDO($dsn, "root", "", $options);
}
?>
```

## পার্থক্য

| বিষয় | Django | PDO |
|---|---|---|
| **Config কোথায়?** | `settings.py` | একটি PHP file |
| **Connection তৈরি** | Django করে দেয় | তুমি করো |
| **Connection Pool** | Django manage করে | তুমি manage করো |
| **Multiple DB** | settings-এ লিখলেই হয় | আলাদা `$pdo` object |

---

# অধ্যায় ২: Model vs Table

## Django-তে Model

Django-তে একটি Python class লিখলে Django সেই class থেকে নিজেই database table তৈরি করে নেয়।

```python
# models.py
from django.db import models

class Student(models.Model):
    name       = models.CharField(max_length=100)
    email      = models.EmailField(unique=True)
    phone      = models.CharField(max_length=15, null=True, blank=True)
    age        = models.IntegerField(null=True)
    department = models.CharField(max_length=50, null=True)
    created_at = models.DateTimeField(auto_now_add=True)

    class Meta:
        db_table = 'students'  # table-এর নাম
```

তারপর:
```bash
python manage.py makemigrations
python manage.py migrate
```
এই দুটো command দিলেই database-এ `students` table তৈরি হয়ে যায়।

## PDO-তে Table

PHP/PDO-তে model concept নেই। তোমাকে নিজেই SQL দিয়ে table তৈরি করতে হয়:

```sql
-- এই SQL নিজে run করতে হবে (phpMyAdmin বা terminal থেকে)
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

PHP-তে কোনো `makemigrations` নেই। Schema পরিবর্তন করতে হলে নিজে `ALTER TABLE` লিখতে হবে।

## পার্থক্য

| বিষয় | Django | PDO |
|---|---|---|
| **Table তৈরি** | Python class → migrate | নিজে SQL লেখো |
| **Schema change** | `makemigrations` | `ALTER TABLE` manually |
| **Migration history** | ✅ আছে | ❌ নেই (manually রাখতে হবে) |
| **Validation** | Model-এই লেখা যায় | নিজে PHP-তে লিখতে হবে |

---

# অধ্যায় ৩: CREATE — নতুন data ঢোকানো

## Django

```python
# পদ্ধতি ১: create()
student = Student.objects.create(
    name="সায়েম",
    email="sayem@example.com",
    age=21,
    department="CSE"
)
print(student.id)  # auto-generated ID

# পদ্ধতি ২: save()
student = Student(name="সায়েম", email="sayem@example.com")
student.age = 21
student.save()
print(student.id)
```

Django নিজেই SQL বানায়:
```sql
INSERT INTO students (name, email, age, department) VALUES ('সায়েম', 'sayem@example.com', 21, 'CSE')
```

## PDO

```php
<?php
$stmt = $pdo->prepare(
    "INSERT INTO students (name, email, age, department)
     VALUES (:name, :email, :age, :department)"
);

$stmt->execute([
    'name'       => 'সায়েম',
    'email'      => 'sayem@example.com',
    'age'        => 21,
    'department' => 'CSE'
]);

$newId = $pdo->lastInsertId();  // Django-র student.id এর মতো
echo $newId;
?>
```

## পার্থক্য

| বিষয় | Django | PDO |
|---|---|---|
| **SQL লেখা** | লাগে না | লিখতে হয় |
| **ID পাওয়া** | `student.id` | `$pdo->lastInsertId()` |
| **Object ফিরে আসে?** | ✅ হ্যাঁ | ❌ না (শুধু ID) |
| **Validation** | Model field-এই | নিজে করতে হবে |

---

# অধ্যায় ৪: READ — data পড়া

## ৪.১ সব record পড়া

### Django
```python
students = Student.objects.all()
# বা specific fields:
students = Student.objects.all().values('name', 'email')

for student in students:
    print(student.name, student.email)
```

### PDO
```php
$stmt = $pdo->query("SELECT * FROM students");
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
// বা specific fields:
// $stmt = $pdo->query("SELECT name, email FROM students");

foreach ($students as $student) {
    echo $student['name'] . " " . $student['email'];
}
```

---

## ৪.২ একটি নির্দিষ্ট record

### Django
```python
# ID দিয়ে
student = Student.objects.get(id=5)
# না পেলে DoesNotExist exception

# না পেলে None
student = Student.objects.filter(id=5).first()
```

### PDO
```php
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([5]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);
// না পেলে false
if ($student) { ... }
```

---

## ৪.৩ Filter করা (WHERE)

### Django
```python
# Single condition
students = Student.objects.filter(department="CSE")

# Multiple conditions (AND)
students = Student.objects.filter(department="CSE", age=21)

# OR condition
from django.db.models import Q
students = Student.objects.filter(Q(department="CSE") | Q(department="EEE"))

# LIKE (contains)
students = Student.objects.filter(name__contains="সায়েম")
students = Student.objects.filter(name__icontains="sayem")  # case-insensitive

# Greater than
students = Student.objects.filter(age__gt=20)
students = Student.objects.filter(age__gte=20)  # >= 20

# Range
students = Student.objects.filter(age__range=(18, 25))
```

### PDO
```php
// Single condition
$stmt = $pdo->prepare("SELECT * FROM students WHERE department = ?");
$stmt->execute(["CSE"]);

// Multiple conditions (AND)
$stmt = $pdo->prepare("SELECT * FROM students WHERE department = ? AND age = ?");
$stmt->execute(["CSE", 21]);

// OR condition
$stmt = $pdo->prepare("SELECT * FROM students WHERE department = ? OR department = ?");
$stmt->execute(["CSE", "EEE"]);

// LIKE
$stmt = $pdo->prepare("SELECT * FROM students WHERE name LIKE ?");
$stmt->execute(["%সায়েম%"]);

// Greater than
$stmt = $pdo->prepare("SELECT * FROM students WHERE age > ?");
$stmt->execute([20]);

// Range
$stmt = $pdo->prepare("SELECT * FROM students WHERE age BETWEEN ? AND ?");
$stmt->execute([18, 25]);
```

---

## ৪.৪ Ordering (ORDER BY)

### Django
```python
# Ascending
students = Student.objects.order_by('name')

# Descending
students = Student.objects.order_by('-name')  # - মানে DESC

# Multiple
students = Student.objects.order_by('department', '-age')
```

### PDO
```php
// Ascending
$stmt = $pdo->query("SELECT * FROM students ORDER BY name ASC");

// Descending
$stmt = $pdo->query("SELECT * FROM students ORDER BY name DESC");

// Multiple
$stmt = $pdo->query("SELECT * FROM students ORDER BY department ASC, age DESC");
```

---

## ৪.৫ Limit এবং Offset

### Django
```python
# প্রথম ১০টি (LIMIT 10)
students = Student.objects.all()[:10]

# ১০ skip করে ১০টি (OFFSET 10, LIMIT 10)
students = Student.objects.all()[10:20]
```

### PDO
```php
// প্রথম ১০টি
$stmt = $pdo->query("SELECT * FROM students LIMIT 10");

// ১০ skip করে ১০টি
$stmt = $pdo->prepare("SELECT * FROM students LIMIT ? OFFSET ?");
$stmt->execute([10, 10]);
```

---

## ৪.৬ Count করা

### Django
```python
total = Student.objects.count()
cse_total = Student.objects.filter(department="CSE").count()
```

### PDO
```php
$total = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE department = ?");
$stmt->execute(["CSE"]);
$cse_total = $stmt->fetchColumn();
// fetchColumn() — শুধু একটি মান নিয়ে আসে
```

---

# অধ্যায় ৫: UPDATE — data পরিবর্তন

## Django
```python
# পদ্ধতি ১: object fetch → save
student = Student.objects.get(id=5)
student.name = "নতুন নাম"
student.age  = 23
student.save()

# পদ্ধতি ২: bulk update (SQL efficient)
Student.objects.filter(department="CSE").update(department="Computer Science")
```

## PDO
```php
// Single record update
$stmt = $pdo->prepare("UPDATE students SET name = ?, age = ? WHERE id = ?");
$stmt->execute(["নতুন নাম", 23, 5]);

// Bulk update
$stmt = $pdo->prepare("UPDATE students SET department = ? WHERE department = ?");
$stmt->execute(["Computer Science", "CSE"]);

echo $stmt->rowCount() . " টি row পরিবর্তন হয়েছে";
```

## পার্থক্য

| বিষয় | Django | PDO |
|---|---|---|
| **Partial update** | `.save()` — changed fields only | পুরো query নিজে লেখো |
| **Bulk update** | `.update()` — এক query-তে | `UPDATE ... WHERE` লেখো |
| **Affected rows** | `queryset.update()` returns count | `$stmt->rowCount()` |

---

# অধ্যায় ৬: DELETE — data মুছা

## Django
```python
# Single record
student = Student.objects.get(id=5)
student.delete()

# Bulk delete
Student.objects.filter(department="Deleted").delete()

# সব মুছো
Student.objects.all().delete()
```

## PDO
```php
// Single record
$stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
$stmt->execute([5]);

// Bulk delete
$stmt = $pdo->prepare("DELETE FROM students WHERE department = ?");
$stmt->execute(["Deleted"]);

// সব মুছো
$pdo->exec("DELETE FROM students");  // বা TRUNCATE TABLE students

echo $stmt->rowCount() . " টি row মুছা হয়েছে";
```

---

# অধ্যায় ৭: Aggregation (GROUP BY, SUM, AVG)

## Django
```python
from django.db.models import Count, Avg, Sum, Max, Min

# Department অনুযায়ী count
result = Student.objects.values('department').annotate(total=Count('id'))
# [{'department': 'CSE', 'total': 45}, ...]

# Average age
avg_age = Student.objects.aggregate(avg=Avg('age'))
# {'avg': 21.5}

# Department wise average age
result = Student.objects.values('department').annotate(avg_age=Avg('age'))
```

## PDO
```php
// Department অনুযায়ী count
$stmt = $pdo->query(
    "SELECT department, COUNT(*) as total 
     FROM students 
     GROUP BY department"
);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
// [['department' => 'CSE', 'total' => 45], ...]

// Average age
$avg = $pdo->query("SELECT AVG(age) FROM students")->fetchColumn();
// 21.5

// Department wise average age
$stmt = $pdo->query(
    "SELECT department, AVG(age) as avg_age 
     FROM students 
     GROUP BY department"
);
```

---

# অধ্যায় ৮: JOIN — সম্পর্কিত table

Django-তে ForeignKey থাকলে automatically JOIN হয়। PDO-তে নিজে SQL JOIN লিখতে হয়।

**ধরো দুটি table আছে:**
- `students` — student-এর তথ্য
- `courses` — course-এর তথ্য (student_id foreign key সহ)

## Django
```python
# models.py
class Course(models.Model):
    student    = models.ForeignKey(Student, on_delete=models.CASCADE, related_name='courses')
    title      = models.CharField(max_length=100)
    instructor = models.CharField(max_length=100)

# view.py — Django automatically JOIN করে
student = Student.objects.prefetch_related('courses').get(id=5)
for course in student.courses.all():
    print(course.title)

# বা reverse: কোন student কোন course করছে
courses = Course.objects.select_related('student').filter(student__department="CSE")
for course in courses:
    print(course.student.name, course.title)
```

## PDO
```php
// INNER JOIN
$stmt = $pdo->prepare(
    "SELECT s.name, s.department, c.title, c.instructor
     FROM students s
     INNER JOIN courses c ON s.id = c.student_id
     WHERE s.id = ?"
);
$stmt->execute([5]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($results as $row) {
    echo $row['name'] . " — " . $row['title'];
}

// LEFT JOIN (student থাকলেও course না থাকলেও দেখাবে)
$stmt = $pdo->query(
    "SELECT s.name, c.title
     FROM students s
     LEFT JOIN courses c ON s.id = c.student_id"
);
```

## পার্থক্য

| বিষয় | Django | PDO |
|---|---|---|
| **JOIN লেখা** | ForeignKey → automatic | নিজে SQL লিখতে হয় |
| **Related data** | `select_related()`, `prefetch_related()` | JOIN query |
| **N+1 Problem** | `prefetch_related()` দিয়ে এড়ানো যায় | JOIN দিয়ে এড়াতে হবে |

---

# অধ্যায় ৯: Raw SQL — Django vs PDO

কখনো কখনো Django ORM-এ জটিল query লেখা কঠিন হয়, তখন raw SQL ব্যবহার করতে হয়। PDO সবসময়ই raw SQL।

## Django-তে Raw SQL

```python
from django.db import connection

# পদ্ধতি ১: Model.objects.raw()
students = Student.objects.raw("SELECT * FROM students WHERE age > %s", [20])
for s in students:
    print(s.name)

# পদ্ধতি ২: cursor (PDO-র মতোই)
with connection.cursor() as cursor:
    cursor.execute("SELECT department, COUNT(*) FROM students GROUP BY department")
    rows = cursor.fetchall()
    for row in rows:
        print(row[0], row[1])  # Tuple format
```

## PDO-তে Raw SQL

```php
// PDO সবসময়ই raw SQL
$stmt = $pdo->prepare("SELECT * FROM students WHERE age > ?");
$stmt->execute([20]);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($students as $s) {
    echo $s['name'];
}

// Complex query
$stmt = $pdo->query(
    "SELECT department, COUNT(*) as total
     FROM students
     GROUP BY department
     HAVING total > 10
     ORDER BY total DESC"
);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
```

**Django developer-দের জন্য গুরুত্বপূর্ণ:**
PDO তে কখনো `%s` ব্যবহার করো না (Django cursor-এর মতো), PDO-তে `?` বা `:name` ব্যবহার করো।

```php
// ❌ PHP-তে এটি কাজ করবে না (Django style)
$stmt->execute("SELECT * WHERE age > %s", [20]);

// ✅ PHP PDO style
$stmt = $pdo->prepare("SELECT * WHERE age > ?");
$stmt->execute([20]);
```

---

# অধ্যায় ১০: Transaction — Django vs PDO

## Django
```python
from django.db import transaction

# পদ্ধতি ১: decorator
@transaction.atomic
def transfer_money(from_id, to_id, amount):
    Account.objects.filter(id=from_id).update(balance=F('balance') - amount)
    Account.objects.filter(id=to_id).update(balance=F('balance') + amount)
    # কোনো error হলে automatically rollback হবে

# পদ্ধতি ২: context manager
def transfer_money(from_id, to_id, amount):
    with transaction.atomic():
        Account.objects.filter(id=from_id).update(balance=F('balance') - amount)
        Account.objects.filter(id=to_id).update(balance=F('balance') + amount)
```

## PDO
```php
function transferMoney(PDO $pdo, int $fromId, int $toId, float $amount): void {
    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("UPDATE accounts SET balance = balance - ? WHERE id = ?");
        $stmt->execute([$amount, $fromId]);
        
        $stmt = $pdo->prepare("UPDATE accounts SET balance = balance + ? WHERE id = ?");
        $stmt->execute([$amount, $toId]);
        
        $pdo->commit();
        echo "Transfer সফল!";
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Transfer ব্যর্থ: " . $e->getMessage();
    }
}
```

## তুলনা

| বিষয় | Django | PDO |
|---|---|---|
| **Syntax** | `@atomic` decorator বা `with atomic()` | `beginTransaction()` → `commit()` / `rollBack()` |
| **Auto rollback** | ✅ Exception হলে auto | ❌ নিজে `rollBack()` call করতে হবে |
| **Nested transaction** | ✅ Savepoint support | ⚠️ সীমিত |

---

# অধ্যায় ১১: Error Handling — Django vs PDO

## Django
```python
from django.db import IntegrityError, DatabaseError

try:
    Student.objects.create(email="duplicate@example.com")  # Unique constraint error
except IntegrityError as e:
    print("Duplicate entry:", e)
except DatabaseError as e:
    print("DB error:", e)
```

## PDO
```php
try {
    $stmt = $pdo->prepare("INSERT INTO students (email) VALUES (?)");
    $stmt->execute(["duplicate@example.com"]);  // Unique constraint error
    
} catch (PDOException $e) {
    // Error code check
    if ($e->getCode() == 23000) {
        echo "এই email আগেই ব্যবহার হয়েছে!";
    } else {
        echo "Database error: " . $e->getMessage();
    }
}
```

**Common PDO Error Codes:**

| Code | কারণ | Django Equivalent |
|---|---|---|
| `23000` | Duplicate entry (UNIQUE constraint) | `IntegrityError` |
| `42S02` | Table not found | `ProgrammingError` |
| `42000` | Syntax error | `ProgrammingError` |
| `HY000` | General error | `DatabaseError` |

---

# অধ্যায় ১২: Fetch Modes — Django QuerySet-এর মতো

Django-তে QuerySet বিভিন্নভাবে data return করে। PDO-তেও তেমনি Fetch Mode আছে।

| Django | PDO Equivalent | ফলাফল |
|---|---|---|
| `Student.objects.all()` | `fetchAll(FETCH_OBJ)` | Object list |
| `.values()` | `fetchAll(FETCH_ASSOC)` | Dict/Array list |
| `.values_list()` | `fetchAll(FETCH_NUM)` | Tuple/Indexed array |
| `.first()` | `fetch(FETCH_ASSOC)` | একটি row |
| `.count()` | `fetchColumn()` | একটি value |

```php
// Django: Student.objects.all() → Object list
$students = $stmt->fetchAll(PDO::FETCH_OBJ);
echo $students[0]->name;  // Django-র মতো dot notation

// Django: .values() → Dict list
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo $students[0]['name'];  // Array notation

// Django: .first() → Single object
$student = $stmt->fetch(PDO::FETCH_ASSOC);

// Django: .count() → Single value
$count = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
```

---

# অধ্যায় ১৩: সম্পূর্ণ Side-by-Side তুলনা

## একটি Student API তৈরি করা

### Django (views.py)

```python
from django.http import JsonResponse
from .models import Student

def student_list(request):
    if request.method == 'GET':
        students = Student.objects.filter(
            department=request.GET.get('dept', 'CSE')
        ).order_by('name').values('id', 'name', 'email', 'age')
        
        return JsonResponse(list(students), safe=False)

def student_detail(request, pk):
    try:
        student = Student.objects.get(pk=pk)
        return JsonResponse({
            'id': student.id,
            'name': student.name,
            'email': student.email
        })
    except Student.DoesNotExist:
        return JsonResponse({'error': 'Not found'}, status=404)

def create_student(request):
    if request.method == 'POST':
        import json
        data = json.loads(request.body)
        
        student = Student.objects.create(
            name=data['name'],
            email=data['email'],
            age=data.get('age'),
            department=data.get('department', 'CSE')
        )
        return JsonResponse({'id': student.id, 'message': 'Created'}, status=201)
```

### PHP PDO (api.php)

```php
<?php
require_once 'config/db.php';
header('Content-Type: application/json');

$pdo    = getPDO();
$method = $_SERVER['REQUEST_METHOD'];
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// GET /students
if ($method === 'GET' && $uri === '/students') {
    $dept = $_GET['dept'] ?? 'CSE';
    
    $stmt = $pdo->prepare(
        "SELECT id, name, email, age FROM students 
         WHERE department = ? ORDER BY name"
    );
    $stmt->execute([$dept]);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($students);
}

// GET /students/5
elseif ($method === 'GET' && preg_match('/\/students\/(\d+)/', $uri, $matches)) {
    $id = $matches[1];
    
    $stmt = $pdo->prepare("SELECT id, name, email FROM students WHERE id = ?");
    $stmt->execute([$id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($student) {
        echo json_encode($student);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Not found']);
    }
}

// POST /students
elseif ($method === 'POST' && $uri === '/students') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    try {
        $stmt = $pdo->prepare(
            "INSERT INTO students (name, email, age, department) 
             VALUES (:name, :email, :age, :department)"
        );
        $stmt->execute([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'age'        => $data['age']        ?? null,
            'department' => $data['department'] ?? 'CSE'
        ]);
        
        http_response_code(201);
        echo json_encode(['id' => $pdo->lastInsertId(), 'message' => 'Created']);
        
    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?>
```

**লক্ষ্য করো:** Django-তে routing, request handling সব framework দেয়। PHP-তে সব নিজে করতে হয়, যদি না Laravel/Slim ব্যবহার করো।

---

# অধ্যায় ১৪: Django Concepts এর PHP/PDO বিকল্প

| Django Feature | PHP/PDO-তে কীভাবে করবে |
|---|---|
| `Model` class | নিজে PHP class লিখতে হবে বা Laravel Eloquent |
| `makemigrations` | নিজে SQL file রাখতে হবে |
| `objects.filter()` | নিজে `WHERE` clause লিখতে হবে |
| `__contains`, `__gt` | `LIKE`, `>` SQL operators |
| `ForeignKey` | নিজে `FOREIGN KEY` ও `JOIN` লিখতে হবে |
| `@transaction.atomic` | `beginTransaction()` / `commit()` / `rollBack()` |
| `DoesNotExist` | `if ($row === false)` check |
| `IntegrityError` | `catch (PDOException $e)` + error code check |
| `annotate(Count())` | `SELECT COUNT(*) ... GROUP BY` |
| `select_related()` | `JOIN` query |
| `prefetch_related()` | সেপারেট query বা `JOIN` |
| `Q()` objects | `OR` in SQL |
| `.values()` | `FETCH_ASSOC` |
| `.values_list()` | `FETCH_NUM` |
| `paginator` | `LIMIT` এবং `OFFSET` নিজে handle |

---

# অধ্যায় ১৫: Laravel — Django-র সবচেয়ে কাছের PHP বিকল্প

> তুমি Django developer, তাই জেনে রাখো — PHP-তে **Laravel** হলো Django-র সবচেয়ে কাছের equivalent।

## Laravel Eloquent vs Django ORM

```php
// Laravel Eloquent (Django ORM-এর মতোই!)

// Django: Student.objects.all()
$students = Student::all();

// Django: Student.objects.filter(department="CSE")
$students = Student::where('department', 'CSE')->get();

// Django: Student.objects.get(id=5)
$student = Student::find(5);
$student = Student::findOrFail(5);  // না পেলে 404

// Django: Student.objects.create(name="সায়েম", email="...")
$student = Student::create(['name' => 'সায়েম', 'email' => '...']);

// Django: student.save()
$student->name = 'নতুন নাম';
$student->save();

// Django: student.delete()
$student->delete();

// Django: .order_by('-name')
$students = Student::orderBy('name', 'desc')->get();

// Django: .filter(age__gt=20)
$students = Student::where('age', '>', 20)->get();

// Django: .count()
$count = Student::count();

// Django: paginator
$students = Student::paginate(10);
```

**Laravel Eloquent প্রায় Django ORM-এর মতোই।** তুমি যদি PHP-তে Django-র মতো experience চাও, Laravel ব্যবহার করো।

তবে Laravel-এর ভেতরেও **PDO** কাজ করে। তাই PDO শেখা মানে Laravel-ও বুঝতে পারবে।

---

# চূড়ান্ত সারসংক্ষেপ: Django Developer হিসেবে PDO শেখার মূল পার্থক্য

| Django | PDO (Raw PHP) |
|---|---|
| ORM — SQL লিখতে হয় না | Raw SQL — সব নিজে লিখতে হয় |
| Model class → table auto তৈরি | নিজে SQL দিয়ে table তৈরি |
| `.filter()`, `.get()`, `.create()` | `prepare()` + SQL |
| `@atomic` transaction | `beginTransaction()` manually |
| `DoesNotExist` exception | `if ($row === false)` |
| `IntegrityError` | `PDOException` + error code |
| Migrations আছে | নেই — নিজে manage করতে হয় |
| Auto JOIN (ForeignKey) | নিজে JOIN query লিখতে হয় |
| Framework করে দেয় অনেক কিছু | সব manually |

## কোনটি কখন?

- **Django** — rapid development, built-in features, Admin panel দরকার হলে
- **PDO (Raw PHP)** — legacy project, full SQL control দরকার, হালকা script
- **Laravel (PHP + Eloquent)** — Django-র মতো PHP experience চাইলে

---

> ✅ **Django developer হিসেবে PDO বোঝার সহজ উপায়:** PDO হলো Django-র `connection.cursor()` এর মতো — তুমি raw SQL লেখো, PDO সেটা নিরাপদে চালায়। Django ORM যা automatically করে দেয়, PDO-তে সেটা তোমাকে SQL দিয়ে নিজে করতে হবে।