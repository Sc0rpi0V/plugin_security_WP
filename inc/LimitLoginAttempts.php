<?php

use \Security\NsFormValidator;

/**
 * CLASS LimitLoginAttempts
 * Check attempted login and track username tested in wp-login
 */
class LimitLoginAttempts {

	/**
	 * Lock login attempts of failed login limit is reached
	 *
	 * @param mixed $user user.
	 * @param mixed $username username.
	 */
	public function check_attempted_login( $user, $username ) {
		$username = hash( 'sha256', $username );
		$datas    = get_transient( NsFormValidator::$transient_name );
		if ( $datas !== null ) {
			if ( $datas[ 'tried' . $username ] >= NsFormValidator::$failed_login_limit ) {
				$until = get_option( '_transient_timeout_' . NsFormValidator::$transient_name );
				$time  = $this->when( $until );
				// Display error message to the user when limit is reached.
				wp_die( sprintf( __( '%1$sERROR%2$s: You have reached the authentication limit, please try again after %3$s.', 'security' ), '<strong>', '</strong>', $time ) );
			}
		}
		return $user;
	}

	/**
	 * Add transient
	 *
	 * @param mixed $username username.
	 */
	public function login_failed( $username ) {
		$username = hash( 'sha256', $username );
		if ( get_transient( NsFormValidator::$transient_name ) ) {
			$datas = get_transient( NsFormValidator::$transient_name );
			if ( array_key_exists( 'tried' . $username, $datas ) ) {
				++$datas[ 'tried' . $username ];
				if ( $datas[ 'tried' . $username ] <= NsFormValidator::$failed_login_limit ) {
					set_transient( NsFormValidator::$transient_name, $datas, NsFormValidator::$lockout_duration );
				}
			} else {
				$datas = array(
					'tried' . $username => 1,
				);
				set_transient( NsFormValidator::$transient_name, $datas, NsFormValidator::$lockout_duration );
			}
		}

		return true;
	}


	/**
	 * Return difference between 2 given dates
	 *
	 * @param  int $time   Date as Unix timestamp.
	 * @return string           Return string
	 */
	private function when( $time ) {
		if ( ! $time ) {
			return;
		}
		$right_now = time();
		$diff      = abs( $right_now - $time );
		$second    = 1;
		$minute    = $second * 60;
		$hour      = $minute * 60;
		$day       = $hour * 24;
		if ( $diff < $minute ) {
			return floor( $diff / $second ) . ' seconde(s)';
		}
		if ( $diff < $minute * 2 ) {
			return 'Il y a environ 1 minute';
		}
		if ( $diff < $hour ) {
			return floor( $diff / $minute ) . ' minute(s)';
		}
		if ( $diff < $hour * 2 ) {
			return 'Il y a environ 1 heure';
		}
		return floor( $diff / $hour ) . ' moment';
	}
}
