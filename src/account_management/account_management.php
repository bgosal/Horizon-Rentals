<?php
session_start();

//echo $_SESSION['email'];
//echo $_SESSION['clientID'];
if (isset($_SESSION['message'])) {
                    echo "<script type='text/javascript'>alert('" . $_SESSION['message'] . "');</script>";
                        unset($_SESSION['message']);
                    }
                    
                    
                    

            
            require_once '../model/registration_login.php';
           
                    
                $query = 'SELECT first_name, last_name, email FROM users WHERE email = :email';
                $statement = $db->prepare($query);
                $statement->bindValue(':email', $_SESSION['email']);
                $statement->execute();
                
                $users = $statement->fetch(); 
                $statement->closeCursor();
                //var_dump($users);
                //var_dump($_SESSION['email']);
//                echo $users['first_name'];
                //echo $users['last_name'];
                //echo $users['email'];
                      

?>

<html>
    <head>
        <meta charset="UTF-8">
        <title> Account Management </title>
        <link href='https://fonts.googleapis.com/css?family=Abel' rel='stylesheet'>
         <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel ='stylesheet'>
        <link rel ='stylesheet' href ='../style.css'>
        
    </head>
    <body class = 'account_management'>
        <?php include '../view/nav.php'; ?>
        
        <div class ="container3">
            <h1>Account Management </h1>
            
            
            <div class ="manage">
            <form action="update_profile.php" method="post">
                 <input type="hidden" name="action" value="update_first_name">
                <label for="first_name"><strong>First Name:</strong></label>
                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($users['first_name']); ?>">
                <input type="submit" value="Update">
                
                
                
            </form>
            <form action="update_profile.php" method="post">
        <input type="hidden" name="action" value="update_last_name">
            <label for="last_name"><strong>Last Name:</strong></label>
                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($users['last_name']); ?>">
                <input type="submit" value="Update">
            
            </form>
            
            <form action="update_profile.php" method="post">
        <input type="hidden" name="action" value="update_email">
            <label for="email"><strong>Email:</strong></label>
                <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($users['email']); ?>">
                <input type="submit" value="Update">
            
            </form>
            
            <form action="update_profile.php" method="post">
                <input type="hidden" name="action" value="update_password">
                <label for="current_password"><strong>Current Password:</strong></label>
                <input type="password" id="current_password" name="current_password" required>
                
                
                <br>
            <label for="new_password"><strong>New Password:</strong></label>
                <input type="password" id="new_password" name="new_password" required>
                <br>
                <label for="confirm_password"><strong>Confirm Password:</strong></label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                
                <input type="submit" name="update" value="Update">
                
                
            
            </form>
                
                </div>
            
            <div class ="footer"> <?php include '../view/footer.php'; ?>     </div>
            
        </div>
        
        
        
        
        
             
             
   
        
        
        
        
       
    </body>
     
</html>


