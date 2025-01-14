<?php
session_start();
require_once '../model/registration_login.php'; 
//ini_set('display_errors', 1);
//error_reporting(E_ALL);



if (isset($_GET['bookingID']) && !empty($_GET['bookingID'])) {
    $bookingID = $_GET['bookingID'];
    $bookingID2 = $_GET['bookingID'];


    $query = "SELECT bookings.*, rental_posts.* 
              FROM bookings 
              JOIN rental_posts ON bookings.postID = rental_posts.postID 
              WHERE bookings.bookingID = :bookingID";
    $statement = $db->prepare($query);
    $statement->bindValue(':bookingID', $bookingID, PDO::PARAM_INT);
    $statement->execute();
    
    $booking = $statement->fetch(PDO::FETCH_ASSOC);
    
    $postID = $booking['postID'];
    
    $queryBookings = "SELECT CheckInDate, CheckOutDate FROM bookings WHERE postID = :postID AND bookingID != :currentBookingID";
    
    $statementBookings = $db->prepare($queryBookings);
    $statementBookings->bindValue(':postID', $postID, PDO::PARAM_INT);
    $statementBookings->bindValue(':currentBookingID', $bookingID2, PDO::PARAM_INT);
    $statementBookings->execute();

    $availablebookings = $statementBookings->fetchAll(PDO::FETCH_ASSOC);
    $statementBookings->closeCursor();


    $unavailableDates = [];


if ($availablebookings) { 
        foreach ($availablebookings as $availablebooking) {
            $period = new DatePeriod(
                new DateTime($availablebooking['CheckInDate']),
                new DateInterval('P1D'),
                (new DateTime($availablebooking['CheckOutDate']))->modify('+1 day') 
            );

            foreach ($period as $date) {
                $unavailableDates[] = $date->format("Y-m-d");
            }
        }
    }




            function generate_time_options($currentTime = '') {
                $options = '';
                for ($hour = 0; $hour < 24; $hour++) {
                    for ($minute = 0; $minute < 60; $minute += 30) {
                        $time = sprintf('%02d:%02d', $hour, $minute);
                        $selected = ($time == $currentTime) ? ' selected' : '';
            $options .= "<option value=\"$time\"$selected>$time</option>";
                    }
                }
                return $options;
            }

            echo '<script>';
echo 'var unavailableDates = ' . json_encode($unavailableDates) . ';';

echo '</script>';
    

echo "<script>
    var checkInDate = '{$booking['CheckInDate']}';
    var checkOutDate = '{$booking['CheckOutDate']}';
</script>";
    if (!$booking) {
        
        echo "Booking not found.";
        exit;
    }
}

