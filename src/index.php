<?php
session_start();
?>




<html>
    <head>
        <meta charset="UTF-8">
        <title> Horizon Rentals </title>
        <link href='https://fonts.googleapis.com/css?family=Abel' rel='stylesheet'>
         <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel ='stylesheet'>
         <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
           <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        
        <link rel ='stylesheet' href ='style.css'>
        
    </head>
    <body class = 'main'>
        <?php include 'view/nav.php'; ?>
         
        <div class ='banner'>
            
            
            
            <div class ='logo'>
                <img src ='images/logo1.png' class= "logo">
            </div>
            
            
            
            
            
            
            
            
                    <div class="search-bar">  
                        <form action="rental_postings/search_results.php" method="get"> 
                        <label for="city">Enter city name:</label>
                        <input type="text" name="city" placeholder="Enter city name...">
                        
                        <label for="checkin">Check-in Date:</label>
                        <input type="text" name="checkin" id="checkin" placeholder="Check-in Date">
                        
                        <label for="checkout">Check-out Date:</label>
                        <input type="text" name="checkout" id="checkout" placeholder="Check-out Date">
                        
                        
                        <button type="submit" name="submit">Search</button>
                   
                        </form>

             </div>

            
            <div class = "footer" ><?php include 'view/footer.php'; ?>     </div>
        
            
             </div>
            
        
             
        
        
            
            
        
            
        
            
    
   
        
        
       
    </body>
  
<script>
    flatpickr("#checkin", {
        dateFormat: "Y-m-d",
        minDate: "today",
        onChange: function(selectedDates, dateStr, instance) {
            var checkOutPicker = flatpickr("#checkout", {minDate: "today"});
        var minCheckOutDate = selectedDates[0].fp_incr(1); 
        checkOutPicker.set('minDate', minCheckOutDate);

        
        var currentCheckOutDate = checkOutPicker.selectedDates[0];
        if (currentCheckOutDate && currentCheckOutDate < minCheckOutDate) {
            checkOutPicker.setDate(minCheckOutDate);
        }
        }
    });

    flatpickr("#checkout", {
        dateFormat: "Y-m-d",
        minDate: new Date().fp_incr(1)
    });
</script>
</html>





