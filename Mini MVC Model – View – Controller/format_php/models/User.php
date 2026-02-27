<!-- in django models.py -->

<?php 
class User
{
    public function getData(){
        return [
            'username' => "An Mamun",
            'is_logged_in' => true,
            'users' => ['An Mamun', 'John Doe', 'Jane Doe']

        ];
    }
}

?>