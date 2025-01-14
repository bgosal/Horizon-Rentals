<?php

session_start();


$ownerID = $_SESSION['clientID']; 
$currentDate = date('Y-m-d');
require_once '../model/registration_login.php'; 

$selectedPostID = isset($_GET['listingFilter']) && !empty($_GET['listingFilter']) ? $_GET['listingFilter'] : null;
$filter = isset($_GET['booking_filter']) ? $_GET['booking_filter'] : 'all';
$sortOption = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'date_asc';
$numBookings = isset($_GET['num_bookings']) ? $_GET['num_bookings'] : 'all'; 


$allListingsQuery = "SELECT postID, address FROM rental_posts WHERE clientID = :ownerID";
$allListingsStmt = $db->prepare($allListingsQuery);
$allListingsStmt->bindValue(':ownerID', $ownerID, PDO::PARAM_INT);
$allListingsStmt->execute();
$allListings = $allListingsStmt->fetchAll(PDO::FETCH_ASSOC);


$query = "SELECT rental_posts.*, bookings.*, users.* 
          FROM rental_posts
          JOIN bookings ON rental_posts.postID = bookings.postID 
          JOIN users ON bookings.clientID = users.clientID
          WHERE rental_posts.clientID = :ownerID";

if ($selectedPostID) {
    $query .= " AND rental_posts.postID = :postID";
}

if ($filter === 'upcoming') {
    $query .= " AND bookings.CheckInDate > :currentDate";
} elseif ($filter === 'past') {
    $query .= " AND bookings.CheckOutDate < :currentDate";
}


if ($sortOption === 'date_asc') {
    $query .= " ORDER BY BookingDate ASC";
} elseif ($sortOption === 'date_desc') {
    $query .= " ORDER BY BookingDate DESC";
} elseif ($sortOption === 'cost_asc') {
    $query .= " ORDER BY totalCost ASC";
} elseif ($sortOption === 'cost_desc') {
    $query .= " ORDER BY totalCost DESC";
}



$statement = $db->prepare($query);
$statement->bindValue(':ownerID', $ownerID, PDO::PARAM_INT);



if ($selectedPostID) {
    $statement->bindValue(':postID', $selectedPostID, PDO::PARAM_INT);
}

if ($filter === 'upcoming' || $filter === 'past') {
    $statement->bindValue(':currentDate', $currentDate);
}


$statement->execute();
$bookings = $statement->fetchAll(PDO::FETCH_ASSOC);


$groupedBookings = [];
foreach ($bookings as $booking) {
    $groupedBookings[$booking['postID']][] = $booking;
}
function displayBookingsTable($bookings) {
?>
    <table>
        <tr>
            <th>Booking ID</th>
            <th>Client Name</th>
            <th>Booking Date</th>
            <th>Total Cost</th>
            <th>Check-in Date</th>
            <th>Check-out Date</th>
            <th>Booking Details</th>
        </tr>
        <?php foreach ($bookings as $booking): ?>
            <tr>
                <td><?php echo htmlspecialchars($booking['bookingID']); ?></td>
                <td><?php echo htmlspecialchars($booking['first_name']) . " " . htmlspecialchars($booking['last_name']); ?></td>
                <td><?php echo htmlspecialchars($booking['BookingDate']); ?></td>
                <td>$<?php echo htmlspecialchars($booking['totalCost']); ?></td>
                <td><?php echo htmlspecialchars($booking['CheckInDate']); ?></td>
                <td><?php echo htmlspecialchars($booking['CheckOutDate']); ?></td>
                <td>
                                        <form action="bookingDetails.php" method="get" style="margin-bottom: 10px;">
                                            <input type="hidden" name="bookingID" value="<?php echo htmlspecialchars($booking['bookingID']); ?>">
                                            <button type="submit" style="background-color: orange; color: white;">View Booking Details</button>
                                        </form>
                                    </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php
}

?>


