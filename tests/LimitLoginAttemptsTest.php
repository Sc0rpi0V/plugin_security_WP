<?php

namespace \Security;

class WP_Error
{
    public function __construct()
    {
    }
}


class LimitLoginAttemptsTest extends \WP_Mock\Tools\TestCase {
    /**
     * Short description.
     *
     * @return void
     */
    public function setUp(): void {
        \WP_Mock::setUp();
    }

    /**
     * Short description.
     *
     * @return void
     */
    public function tearDown(): void {
        \WP_Mock::tearDown();
    }

    /**
     * Short description.
     */
    public function testcheck_attempted_login() {
        $LimitLoginAttempts = new \LimitLoginAttempts();
        $username           = hash( 'sha256', 'admin' );

        // on simule un tentative infructueuse par 3 fois.
        \WP_Mock::UserFunction( 'get_transient', array(
            'args'   => array( 'ns_attempted_login' ),
            'return' => array( 'tried' . $username => 3 ),
        ));

        // on simule get_option pour retourner le temps restant
        \WP_Mock::UserFunction('get_option', array(
            'args'   => array( '_transient_timeout_attempted_login' ),
            'return' => 3,
        ));

        $ret = $LimitLoginAttempts->check_attempted_login( 'admin', 'admin', '1234' );
        // $this->assertInstanceOf("\Security\WP_Error", $ret).

        // on simule un tentative infructueuse par 1 fois, donc on peut encore tester l'auth.
        \WP_Mock::UserFunction( 'get_transient', array(
            'args'   => array( 'ns_attempted_login' ),
            'return' => array( 'tried' . $username => 1 ),
        ));

        $ret = $LimitLoginAttempts->check_attempted_login( 'admin', 'admin', '1234' );

        $this->assertEquals( 'admin', $ret );
    }

    /**
     * Short description.
     */
    public function testlogin_failed() {
        $LimitLoginAttempts = new \LimitLoginAttempts();
        $username           = hash( 'sha256', 'admin' );

        // on simule un tentative infructueuse par 1 fois.
        \WP_Mock::UserFunction('get_transient', array(
            'times'  => 2,
            'args'   => array( 'ns_attempted_login' ),
            'return' => array( 'tried' . $username => 1 ),
        ));

        \WP_Mock::UserFunction( 'set_transient', array(
            'times' => 1,
            'args'  => array( 'ns_attempted_login', [ 'tried' . $username => 2], 1800 ),
        ));

        $ret = $LimitLoginAttempts->login_failed( 'admin' );
        $this->assertTrue( $ret );
    }
}
