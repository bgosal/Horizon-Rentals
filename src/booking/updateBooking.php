<?php
   
session_start();
require_once '../model/registration_login.php'; 




$bookingID = $_POST['bookingID'];
$checkInDate = $_POST['check_in_date'];
$checkInTime = $_POST['check-in-time'];
$checkOutDate = $_POST['check_out_date'];
$checkOutTime = $_POST['check-out-time'];

$totalCost = $_POST['total_cost'];
$costForStay = $_POST['cost_for_stay'];
$cleaningFee = $_POST['cleaning_fee'];
$serviceFee = $_POST['service_fee'];
$taxes = $_POST['taxes'];


if (isset($_POST['action'])) {
    $action = $_POST['action'];
    $bookingID = $_POST['bookingID'];


if ($action == 'update') {
$query = "UPDATE bookings SET 
            CheckInDate = :checkInDate,
            CheckInTime =:checkInTime,
            CheckOutDate = :checkOutDate,
            CheckOutTime =:checkOutTime,
            TotalCost = :totalCost, 
            CostForStay = :costForStay, 
            CleaningFee = :cleaningFee, 
            ServiceFee = :serviceFee, 
            Taxes = :taxes 
          WHERE bookingID = :bookingID";

$statement = $db->prepare($query);
$statement->bindValue(':bookingID', $bookingID);
$statement->bindValue(':checkInDate', $checkInDate);
$statement->bindValue(':checkInTime', $checkInTime);
$statement->bindValue(':checkOutTime', $checkOutTime);
$statement->bindValue(':checkOutDate', $checkOutDate);
$statement->bindValue(':totalCost', $totalCost);
$statement->bindValue(':costForStay', $costForStay);
$statement->bindValue(':cleaningFee', $cleaningFee);
$statement->bindValue(':serviceFee', $serviceFee);
$statement->bindValue(':taxes', $taxes);

try {
    $statement->execute();

    $_SESSION['message'] = 'Booking updated successfully.';
    header('Location: modifyBooking.php?bookingID=' . $bookingID);
 
} catch (PDOException $e) {

    echo "Error: " . $e->getMessage();
}


}


elseif ($action == 'delete') {
    
    
       
        $userCheckQuery = "SELECT clientID FROM bookings WHERE bookingID = :bookingID";
        $checkStmt = $db->prepare($userCheckQuery);
        $checkStmt->bindValue(':bookingID', $bookingID);
        $checkStmt->execute();
        $bookingUser = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($bookingUser) {
            
           
                $query = "DELETE FROM bookings WHERE bookingID = :bookingID";
                $statement = $db->prepare($query);
                $statement->bindValue(':bookingID', $bookingID);
                try {
                    $statement->execute();
                   $_SESSION['message'] = 'Booking deleted successfully.';
                   if (isset($_SESSION['clientID']) && $_SESSION['clientID'] == $bookingUser['clientID']){
                   header('Location: myBookings.php');
                   }
                   
                   else{
                       header('Location: clientBookings.php');
                   }
                   
                    
                    } catch (PDOException $e) {
                        echo "Error: " . $e->getMessage();
                }
                
            }
        
}


  exit;

}


?>





    

