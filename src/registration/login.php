<?php

$returnURL = isset($_GET['return']) ? htmlspecialchars($_GET['return']) : '';
?>



<html>
    <head>
        <meta charset="UTF-8">
        <title> Login </title>
         <link href='https://fonts.googleapis.com/css?family=Abel' rel='stylesheet'>
         <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel ='stylesheet'>
        <link rel ='stylesheet' href ='../style.css'>
    </head>
    <body class ='login_page'>
        <?php include '../view/nav.php'; ?>
        <div class ="container">
            <h1>Login</h1>
            <?php
            
            
            if(isset($_POST["Login"])){
                $email = filter_input(INPUT_POST, 'email');
                $user_psswd = filter_input(INPUT_POST,'psswd');
                
                
                
                
                require_once '../model/registration_login.php';
                $query = 'SELECT * FROM users WHERE email = :email';
                $statement = $db->prepare($query);
                $statement->bindValue(':email', $email);
               
                
                
                
                $statement->execute();
                $user = $statement->fetch(PDO::FETCH_ASSOC);  
                $statement->closecursor();
                if ($user) {
                  
                    
                    if(password_verify($user_psswd, $user['password'])){
                    session_start();
                    
                    
                    $_SESSION["user"] = "yes";
                    $_SESSION["email"] = $user['email'];
                     $_SESSION['clientID'] = $user['clientID'];
                     $_SESSION['test'] = 'testing';
//                    var_dump($_SESSION['email'], $_SESSION['clientID']);
                    
                    
                    if(isset($_POST['remember_me'])){
           
                        setcookie('user_email', $email, time() + (86400 * 30), "/");
                    }
                    
                    if (!empty($returnURL)) {
                    header("Location: " . urldecode($_POST['return']));
                     } else {
                        header("Location: ../index.php");
                    }
                    exit();
                    }
                    
                    else {
                            echo "Incorrect password!";
                            }
                            
                }

                
                else{
                   echo "Email not found!";
                    
                }
            }
            
            
            
            
            
            ?>
            
            
            <form action="login.php" method ="post">
                
                
                <input type="hidden" name="return" value="<?php echo $returnURL; ?>">
                <div class ="form-group">
                    
                    <input type="email" name="email" placeholder="Email:" value="<?php echo isset($_COOKIE['user_email']) ? htmlspecialchars($_COOKIE['user_email']) : (isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''); ?>">

                    <i class='bx bxs-envelope'></i>
                </div>
            
        
                <div class ="form-group">
                    <input type="password" name="psswd" placeholder="Password">
                    <i class='bx bxs-lock-alt'></i>
                </div>
                
                <div class = "remember_button">
                    <label><input type="checkbox" name="remember_me"> Remember me</label>
                        <a href ="#" style="color: white; text-decoration: underline;"> Forgot password</a>
                    
                   
                    
                </div>
                
                
                <div class = "login_button">
                    <input type ="submit" value ="Login" name = "Login">
                    
                </div>
                
                <div class ="register_new">
                    <p>Don't have an account?<a href='registration.php' style="color: white; text-decoration: underline;"> Register Now </a></p>
                            
                </div>
                
            </form>
            
            
            
            
            
        </div>
        
        
    </body>
</html>

