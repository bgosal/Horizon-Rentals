<?php

session_start();
require_once '../model/registration_login.php';

if (isset($_POST['action'], $_SESSION['email'],$_SESSION['clientID'])) {
    $action = $_POST['action'];
    $clientID = $_SESSION['clientID'];
    $email = $_SESSION['email'];
    
    
    if ($action == 'update_first_name') {
        $first_name = $_POST['first_name'];
        
        if (!empty ($first_name)){
    $query = 'UPDATE users SET first_name = :first_name WHERE clientID = :clientID';
                $statement = $db->prepare($query);
                $statement->bindValue(':first_name', $first_name);
                $statement->bindValue(':clientID', $clientID);
                
                try{
                $statement->execute();
                
                
                header('Location: account_management.php');
                $_SESSION['message'] = "First name updated";
                
                exit();
                
                }catch (PDOException $e) {
                     header('Location: account_management.php');
                $_SESSION['message'] = "Error updating first name: " . $e->getMessage();
                
            }
      
}

        else{
           
                header('Location: account_management.php');
                $_SESSION['message'] = "First name cannot be empty.";

        exit();
        }

    }
    
    
    
    else if ($action == 'update_last_name') {
         $last_name = $_POST['last_name'];
        if (!empty ($last_name)){
           
            $query = 'UPDATE users SET last_name = :last_name WHERE clientID = :clientID';
                $statement = $db->prepare($query);
                $statement->bindValue(':last_name', $last_name);
                $statement->bindValue(':clientID', $clientID);
                
                try{
                $statement->execute();
                
                header('Location: account_management.php');
                $_SESSION['message'] = "Last name updated";
                exit();
                
                }catch (PDOException $e) {
                 header('Location: account_management.php');
                $_SESSION['message'] = "Error updating last name: " . $e->getMessage();
            }
      
}

        else{
           header('Location: account_management.php');
                $_SESSION['message'] = "Last name cannot be empty.";
        exit();


        }
        
    }
    
    
    
     else if ($action == 'update_email') {
        if (!empty ($email)){
           $email_new = $_POST["email"];
            $query = 'UPDATE users SET email = :email WHERE clientID = :clientID ';
                $statement = $db->prepare($query);
                $statement->bindValue(':clientID', $clientID);
                $statement->bindValue(':email', $email_new);
                
                try{
                $statement->execute();
                $_SESSION['email'] = $email_new; 
                
                header('Location: account_management.php');
                $_SESSION['message'] = "Email updated";
                exit();
                
                }catch (PDOException $e) {
                
                header('Location: account_management.php');
                $_SESSION['message'] = "Error updating email " . $e->getMessage();
            }
      
}

        else{
            header('Location: account_management.php');
                $_SESSION['message'] = "Email cannot be empty!" ;



        }
        
    }
    
    
    else if ($action == 'update_password') {
        
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        $current_password = $_POST['current_password'];


            $query = 'SELECT password FROM users WHERE email = :email ';
            $statement = $db->prepare($query);
            $statement->bindValue(':email', $email);
            $statement->execute();
            $user = $statement->fetch(PDO::FETCH_ASSOC);



        if ($new_password !== $confirm_password) {
            $_SESSION['message'] = "Passwords dont match";
            header('Location: account_management.php');
             exit();

        }


            else if(strlen($new_password) <8){
                $_SESSION['message'] = "Password must be at least 8 characters long";
                header('Location: account_management.php');
            exit();

            }

            else {
            if ($user && password_verify($current_password, $user['password'])){

                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $query = 'UPDATE users SET password = :password WHERE email = :email';
                        $statement = $db->prepare($query);
                        $statement->bindValue(':password', $hashed_password);
                        $statement->bindValue(':email', $email);
                        try {
                    $statement->execute();
                    $_SESSION['message'] = "Password updated successfully!";
                } catch (PDOException $e) {
                    $_SESSION['message'] = "Error updating password: " . $e->getMessage();
                }
                        header('Location: account_management.php');
                exit();



            }
            else{

              $_SESSION['message'] = "Current password is incorrect!";
                header('Location: account_management.php');
                exit();

            }
        }

     
        
    }
   
    
    
    
    } 
    
    
    
    


?>

   
    
    
