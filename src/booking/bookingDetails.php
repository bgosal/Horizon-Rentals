<?php
session_start();
//echo 'Session clientID: ' . $_SESSION['clientID'];
if (!isset($_GET['bookingID']) || empty($_GET['bookingID'])) {
    
    echo "Invalid request. Booking ID is required.";
    exit;
}




$bookingID = $_GET['bookingID'];


require_once '../model/registration_login.php'; 
$query = "SELECT bookings.*, rental_posts.* ,bookings.clientID AS bookingClientID 
              FROM bookings 
              JOIN rental_posts ON bookings.postID = rental_posts.postID 
              WHERE bookings.bookingID = :bookingID";
$statement = $db->prepare($query);
$statement->bindValue(':bookingID', $bookingID);

try {
    $statement->execute();
    $booking = $statement->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
   
    echo "Error: " . $e->getMessage();
    exit;
}
//echo 'Booking clientID: ' . $booking['bookingClientID'];
   
?>


<html>
    <head>
        <meta charset="UTF-8">
        <title> Booking Details </title>
        <link href='https://fonts.googleapis.com/css?family=Abel' rel='stylesheet'>
         <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel ='stylesheet'>
        
        <link rel ='stylesheet' href ='../style.css'>
        
    </head>
    <?php include '../view/nav.php'; ?>
    <body class = 'bookingDetails'>
        <h1>Booking Summary</h1>
        <div class ="new_container">
            
        <?php if ($_SESSION['clientID'] == $booking['bookingClientID']): ?> 
        <form action="modifyBooking.php" method="get" style="margin-bottom: 10px;">
        <input type="hidden" name="bookingID" value="<?php echo htmlspecialchars($booking['bookingID']); ?>">
        <button type="submit">Modify Booking</button>
        </form>
            <?php endif; ?>
            
        <a href="../rental_postings/listing.php?postID=<?php echo htmlspecialchars($booking['postID']); ?>" class="button-link">View Listing</a>
            

            

                               
            
             <div class="cost-details">
                 
            <h2>Cost Details</h2>
            <h4>Total Cost: $<?php echo htmlspecialchars($booking["totalCost"]); ?></h4>
            <div class="detail">
                <strong>Price per Night:</strong>
                <span><?php echo "$".htmlspecialchars($booking["price"]); ?></span>
            </div>
            <div class="detail">
                <strong>Cost for Stay:</strong>
                <span><?php echo "$".htmlspecialchars($booking["CostForStay"]); ?></span>
            </div>
            <div class="detail">
                <strong>Cleaning Fee:</strong>
                <span><?php echo "$".htmlspecialchars($booking["CleaningFee"]); ?></span>
            </div>
            <div class="detail">
                <strong>Service Fee:</strong>
                <span><?php echo "$".htmlspecialchars($booking["ServiceFee"]); ?></span>
            </div>
            <div class="detail">
                <strong>Taxes:</strong>
                <span><?php echo "$".htmlspecialchars($booking["taxes"]); ?></span>
            </div>
            
        </div>
        
        
        
        <div class="booking-details">
            <h2> Booking Details </h2>
            <div class="detail">
            <strong>Booking Date:</strong> <span> <?php echo htmlspecialchars($booking["BookingDate"]); ?></span>
        </div>
        <div class="detail">
            <strong>Check In Date:</strong> <span> <?php echo htmlspecialchars($booking["CheckInDate"]); ?></span>
        </div>
        <div class="detail">
        <strong>Check Out Date:</strong>
        <span><?php echo htmlspecialchars($booking["CheckInTime"]); ?></span>
        </div>
        <div class="detail">
        <strong>Check In Time:</strong>
        <span><?php echo htmlspecialchars($booking["CheckInTime"]); ?></span>
        </div>
        <div class="detail">
        <strong>Check Out Time:</strong>
        <span><?php echo htmlspecialchars($booking["CheckOutTime"]); ?></span>
        </div>
        
        
        </div>
        
        
       

        
                <div class="property-info">
                    
           <h2>
            Property Information
            </h2>

            
            <div class="detail">
                <strong>Address:</strong>
                <span><?php echo htmlspecialchars($booking["address"]); ?></span>
            </div>
            
            <?php if ((!empty($booking["unit_number"]))): ?>
                <div class="detail">
                    <strong>Unit, Floor, Appt Number:</strong>
                    <span><?php echo htmlspecialchars($booking["unit_number"]); ?></span>
                </div>
            <?php endif; ?>

            
            
            <div class="detail">
                <strong>City:</strong>
                <span><?php echo htmlspecialchars($booking["city"]); ?></span>
            </div>
            <div class="detail">
                <strong>Province:</strong>
                <span><?php echo htmlspecialchars($booking["province"]); ?></span>
            </div>
            <div class="detail">
                <strong>Postal Code:</strong>
                <span><?php echo htmlspecialchars($booking["postal_code"]); ?></span>
            </div>
            <div class="detail">
                <strong>Bedrooms:</strong>
                <span><?php echo htmlspecialchars($booking["bedrooms"]); ?></span>
            </div>
            <div class="detail">
                <strong>Bathrooms:</strong>
                <span><?php echo htmlspecialchars($booking["bathrooms"]); ?></span>
            </div>
            
            <div class="detail">
                <strong>Kitchens:</strong>
                <span><?php echo htmlspecialchars($booking["kitchens"]); ?></span>
            </div>
            
            <div class="detail">
                <strong>Parking:</strong>
                <span><?php echo htmlspecialchars($booking["parking"]); ?></span>
            </div>
            <div class="detail">
                <strong>Area:</strong>
                <span><?php echo htmlspecialchars($booking["area"]); ?> sqft</span>
            </div>

        </div>

        
        </div>
        
        
        </body>
</html>

        
        
