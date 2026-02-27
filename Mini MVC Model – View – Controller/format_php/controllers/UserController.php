<!-- in django views.py -->

<?php
require_once __DIR__ . '/../models/User.php';

class UserController
{
    public function home() {
        $userModel = new User(); // model instance
        $date = $userModel->getData(); // data fetch from model

        // template load

        extract($date); // array key → variable name    
        require __DIR__ . '/../views/home.php'; // template load
    }

    public function about() {
        echo "This is the About Page.";
    }
}   


?>


<!-- extract($date); -->
<!-- [
  'username' => 'AN Mamun',
  'users' => ['Ali', 'Rahim']
]
extract() এর পর:

$username = 'AN Mamun';
$users = ['Ali', 'Rahim']; -->