<?php
   if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/*
 * ME Testing the change log system of git, and did the repo change locations. So One folder fits all
 */
global $venueLocation;
        function getDBConnection() {
		$dataSetName = 'mysql:host=localhost; dbname=cis411_EventRegistration';
		$username = 's_cgillis';
		$password = 'Gk$98pbw';

		try {
			$dataBase = new PDO($dataSetName, $username, $password);
		} catch (PDOException $e) {
			$errorMessage = $e->getMessage();
                        echo $errorMessage;
			include '../view/404.php';
			die;
		}
		return $dataBase;
	}
	
        function getClassList() {
		try {
			$dataBase = getDBConnection();
			$query = "SELECT
                                    class.class_number,
                                    class.class_section,
                                    class.class_name,
                                    class.semester_offered,
                                    user.name
                                    user.email
                                FROM 
                                    class INNER JOIN user ON class.instructor_id = user.id ";
			$statement = $dataBase->prepare($query);
			$statement->execute();
			$results = $statement->fetchAll();
			$statement->closeCursor();
			return $results;           // Assoc Array of Rows
		} catch (PDOException $e) {
			$errorMessage = $e->getMessage();
                        echo $errorMessage;
			include '../view/404.php';
			die;
		}		
	}
        
        function getEventDetails($eventid){
            try{
                $dataBase = getDBConnection();
                $query = "SELECT \n"
                        . "event.name, \n"
                        . "event.start_time, \n"
                        . "event.end_time, \n"
                        . "event.event_date, \n"
                        . "event.description, \n "
                        . "venue.building_name, \n"
                        . "venue.room_number \n"
                        . "FROM \n"
                        . "event INNER JOIN venue ON event.venue_id = venue.id  \n"
                        . "WHERE event.id = :id";
                $statement = $dataBase->prepare($query);
                $statement->bindValue(':id', $eventid);
                $statement->execute();
                $results = $statement->fetch();
                $statement->closeCursor();
                return $results;           // Assoc Array of Rows
                
            } catch (PDOException $e) {
			$errorMessage = $e->getMessage();
                        echo $errorMessage;
			include '../view/404.php';
			die;

            }
        }
        function getEligibleClasses($eventid){
                $dataBase = getDBConnection();
                $query = "SELECT \n"
                            . " class.class_name, \n"
                            . " class.class_number, \n"
                            . " class.class_section,\n"
                            . " user.name\n"
                            . "FROM \n"
                            . " class \n"
                            . " INNER JOIN user ON class.instructor_id = user.id \n"
                            . "WHERE \n"
                            . " class.event_id = :id";
                $statement = $dataBase->prepare($query);
                $statement->bindValue(':id', $eventid);
                $statement->execute();
                $results = $statement->fetchAll();
                $statement->closeCursor();
                return $results; 
        }
        
        function getEventList(){
            try{
                $dataBase = getDBConnection();
                $query = "SELECT \n"
                            . " event.id, \n"
                            . " event.name, \n"
                            . " event.start_time, \n"
                            . " event.end_time, \n"
                            . " event.event_date, \n"
                            . " venue.building_name, \n"
                            . " venue.room_number, venue.id AS location \n"
                            . "FROM \n"
                            . " event \n"
                            . " INNER JOIN venue ON event.venue_id = venue.id \n"
                            . "WHERE \n"
                            . " event.event_date >= CURDATE() \n"
                            . "ORDER BY \n"
                            . " event.event_date";
                $statement = $dataBase->prepare($query);
                $statement->execute();
                $result = $statement->fetchAll();  // Should be 0 or 1 row
                $statement->closeCursor();
                return $result;			 // False if 0 rows
            } catch (PDOException $e) {
                $errorMessage = $ex->getMessage();
                echo $errorMessage;
                include '../view/404.php';
                die;
            }
        }
        
        function locationForEvent($venue){
            global $venueLocation;
            $venueLocation = $venue;
        }
        function getLocationForEvent(){
            global $venueLocation;
            $restrictedVenue = $venueLocation;
            print_r($restrictedVenue);
            return $restrictedVenue;
        }
        
        function locationCheckBecker(){
          try{ 
                $dataBase = getDBConnection();
                $sql = "select venue.id,venue.building_name,venue.room_number,venue.corner1_lat,venue.corner1_lng,venue.corner2_lat,venue.corner2_lng,venue.corner3_lat,venue.corner3_lng,"
                . "venue.corner4_lat,venue.corner4_lng FROM venue WHERE id = :location ";      
                $statement = $dataBase->prepare($sql);
                $statement->bindValue(':location', $_SESSION['venue']);
                $statement->execute();
                $results = $statement->fetch();
                $statement->closeCursor();
                return $results;
            } catch (Exception $ex) {
                $errorMessage = $ex->getMessage();
                        echo $errorMessage;
			include '../view/404.php';
			die;
            }
        }
       
        function checkIfStudentExsists($email){
            try{
            $db = getDBConnection();
            $query = 'SELECT user.id FROM user WHERE user.email = :email';
            $statement = $db -> prepare ($query);
            $statement->bindValue (':email', $email);
            $statement ->execute();
            $results = $statement->fetch();
            $statement->closeCursor();
            return $results;
            }catch (Exception $ex) {
                $errorMessage = $ex->getMessage();
                        echo $errorMessage;
			include '../view/404.php';
			die;
            }
        }
        function insertStudent($username, $email)
        {
            $exsist = checkIfStudentExsists($email);
            if($exsist != ''){
                return $exsist;    
            }else
            {
                $db = getDBConnection();
                $query = 'INSERT INTO user (name , email, is_student)'
                        . 'VALUES (:name, :email, Y)';
                $statement = $db -> prepare ($query);
                $statement->bindValue (':name',$username);
                $statement->bindValue (':email', $email);
                $success  = $statement ->execute();
                $statement->closeCursor();
                if ($success) {
                            return $db->lastInsertId(); // Get generated StoryID
                    } else {
                            logSQLError($statement->errorInfo());  // Log error to debug
                    }		
            }
        
        }
        
        function addToClassList($class_number, $class_section, $event_id, $student_id){
            $db = getDBConnection();
            $query = 'INSERT INTO extra_credit_list'
                    . 'VALUES (:class_num, :class_section, :event_id, :student_id)';
            $statement = $db -> prepare ($query);
            $statement->bindValue (':class_num',$class_number);
            $statement->bindValue (':class_section', $class_section);
            $statement->bindValue (':event_id', $event_id);
            $statement->bindValue (':student_id', $student_id);
            $success  = $statement ->execute();
            $statement->closeCursor();
            if ($success) {
			return $db->lastInsertId(); // Get generated StoryID
		} else {
			logSQLError($statement->errorInfo());  // Log error to debug
		}		
        }
        
	function logSQLError($errorInfo) {
		$errorMessage = $errorInfo[2];
                include '../view/404.php';
		die;
        }          
?>