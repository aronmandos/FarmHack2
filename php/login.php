<?php
function login($email, $password, $mysqli) {
	if ($stmt = $mysqli->prepare ( "SELECT gebruikerID, wachtwoord FROM gebruiker WHERE email = ?
        LIMIT 1" )) {
		
		$stmt->bind_param ( 's', $email );
		$stmt->execute ();
		$stmt->store_result ();
		
		$stmt->bind_result ( $user_id, $db_password );
		$stmt->fetch ();
		
		$password = hash ( 'sha256', $password );
		if ($stmt->num_rows == 1) {
			
			if ($db_password == $password) {
				$user_browser = $_SERVER ['HTTP_USER_AGENT'];
				
				$_SESSION ['gebruikerID'] = $user_id;
				$_SESSION ['medewerker'] = false;
				
				$_SESSION ['email'] = $email;
				$_SESSION ['login_string'] = hash ( 'sha256', $password . $user_browser );

				return true;
			} else {
				
				return false;
			}
		} else if ($stmt = $mysqli->prepare ( "SELECT medewerkerID, wachtwoord FROM medewerker WHERE email = ?
        LIMIT 1" )) {
			$stmt->bind_param ( 's', $email ); 
			$stmt->execute (); 
			$stmt->store_result ();
			
			$stmt->bind_result ( $user_id, $db_password );
			$stmt->fetch ();
			
			if ($stmt->num_rows == 1) {
				if ($db_password == $password) {
					$user_browser = $_SERVER ['HTTP_USER_AGENT'];
					
					$_SESSION ['gebruikerID'] = $user_id;
					$_SESSION ['medewerker'] = true;
					
					$_SESSION ['email'] = $email;
					$_SESSION ['login_string'] = hash ( 'sha256', $password . $user_browser );
					return true;
				} else {
					
					return false;
				}
			} else {
				return false;
			}
		}
	}
}