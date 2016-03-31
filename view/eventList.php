<?php
 
    $title = "List of Events";
    require '../view/headerInclude.php';
?>
<script src="../js/locationCompare.js"></script>
<script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js'></script>
    <section id="page-breadcrumb">
        <div class="vertical-center sun">
             <div class="container">
                <div class="row">
                    <div class="action">
                        <div class="col-sm-12">
                            <h1 class="title"><?php echo $title ?></h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
   </section>
    <!--/#action-->
    
    <section id="portfolio-information" class="padding-top">
        <div class="container">
            <div class="row">
                <!-- CG: Just Was Printing out for test purposes, feel free to delete whenver -->
                <?php 
                        
                        //print_r($_SESSION);
                       //print_r($_SESSION['preferred_username']);
                       //print_r($_SESSION['user_name']);
                        
                        ?>
                <h2> Welcome </h2>
                <table id="eventsTable" class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>Event Name</th>
                            <th>Building</th>
                            <th>Room</th>
                            <th>Date</th>
                            <th>Begin Time</th>
                            <th>End Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i=0; foreach ($events as $row){ $i++; ?><tr>
                            <td><a href="../controller/controller.php?action=EventDetails&amp;EventID=<?php echo $row['id'] ?>&amp;VenueID=<?php echo $row['location']?>"> <?php echo $row['name'] ?></a></td>
                            <td><?php echo $row['building_name'] ?></td>
                            <td><?php echo $row['room_number'] ?></td>
                            <td><?php echo toReadableDate($row['event_date']) ?></td>
                            <td><?php echo to12HourTime($row['start_time']) ?></td>
                            <td><?php echo to12HourTime($row['end_time']) ?></td>
                        </tr>
                        <?php } ?></tbody>
                </table>
            </div>
        </div>
    </section>
<?php
    require '../view/footerInclude.php';
?>