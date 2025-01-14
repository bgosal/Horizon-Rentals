<?php
session_start();






?>
<html>
    <head>
        <meta charset="UTF-8">
        <title> Post A Rental </title>
         <link href='https://fonts.googleapis.com/css?family=Abel' rel='stylesheet'>
         <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel ='stylesheet'>
        <link rel ='stylesheet' href ='../style.css'>
    </head>
    <body class ='post_rental'>
        <?php include '../view/nav.php'; ?>
        <div class = "container2">
            <h1>Post A New Rental</h1>
            <h2>Property Address</h2>
            
            <?php
            
            if (isset($_POST["submit"])){
                $address = filter_input(INPUT_POST,'address');
                $number = filter_input(INPUT_POST, 'number');
                $city = filter_input(INPUT_POST, 'city');
                $province = filter_input(INPUT_POST, 'province');
                $postal_code = filter_input(INPUT_POST, 'postal_code');
                
                
                //property_description
                $bedrooms = filter_input(INPUT_POST,'bedrooms');
                $bathrooms = filter_input(INPUT_POST,'bathrooms');
                $kitchens = filter_input(INPUT_POST,'kitchens');
                $area = filter_input(INPUT_POST,'area', FILTER_VALIDATE_FLOAT);
                $parking = filter_input(INPUT_POST, 'parking');
                
                //property price
                $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
                
                
                $pictures = $_FILES['pictures']; 
                
                $errors = array();
            
                if(!isset($address) || !isset($city)  || !isset($postal_code) || !isset($bedrooms) || !isset($bathrooms) || !isset($kitchens) || !isset($area) || !isset($parking) || !isset($price))  {
                    array_push($errors, "All fields are required");   
                }
                //verify??
                if (!preg_match('/^[A-Za-z]\d[A-Za-z] \d[A-Za-z]\d$/', $postal_code)) {
                    array_push($errors, "Invalid postal code format! The correct format is A1A 1A1.");
                 }
                 
                
                
                
                if (count($errors)>0){
                    foreach ($errors as $error){
                        echo $error; 
                    }
                        
                }
                
                       
                                
         
                else{
                
                require_once '../model/registration_login.php';
                $query = 'INSERT INTO rental_posts 
                            (clientID, address, unit_number, city, province, postal_code, bedrooms, bathrooms, kitchens, area, parking, price) 
                            VALUES (:clientID, :address, :unit_number, :city, :province, :postal_code, :bedrooms, :bathrooms, :kitchens, :area, :parking, :price)';

                    
                    $statement = $db-> prepare($query);
                    $statement -> bindValue(':clientID',$_SESSION['clientID'] );
                    $statement-> bindValue(':address', $address);
                    $statement-> bindValue(':unit_number', $number);   
                    $statement-> bindValue(':city', $city);
                    $statement-> bindValue(':province', $province);
                    $statement-> bindValue(':postal_code', $postal_code);
                    $statement->bindValue(':bedrooms', $bedrooms);
                    $statement->bindValue(':bathrooms', $bathrooms);
                    $statement->bindValue(':kitchens', $kitchens);
                    $statement->bindValue(':area', $area);
                    $statement->bindValue(':parking', $parking);
                    $statement->bindValue(':price', $price);
                    
                    
                    
                   
                    
                    try {
                        $statement->execute();
                        
                        
                    
                
                        $postID = $db->lastInsertId();


                    if (isset($_FILES['pictures'])) {
                        for ($i = 0; $i < count($_FILES['pictures']['name']); $i++) {
                            if ($_FILES['pictures']['error'][$i] === UPLOAD_ERR_OK) {
                                $imageData = file_get_contents($_FILES['pictures']['tmp_name'][$i]);
                                $imageType = $_FILES['pictures']['type'][$i]; 

                                $imgQuery = 'INSERT INTO images (postID, imageData, imageType) VALUES (:postID, :imageData, :imageType)';
                                $imgStatement = $db->prepare($imgQuery);
                                $imgStatement->bindValue(':postID', $postID);
                                $imgStatement->bindValue(':imageData', $imageData, PDO::PARAM_LOB);
                                $imgStatement->bindValue(':imageType', $imageType);
                                
                                try {
                        $imgStatement->execute();
                        $imgStatement->closeCursor();
                        header('Location:../rental_postings/listing.php?postID=' . $postID);
                        
                    } catch (PDOException $e) {
                        echo "Error: " . $e->getMessage();
                        }
                                
                            } 
                        }
                    }
                        header('Location:../rental_postings/listing.php?postID=' . $postID);
                   } catch (PDOException $e) {
                        echo "Error: " . $e->getMessage();
                        }
                       
                   }
                        
            }
                        
                
            
            
            
            
            
           
            
            
            
            
            ?>
            
            
            
            
            <form action="post_rental.php" method="post" enctype="multipart/form-data">
                <div class ="rental_info">
                     <label for="address"><strong>Address :</strong></label>
                    <input type ="text" name ="address" placeholder ="Address:"value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>"required>
                    
                </div>
                <div class ="rental_info">
                    <label for="number"><strong>Number:</strong></label>
                    <input type ="text" name ="number" placeholder ="Apartment, suite, unit, floor, etc:"value="<?php echo isset($_POST['number']) ? htmlspecialchars($_POST['number']) : ''; ?>" required>
 </div>

                <?php
                $cities = array ("Abbotsford", "Burnaby", "Chilliwack", "Coquitlam", "Delta", "Kamloops", "Kelowna", "Langley", "Maple Ridge", "Mission", "Nanaimo", "Penticton", "Prince George", "Richmond", "Surrey", "Vancouver", "Victoria", "White Rock");
                ?>

                <div class ="rental_info" required>
                    <label for="city"><strong>City:</strong></label>
                    <select name="city" required>
                <option value="" disabled selected>Select your city</option>
                    <?php 
                        $selectedCity = isset($_POST['city']) ? $_POST['city'] : '';
                        foreach ($cities as $city) {
                            echo '<option value="' . htmlspecialchars($city) . '"' . ($selectedCity === $city ? ' selected' : '') . '>' . htmlspecialchars($city) . '</option>';
                        }
                        ?>
                </select>
                </div>
                <div class ="rental_info">
                    <label for="province"><strong>Province:</strong></label>
                    <select name="province">
                        <option value="BC">British Columbia</option>
                    </select>
                </div>
                <div class ="rental_info">
                    <label for="postal_code"><strong>Postal Code (A1A 1A1):</strong></label>
                    <input type ="text" name ="postal_code" placeholder ="Postal Code:"value="<?php echo isset($_POST['postal_code']) ? htmlspecialchars($_POST['postal_code']) : ''; ?>" required pattern="[A-Za-z]\d[A-Za-z] ?\d[A-Za-z]\d">
                </div>
                
                
                
               
                <h3>Property Information</h3>
                <div class ="rental_info">
                    <label for="bedrooms"><strong>Bedrooms:</strong></label>
                    <input type ="number" min="0" name ="bedrooms" placeholder = "Number of Bedrooms:"value="<?php echo isset($_POST['bedrooms']) ? htmlspecialchars($_POST['bedrooms']) : ''; ?>" required>
