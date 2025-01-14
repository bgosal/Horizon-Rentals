<?php


session_start();
require_once '../model/registration_login.php';

if (isset($_GET['postID']) && !empty($_GET['postID'])) {
    $postID=$_GET['postID'];
    
    //echo $postID;
    
  $query = "SELECT * FROM rental_posts WHERE postID = :postID";
    $statement = $db->prepare($query);
$statement->bindValue(':postID', $postID);

$statement->execute();
$postDetails = $statement->fetch(PDO::FETCH_ASSOC);

try {

        $statement->execute();
        $postDetails = $statement->fetch(PDO::FETCH_ASSOC);

        
        if (!$postDetails) {
            echo "No listing found with that ID.";
        }
        
    } catch (PDOException $e) {
        
        echo "Error: " . $e->getMessage();
    }
}



$cities = [ "Abbotsford", "Burnaby", "Chilliwack", "Coquitlam", "Delta", "Kamloops", "Kelowna", "Langley", "Maple Ridge", "Mission", "Nanaimo", "Penticton", "Prince George", "Richmond", "Surrey", "Vancouver", "Victoria", "White Rock"];
$provinces = ["BC" => "British Columbia"]; 



?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Listing</title>
    <link href='https://fonts.googleapis.com/css?family=Abel' rel='stylesheet'>
    <link rel ='stylesheet' href ='../style.css'>
</head>
<body class="editListing">
    <?php include '../view/nav.php'; ?>
    
    <h1>Edit Listing</h1>
    <div class="edit_listing">
        
        
        
        
        
        

        
        <form action="updatePost.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="postID" value="<?php echo htmlspecialchars($postID); ?>">
            
            <h2> Property Address </h2>
                <strong>Address:</strong>
                <input type="text" name="address" value="<?php echo htmlspecialchars($postDetails['address']); ?>"required>
           
            
            
            
                <strong>Number:</strong>
                <input type="text" name="unit_number" value="<?php echo htmlspecialchars($postDetails['unit_number']); ?>">
            
            
            
                <strong>City:</strong>
                <select name="city"required>
                    <?php foreach ($cities as $city): ?>
                        <option value="<?php echo $city; ?>" <?php if ($postDetails['city'] == $city) echo 'selected'; ?>><?php echo $city; ?></option>
                    <?php endforeach; ?>
                </select>
            
            
           
                <strong>Province:</strong>
                <select name="province">
                    <?php foreach ($provinces as $provinceCode => $provinceName): ?>
                        <option value="<?php echo $provinceCode; ?>" <?php if ($postDetails['province'] == $provinceCode) echo 'selected'; ?>><?php echo $provinceName; ?></option>
                    <?php endforeach; ?>
                </select>
            
            
            
                <strong>Postal Code (A1A 1A1):</strong>
                <input type="text" name="postal_code" value="<?php echo htmlspecialchars($postDetails['postal_code']); ?>" pattern="\b[A-Za-z][0-9][A-Za-z] ?[0-9][A-Za-z][0-9]\b" title="Please enter a valid postal code (e.g., M4B 1B3)." required>

                
                
                <h2> Property Information </h2>
                <strong>Bedrooms:</strong>
                <input type="number"  min="0" name="bedrooms" value="<?php echo htmlspecialchars($postDetails['bedrooms']); ?>"required>
            
                <strong>Bathrooms:</strong>
                <input type="number" min="0" name="bathrooms" value="<?php echo htmlspecialchars($postDetails['bathrooms']); ?>"required>
            
                
                <strong>Kitchens:</strong>
                <input type="number" min="0" name="kitchens" value="<?php echo htmlspecialchars($postDetails['kitchens']); ?>"required>
            
                
                <strong>Area (sq ft):</strong>
                <input type="number" min="0" step="0.01" name="area" value="<?php echo htmlspecialchars($postDetails['area']); ?>"required>
                
                <strong>Number of Parking Spots:</strong>
                <input type="number" min="0"  name="parking" value="<?php echo htmlspecialchars($postDetails['parking']); ?>"required>
                
                
                <h2> Pricing </h2>
                <strong>Pricing:</strong>
                <input type="number" min="0" step="0.01" name="price" value="<?php echo htmlspecialchars($postDetails['price']); ?>"required>
                
<!--                <h2> Photos</h2>
                
                <strong>Upload New Photos:</strong>
                <input type="file" name="pictures[]" accept="image/*" multiple>-->
           
            
            
                <input type="submit" name="submit" value="Update">
          
        </form>
 </div>
    
    <?php include '../view/footer.php'; ?>
</body>
</html>