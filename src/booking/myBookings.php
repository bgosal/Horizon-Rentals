<?php

session_start();
$currentDate = date('Y-m-d');

$clientID = $_SESSION['clientID'];
$filter = isset($_GET['booking_filter']) ? $_GET['booking_filter'] : 'all';
$sortOption = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'date_desc'; 
$numBookings = isset($_GET['num_bookings']) ? $_GET['num_bookings'] : '5'; 
require_once '../model/registration_login.php'; 


    $query = "SELECT bookings.*, rental_posts.* FROM bookings "
            . "JOIN rental_posts ON bookings.postID = rental_posts.postID 
              WHERE bookings.clientID = :clientID";
    if ($filter === 'upcoming') {
        $query .= " AND CheckInDate >= '$currentDate'";
    } elseif ($filter === 'past') {
        $query .= " AND CheckInDate < '$currentDate'";
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





    try {
        $statement = $db->prepare($query);
        $statement->bindValue(':clientID', $clientID);
        $statement->execute();
        $bookings = $statement->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {

        echo " error: " . $e->getMessage();
        $bookings = []; 
    }





?>




<html>
    <head>
        <meta charset="UTF-8">
        <title> My Bookings</title>
        <link href='https://fonts.googleapis.com/css?family=Abel' rel='stylesheet'>
         <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel ='stylesheet'>
        
        <link rel ='stylesheet' href ='../style.css'>
        
    </head>
    <body class = 'myBookings'>
        <?php include '../view/nav.php'; ?>
        
        
        
     <h1>My Bookings</h1>
     
     
            <div class="filter-options">
            <form action="" method="GET">
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
                    <option value="5" <?php if ($numBookings === '5') echo 'selected'; ?>>5</option>
                    <option value="10" <?php if ($numBookings === '10') echo 'selected'; ?>>10</option>
                    <option value="15" <?php if ($numBookings === '15') echo 'selected'; ?>>15</option>
                    <option value="all" <?php if ($numBookings === 'all') echo 'selected'; ?>>All</option>
                </select>

                
                
                    <button type="submit">Filter</button>
                </form>
            </div>
 <?php
                    if (isset($_SESSION['message'])) {
                    echo "<script type='text/javascript'>alert('" . $_SESSION['message'] . "');</script>";
                        unset($_SESSION['message']);
                    }
                    ?>
     
                <?php 
                
                $upcomingBookings = [];
                $pastBookings = [];

                
                foreach ($bookings as $booking) {
                    $checkInDate = $booking['CheckInDate'];
                        if ($currentDate <= $checkInDate) {
                            $upcomingBookings[] = $booking;
                        } else {
                        $pastBookings[] = $booking;
                        }
                        }
                        
                       if ($numBookings !== 'all') {
                         $limit = (int) $numBookings;
                        $upcomingBookings = array_slice($upcomingBookings, 0, $limit);
                        $pastBookings = array_slice($pastBookings, 0, $limit);
    
                       }

                     
                     
                     
                     function generateBookingRows($bookings, $isUpcoming) {
                            foreach ($bookings as $booking) {
                        ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($booking['bookingID']); ?></td>
                                    
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

                                    <?php if ($isUpcoming): ?>
                                        <td>
                                            <form action="modifyBooking.php" method="get" style="margin-bottom: 10px;">
                                                <input type="hidden" name="bookingID" value="<?php echo htmlspecialchars($booking['bookingID']); ?>">
                                                <button type="submit">Modify Booking</button>
                                            </form>
                                            <form action="updateBooking.php" method="post" onsubmit="return confirm('Are you sure you want to delete this booking?');">
                                                <input type="hidden" name="bookingID" value="<?php echo htmlspecialchars($booking['bookingID']); ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <button type="submit" style="background-color: red; color: white;">Delete Booking</button>
                                            </form>
                                        </td>
                                    <?php else: ?>
                                        <td>Completed</td>
                                    <?php endif; ?>
                                </tr>
                        <?php
                            }
                        }


                   
                    ?>
     
     
     
     
                <?php if ($filter === 'all' || $filter === 'upcoming'): ?>
                        <h2>Upcoming Bookings</h2>
                           <?php if (!empty($upcomingBookings)): ?>
                           <table>
                               <thead>
                                   <tr>
                                       <th>Booking ID</th>
                                       
                                       <th>Booking Date</th>
                                       <th>Total Cost</th>
                                       <th>Check-in Date</th>
                                       <th>Check-out Date</th>
                                       <th>Booking Details</th>
                                       <th>Manage Booking</th>
                                       
                                   </tr>
                               </thead>
                               <tbody>

                                   <?php generateBookingRows($upcomingBookings,true); ?>
                       </tbody>
                   </table>

                   <?php else: ?>
                       <p style="color: white;font-size: 25px; font-weight: bold; text-align: center;">You have no upcoming bookings.</p>
                   <?php endif; ?>
                       <?php endif; ?>
                       
                       <?php if ($filter === 'all' || $filter === 'past'): ?>
                       <h2>Previous Bookings</h2>
                            <?php if (!empty($pastBookings)): ?>
                            <table>
                                   <thead>
                                   <tr>
                                       <th>Booking ID</th>
                                       
                                       <th>Booking Date</th>
                                       <th>Total Cost</th>
                                       <th>Check-in Date</th>
                                       <th>Check-out Date</th>
                                       <th>Booking Details</th>
                                       <th>Manage Booking</th>
                                       
                                   </tr>
                               </thead>
                                <tbody>
                                    <?php generateBookingRows($pastBookings, false); ?>
                                </tbody>
                            </table>
                            <?php else: ?>
                                <p style="color: white;font-size: 25px; font-weight: bold; text-align: center;">You have no previous bookings.</p>
                            <?php endif; ?>
                                 <?php endif; ?>




                    
                    
                     
          
  
<?php include '../view/footer.php'; ?>
</body>

</html>
     
    





