


<html>
    <head>
        <meta charset="UTF-8">
        <title> Registration </title>
        <link href='https://fonts.googleapis.com/css?family=Abel' rel='stylesheet'>
         <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel ='stylesheet'>
        <link rel ='stylesheet' href ='../style.css'>
    </head>
    <body class = "registration_page">
       <?php include '../view/nav.php'; ?>
        <div class ="container1">
             <h1> Account Registration </h1>
            <?php
            if (isset($_POST["submit"])){
                $first_name = filter_input(INPUT_POST, "first_name", FILTER_SANITIZE_STRING);
                $last_name = filter_input(INPUT_POST, "last_name", FILTER_SANITIZE_STRING);
                $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
                $password = $_POST["password"];
                $confirm_password = $_POST["confirm_password"];
                $errors = array();
                
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                if(empty($first_name) || empty($last_name) || empty($email)|| empty($password)|| empty($confirm_password)){
                    array_push($errors, "All fields are required");
                    
                    
                }
                
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
                        array_push($errors, "Invalid Email");
                        
                }
                
                if (strlen($password)< 8){
                    array_push($errors, "Password must be at least 8 characters long");
                    
                }
                
                if($password !== $confirm_password){
                    array_push($errors, "Passwords don't match");
                    
                }
                        

                require_once '../model/registration_login.php';
                $query = 'Select * FROM users WHERE email = :email';
                $statement = $db->prepare($query);
                $statement->bindValue(':email', $email);
                $statement->execute();

                $users = $statement->fetch(PDO::FETCH_ASSOC);        
                if ($users) {
                    array_push($errors, "Email already registered"); 
                }
                
                if (count($errors)>0){
                    foreach ($errors as $error){
                        echo "<p style='color: red; text-align: center;'>" . htmlspecialchars($error) . "</p>"; 
                    }
                }
                
                
                
                else{
                    ini_set('display_errors', 1);
                    ini_set("display_startup_errors","1");
                       error_reporting(E_ALL);

                    
                    

                    
                    
                    $query = 'INSERT INTO users 
                            (first_name, last_name, email, password) 
                            VALUES (:first_name, :last_name, :email, :password)';
                    
                    $statement = $db-> prepare($query);
                    $statement-> bindValue(':first_name', $first_name);
                    $statement-> bindValue(':last_name', $last_name);   
                    $statement-> bindValue(':email', $email);
                    $statement-> bindValue(':password', $hashed_password);
                    
                    
                    try {
                        $statement->execute();
                        $statement->closeCursor();
                        session_start(); 
                       $_SESSION['clientID']= $db->lastInsertId(); 
                        $_SESSION['user'] = $first_name; 
                        $_SESSION['email'] = $email; 
                        //$_SESSION['clientID'] = $clientID;
                        
                        header('Location: ../index.php');
                    } catch (PDOException $e) {
                        echo "Error: " . $e->getMessage();
                        }
                    
                            
                            
                            
                    
                }
                    
                
            }
        ?>
            
            
            <form action="registration.php" method ="post">
                <div class ="reg_info">
                   
                    <input type ="text" name ="first_name" placeholder ="First Name:"value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>">
                    <i class='bx bxs-user'></i>
                </div>
                <div class ="reg_info">
                    <input type ="text" name ="last_name" placeholder ="Last Name:"value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>">
                    <i class='bx bxs-user'></i>
                </div>
            
                <div class ="reg_info">
                    <input type ="text" name ="email" placeholder ="Email:"value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    <i class='bx bxs-envelope'></i>
                </div>
                <div class ="reg_info">
                    <input type ="password" name ="password" placeholder ="Password (At least 8 characters required):">
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <div class ="reg_info">
                    <input type ="password" name ="confirm_password" placeholder ="Confirm Password:">
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <div class ="register">
                    <input type ="submit" value = "Register" name ="submit">
                </div>
                
                 <div class ="user_login">
                    <p style="color: white;font-size: 25px; font-weight: bold; text-align: center;">Already have an account?<a href='login.php' style="color: white; text-decoration: underline;"> Login Now </a></p>
                            
                </div>
            </form>
            
            
            <div class ="footer" > <?php include '../view/footer.php'; ?>     </div>
            
            
        </div>
        
        
        
        
        
    </body>
</html>


