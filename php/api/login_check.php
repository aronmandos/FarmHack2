<?php
session_start ();
function login_check($mysqli) {
	// Check if all session variables are set
	if (isset ( $_SESSION ['gebruikerID'], $_SESSION ['email'], $_SESSION ['login_string'] )) {
		
		$gebruikerID = $_SESSION ['gebruikerID'];
		$login_string = $_SESSION ['login_string'];
		$email = $_SESSION ['email'];
		
		// Get the user-agent string of the user.
		$user_browser = $_SERVER ['HTTP_USER_AGENT'];
		
		if ($stmt = $mysqli->prepare ( "SELECT wachtwoord
                                      FROM gebruiker
                                      WHERE gebruikerID = ? LIMIT 1" )) {
			// Bind "$gebruikerID" to parameter.
			$stmt->bind_param ( 'i', $gebruikerID );
			$stmt->execute (); // Execute the prepared query.
			$stmt->store_result ();
			
			if ($stmt->num_rows == 1) {
				// If the user exists get variables from result.
				$stmt->bind_result ( $password );
				$stmt->fetch ();
				$login_check = hash ( 'sha256', $password . $user_browser );
				
				if ($login_check == $login_string) {
					// Logged In!!!!
					return true;
				} else {
					// Not logged in
					return false;
				}
			} else {
				// Not logged in
				return false;
			}
		} else {
			// Not logged in
			return false;
		}
	} else {
		// Not logged in
		return false;
	}
}
function isMedewerker() {
	return ($_SESSION ['medewerker'] == true);
}
?>