</div>

                
                
                <div class ="rental_info">
                    <label for="bathrooms"><strong>Bathrooms:</strong></label>
                    <input type ="number" min="0" name ="bathrooms" placeholder = "Number of Bathrooms:" value="<?php echo isset($_POST['bathrooms']) ? htmlspecialchars($_POST['bathrooms']) : ''; ?>" required>
               
                    
                </div>
                
                
                <div class ="rental_info">
                  <label for="kitchens"><strong>Kitchens:</strong></label>
                    <input type ="number" min="0" name ="kitchens" placeholder = "Number of Kitchens:"value="<?php echo isset($_POST['kitchens']) ? htmlspecialchars($_POST['kitchens']) : ''; ?>" required>
                    
                </div>
                
             
                <div class ="rental_info">
                    <label for="area"><strong>Area (sq ft):</strong></label>
                    <input type ="number" min="0" step="0.01" name ="area" placeholder = "Area (sq ft): "value="<?php echo isset($_POST['area']) ? htmlspecialchars($_POST['area']) : ''; ?>" required>
                    
                </div>
                
                 <div class ="rental_info">
                     <label for="parking"><strong>Number of Parking Spots:</strong></label>
                    <input type ="number" min="0" name ="parking" placeholder = "Number of Parking Spots: "value="<?php echo isset($_POST['parking']) ? htmlspecialchars($_POST['parking']) : ''; ?>" required>
                </div>
                
                
                
                <h4>Pricing</h4>
                <div class ="rental_info">
                    <label for="price"><strong>Price per night (CAD$): </strong></label>
                    <input type ="number" min="0" step="0.01" name ="price" placeholder = "Price per night (CAD$):  "value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>" required>
                </div>
                
                <h6>Property Photos</h6>
                
                 <div class="rental_info">
                    <input type="file" name="pictures[]" accept="image/*" multiple>
                </div>
                
                
               <div class ="post_rental_bttn">
                    <input type ="submit" value = "Post Rental" name ="submit">
                </div>
                
<div class ='footer'> <?php include '../view/footer.php'; ?>     </div>
                   
                </div>
                
                
               

                
                
                
                    
            </form>     
            
            
            
            
            
        </div>
        
        
        
    </body>
</html>