else {
    echo "No booking ID provided.";
    exit;
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Modify Booking</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href='https://fonts.googleapis.com/css?family=Abel' rel='stylesheet'>
     <link rel ='stylesheet' href ='../style.css'>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</head>
<?php include '../view/nav.php'; ?>
<body class = 'modifyBooking'>
    
    <h1>Modify Booking</h1>
    <?php
                    if (isset($_SESSION['message'])) {
                    echo "<script type='text/javascript'>alert('" . $_SESSION['message'] . "');</script>";
                        unset($_SESSION['message']);
                    }
                    ?>
        

        <?php 
        $currentCheckInTime = date('H:i', strtotime($booking['CheckInTime']));
        $currentCheckOutTime = date('H:i', strtotime($booking['CheckOutTime']));
        
        ?>
        

               
        
                
              <label for="checkInDate">Check In Date:</label>
            <input type="text" id="checkInDate" name="checkInDate" placeholder="Select Check-In Date">

            <label for="checkOutDate">Check Out Date:</label>
            <input type="text" id="checkOutDate" name="checkOutDate" placeholder="Select Check-Out Date">

            <div class = 'summary' >
                <div id="cost-for-stay">Cost for Stay: $0.00</div>
                <div class="cleaning-fee">Cleaning Fee: $60.00</div>
                <div id="service-fee">Service Fee (15%): $0.00</div> 
                <div id="taxes">Taxes: $0.00</div>
                
                <div id ="total-cost">Total cost: $0.00</div>
                
                        </div>
                
               
                    <form action="updateBooking.php" method="post">
                        <input type="hidden" name="action" value="update">
                         <input type="hidden" name="bookingID" value="<?php echo htmlspecialchars($booking['bookingID']); ?>">
                        <input type="hidden" name="check_in_date" id="hidden-check-in-date">
                        <input type="hidden" name="check_out_date" id="hidden-check-out-date">
                        
                        <div class ="time">
                        <label for="check-in-time">Check In Time:</label>
                        <select id="checkin" name="check-in-time" style="width: 200px; height: 60px;">  <?php echo generate_time_options($currentCheckInTime); ?></select>
                        <label for="checkout-out-time">Check Out Time:</label>
                                <select id="checkout" name="check-out-time" style="width: 200px; height: 60px;"> <?php echo generate_time_options($currentCheckOutTime); ?>
                        </select>
                        </div>
                        <input type="hidden" name="total_cost" id="hidden-total-cost">
                        <input type="hidden" name="cost_for_stay" id="hidden-cost-for-stay">
                        <input type="hidden" name="cleaning_fee" id="hidden-cleaning-fee">
                        <input type="hidden" name="service_fee" id="hidden-service-fee">
                        <input type="hidden" name="taxes" id="hidden-taxes">
                        
                        
                        
                        
                        <button type="submit" class="btn btn-primary">Update Booking</button>
                        
                    </form>
            
            
                
                <form action="updateBooking.php" method="post" onsubmit="return confirm('Are you sure you want to delete this booking?');">
                            <input type="hidden" name="bookingID" value="<?php echo htmlspecialchars($booking['bookingID']); ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" style="background-color: red; color: white;">Delete Booking</button>
                           </form>   

                </div>
                    
                    
                    
                    
                </div>
                       

            
                
                
        
        
     
                    
 

  
            
            
            
            
            
            
            

                
            </form>
            
            
        </div>
        
        
        
        
        


            

        
    </body>
</html>


 <script>
document.addEventListener('DOMContentLoaded', function() {
    
    var checkInDatepicker, checkOutDatepicker;

    function updateTotalPrice() {
        var checkInDate = checkInDatepicker.selectedDates[0]; 
        var checkOutDate = checkOutDatepicker.selectedDates[0]; 


       if (checkInDate && checkOutDate) {
            var timeDiff = checkOutDate.getTime() - checkInDate.getTime();
            var daysDiff = timeDiff / (1000 * 3600 * 24);

             daysDiff = Math.ceil(daysDiff);

            if (daysDiff < 1) {
                alert('Check-out date must be after check-in date.');
                return;
            }

            var pricePerNight = <?php echo json_encode($booking['price']); ?>;
            var cleaningFee = <?php echo "60"; ?>;
            var serviceFeePercentage = 0.15; 
            var taxRate = 0.12; 

            var totalPriceForStay = daysDiff * pricePerNight;
            var serviceFee = totalPriceForStay * serviceFeePercentage;
            var taxes = (totalPriceForStay + serviceFee) * taxRate;
            var totalCost = totalPriceForStay + cleaningFee + serviceFee + taxes;

            
            document.getElementById('total-cost').textContent = 'Total cost: $' + totalCost.toFixed(2);
            document.getElementById('service-fee').textContent = 'Service Fee: $' + serviceFee.toFixed(2);
            document.getElementById('taxes').textContent = 'Taxes: $' + taxes.toFixed(2);
            document.getElementById('cost-for-stay').textContent = 'Cost for Stay: $' + totalPriceForStay.toFixed(2);

            
            document.getElementById('hidden-check-in-date').value = checkInDate.toISOString().split('T')[0];
            document.getElementById('hidden-check-out-date').value = checkOutDate.toISOString().split('T')[0];
            document.getElementById('hidden-total-cost').value = totalCost.toFixed(2);
            document.getElementById('hidden-cost-for-stay').value = totalPriceForStay.toFixed(2);
            document.getElementById('hidden-cleaning-fee').value = cleaningFee.toFixed(2);
            document.getElementById('hidden-service-fee').value = serviceFee.toFixed(2);
            document.getElementById('hidden-taxes').value = taxes.toFixed(2);
        }
    }


    

        
        var checkInDatepicker = flatpickr("#checkInDate", {
        
        dateFormat: "Y-m-d",
        minDate: "today",
        defaultDate: checkInDate,
        disable: unavailableDates.map(date => new Date(date)),
        onChange: function(selectedDates) {
            
             updateTotalPrice();
            
            
             const nextUnavailableDate = unavailableDates.find(date => new Date(date) > selectedDates[0]);
             if (nextUnavailableDate) {
                
                var maxDate = new Date(nextUnavailableDate);
                maxDate.setDate(maxDate.getDate() - 1);
                checkOutDatepicker.set("maxDate", maxDate);
            } else {
                
                checkOutDatepicker.set("maxDate", null);
            }
            
            
            if (checkOutDatepicker.selectedDates.length > 0 &&
                selectedDates[0] >= checkOutDatepicker.selectedDates[0]) {
                checkOutDatepicker.clear();
            }
            checkOutDatepicker.set("minDate", selectedDates[0].fp_incr(1));
        }
    });

    var checkOutDatepicker = flatpickr("#checkOutDate", {
    
        dateFormat: "Y-m-d",
        minDate: new Date().fp_incr(1),
        defaultDate: checkOutDate,
        disable: unavailableDates.map(date => new Date(date)),
        onChange: function(selectedDates) {
            
           
            
            updateTotalPrice();
        
    }
    });
        updateTotalPrice();
        });

</script>