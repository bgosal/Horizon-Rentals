<?php

session_start();
require_once '../model/registration_login.php';


if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $bookingID = $_POST['bookingID'];
    $postID = $_POST['postID'];
    
    
    

if ($action == 'update') {
    
   $address = $_POST['address'];
$unit_number = $_POST['unit_number'];
$city = $_POST['city'];
$province = $_POST['province'];
$postal_code = $_POST['postal_code'];
$bedrooms = $_POST['bedrooms'];
$bathrooms = $_POST['bathrooms'];
$kitchens = $_POST['kitchens'];
$area = $_POST['area'];
$parking = $_POST['parking'];
$price = $_POST['price'];

    
    try {
        $updateQuery = "UPDATE rental_posts SET address = :address, unit_number = :unit_number, city = :city, province = :province, postal_code = :postal_code, bedrooms = :bedrooms, bathrooms = :bathrooms, kitchens = :kitchens, area = :area, parking = :parking, price = :price WHERE postID = :postID";
        $statement = $db->prepare($updateQuery);

        $statement->bindValue(':address', $address);
        $statement->bindValue(':unit_number', $unit_number);
        $statement->bindValue(':city', $city);
        $statement->bindValue(':province', $province);
        $statement->bindValue(':postal_code', $postal_code);
        $statement->bindValue(':bedrooms', $bedrooms);
        $statement->bindValue(':bathrooms', $bathrooms);
        $statement->bindValue(':kitchens', $kitchens);
        $statement->bindValue(':area', $area);
        $statement->bindValue(':parking', $parking);
        $statement->bindValue(':price', $price);
        $statement->bindValue(':postID', $postID);
       

        $statement->execute();

        $_SESSION['message'] = "Listing updated successfully.";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Error updating listing: " . $e->getMessage();
    }

    header('Location: listing.php?postID='.$postID); 
    exit;
}

elseif ($action == 'delete') {
    
    try {
        
        
        $deleteImagesQuery = "DELETE FROM images WHERE postID = :postID";
    $stmt1 = $db->prepare($deleteImagesQuery);
    $stmt1->bindValue(':postID', $postID); 
    $stmt1->execute();
        
        $deleteBookingsQuery = "DELETE FROM bookings WHERE postID = :postID";
        $stmt = $db->prepare($deleteBookingsQuery);
        $stmt->bindValue(':postID', $postID);
        $stmt->execute();
        
        $deleteQuery = "DELETE FROM rental_posts WHERE postID = :postID";
        $statement = $db->prepare($deleteQuery);
        $statement->bindValue(':postID', $postID);
        

        $statement->execute();

        $_SESSION['message'] = "Listing deleted successfully.";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Error deleting listing: " . $e->getMessage();
    }

    header('Location: myPosts.php'); 
    exit;
}

}
?>


