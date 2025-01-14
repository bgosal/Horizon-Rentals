<?php

session_start();


$base_url = "http://localhost/COMP351_Project/"; 

?>



<div class ="navbar">
                 <li class="web_logo"><a href="<?php echo $base_url; ?>"><img src="<?php echo $base_url; ?>images/web_logo2.png" alt="Logo" style="height:70px;"></a></li>
                <nav>
                   
                <ul>
                    
                    
                    <?php
                    
                    
                    if (isset($_SESSION['user']) && $_SESSION['user'] == 'admin'){
                        echo '<li><a href="'.$base_url.'/account_management/account_management.php">My Account</a></li>';
                        echo '<li><a href="'.$base_url.'>User Management</a></li>';
                        echo '<li><a href="'.$base_url.'>Bookings</a></li>';
                        echo '<li><a href="'.$base_url.'booking/myBookings.php">My Bookings</a></li>';
                        echo '<li><a href="'.$base_url.'rental_postings/post_rental.php">Post A Rental</a></li>';
                        echo '<li><a href="'.$base_url.'registration/logout.php">Sign Out</a></li>';
                        
                    }    
                    
                    else if (isset($_SESSION['user'])) {
                            echo '<li><a href=" '.$base_url.'account_management/account_management.php">My Account</a></li>';
                            echo '<li><a href="'.$base_url.'rental_postings/post_rental.php">Post A Rental</a></li>';
                            echo '<li><a href="'.$base_url.'booking/myBookings.php">My Bookings</a></li>';
                            echo '<li><a href="'.$base_url.'booking/clientBookings.php">Client Bookings</a></li>';
                            echo '<li><a href="'.$base_url.'rental_postings/myPosts.php">My Listings</a></li>';
                            echo '<li><a href="'.$base_url.'registration/logout.php">Sign Out</a></li>';
                        }
                        
                    
                     else {
                            echo '<li><a href="'.$base_url.'registration/registration.php">Register</a></li>';
                            echo '<li><a href="'.$base_url.'registration/login.php">Login</a></li>';
                            

                            
                            echo '<li><a href="'.$base_url.'registration/login.php">My Account</a></li>';  
                            echo '<li><a href="'.$base_url.'registration/login.php">My Bookings</a></li>';
                            echo '<li><a href="'.$base_url.'registration/login.php">Post A Rental</a></li>';
                            echo '<li><a href="'.$base_url.'registration/login.php">Contact Us</a></li>';
                        }
                   
                             
                    ?>
                 </ul>
                    
                    </nav>
            
                
            </div>