<?php
   
session_start();


require_once '../model/registration_login.php'; 

$postID = $_POST['postID']; 
$clientID = $_SESSION['clientID'];
$checkInDate = $_POST['check_in_date'];
$checkInTime = $_POST['check-in-time'];
$checkOutDate = $_POST['check_out_date'];
$checkOutTime = $_POST['check-out-time'];
$totalCost = $_POST['total_cost'];
$costForStay = $_POST['cost_for_stay'];
$cleaningFee = $_POST['cleaning_fee'];
$serviceFee = $_POST['service_fee'];
$taxes = $_POST['taxes'];
$bookingDate = date('Y-m-d'); 

$query = "INSERT INTO bookings (postID, clientID, CheckInDate, CheckInTime, CheckOutDate, CheckOutTime, totalCost, CostForStay, CleaningFee, ServiceFee, taxes, BookingDate) 
          VALUES (:postID, :clientID, :CheckInDate, :CheckInTime, :CheckOutDate, :CheckOutTime, :totalCost, :CostForStay, :CleaningFee, :ServiceFee, :taxes, :BookingDate)";
$statement = $db->prepare($query);
$statement->bindValue(':postID', $postID);
$statement->bindValue(':clientID', $clientID);
$statement->bindValue(':CheckInDate', $checkInDate); 
$statement->bindValue(':CheckInTime', $checkInTime);
$statement->bindValue(':CheckOutTime', $checkOutTime);
$statement->bindValue(':CheckOutDate', $checkOutDate); 
$statement->bindValue(':totalCost', $totalCost); 
$statement->bindValue(':CostForStay', $costForStay);
$statement->bindValue(':CleaningFee', $cleaningFee);
$statement->bindValue(':ServiceFee', $serviceFee);
$statement->bindValue(':taxes', $taxes);
$statement->bindValue(':BookingDate', $bookingDate); 
try {
    $statement->execute();
    $bookingID = $db->lastInsertId();
    header("Location: bookingDetails.php?bookingID=" . $bookingID);
      exit;
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

    
    
    

    
    
    