<html>
    <head>
        <meta charset="UTF-8">
        <title> Client Bookings</title>
        <link href='https://fonts.googleapis.com/css?family=Abel' rel='stylesheet'>
         <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel ='stylesheet'>
        
        <link rel ='stylesheet' href ='../style.css'>
        
    </head>
    <body class = 'clientBookings'>
        
        
        
        
     <h1>Client Bookings</h1>
    <?php include '../view/nav.php'; ?>
     <div class ="filter-options">
     <form action="" method="get">
        
                <select name="listingFilter" id="listingFilter">
        <option value="">Select a Listing</option>
        <?php foreach ($allListings as $listing): ?>
            <option value="<?php echo htmlspecialchars($listing['postID']); ?>" <?php if (isset($_GET['listingFilter']) && $_GET['listingFilter'] == $listing['postID']) echo 'selected'; ?>>
                <?php echo htmlspecialchars($listing['address']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    
        
         
         
         
                <select name="booking_filter">
                    <option value="all" <?php if ($filter === 'all') echo 'selected'; ?>>All Bookings</option>
                    <option value="upcoming" <?php if ($filter === 'upcoming') echo 'selected'; ?>>Upcoming Bookings</option>
                    <option value="past" <?php if ($filter === 'past') echo 'selected'; ?>>Past Bookings</option>
                    </select>
                
                
                
             
             <select name="sort_by">
                    
                     <option value="date_asc" <?php if ($sortOption === 'date_asc') echo 'selected'; ?>>Date (Oldest First)</option>
                     <option value="date_desc" <?php if ($sortOption === 'date_desc') echo 'selected'; ?>>Date (Newest First)</option>
                    <option value="cost_asc" <?php if ($sortOption === 'cost_asc') echo 'selected'; ?>>Total Cost (Low to High)</option>
                    <option value="cost_desc" <?php if ($sortOption === 'cost_desc') echo 'selected'; ?>>Total Cost (High to Low)</option>

                </select>
         
         <select name="num_bookings">
                    <option value="2" <?php if ($numBookings === '2') echo 'selected'; ?>>2 Bookings</option>
                    <option value="5" <?php if ($numBookings === '5') echo 'selected'; ?>>5 Bookings</option>
                    <option value="10" <?php if ($numBookings === '10') echo 'selected'; ?>>10 Bookings</option>
                    <option value="all" <?php if ($numBookings === 'all') echo 'selected'; ?>>All Bookings</option>
                </select>
             
<button type="submit">Filter</button>
     </form>
     </div>
     
     

    <?php if (!empty($groupedBookings)): ?>
        <?php foreach ($groupedBookings as $postID => $bookings): ?>
            <?php
            $firstBooking = reset($bookings); 

           
            $upcomingBookings = [];
            $previousBookings = [];

            foreach ($bookings as $booking) {

                $checkInDate = $booking['CheckInDate'];
                if ($checkInDate > $currentDate) {
                    $upcomingBookings[] = $booking;
                } else {
                    $previousBookings[] = $booking;
                }
            }
            
            if ($numBookings !== 'all') {
                $limit = (int) $numBookings; 
                $upcomingBookings = array_slice($upcomingBookings, 0, $limit);
                $previousBookings = array_slice($previousBookings, 0, $limit);
            }

          
        ?>
            <h2>
                <a href ="../rental_postings/listing.php?postID=<?php echo htmlspecialchars($firstBooking['postID']); ?>" style="text-decoration: none; color: white;">
                <?php echo htmlspecialchars($firstBooking['address']) . ', ' . htmlspecialchars($firstBooking['city']) . ', ' . htmlspecialchars($firstBooking['province']) . ', ' . htmlspecialchars($firstBooking['postal_code']); ?>
                             </a>

            </h2>
          
     <h3><?php
                echo "Bedrooms: " . htmlspecialchars($firstBooking['bedrooms']);
                echo ", Bathrooms: " . htmlspecialchars($firstBooking['bathrooms']);
                
                    echo ", Kitchens: " . htmlspecialchars($firstBooking['kitchens']);
                
                echo ", Area: " . htmlspecialchars($firstBooking['area']) . " sqft";
                
                    echo ", Parking: " . htmlspecialchars($firstBooking['parking']);
                
            ?></h3>
            <h3>Listing ID: <?php echo htmlspecialchars($postID); ?></h3>

            
            <?php if ($filter === 'all' || $filter === 'upcoming'): ?>
                <?php if (!empty($upcomingBookings)): ?>
                    <h4>Upcoming Bookings</h4>
                    <?php displayBookingsTable($upcomingBookings); ?>
                <?php elseif ($filter === 'upcoming'): ?>
                    <h4>You have no upcoming bookings for this address.</h4>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($filter === 'all' || $filter === 'past'): ?>
                <?php if (!empty($previousBookings)): ?>
                    <h4>Previous Bookings</h4>
                    <?php displayBookingsTable($previousBookings); ?>
                <?php elseif ($filter === 'past'): ?>
                    <h4>You have no previous bookings for this address.</h4>
                <?php endif; ?>
            <?php endif; ?>

        <?php endforeach; ?>
    <?php else: ?>
        <h4>No booked clients.</h4>
    <?php endif; ?>

        
    <?php include '../view/footer.php'; ?>    

</body>
</html>


</body>
</html>
    
    