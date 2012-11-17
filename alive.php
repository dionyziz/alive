<?php
/*
    ALiVE 3D Rendering Engine Version 4 for PHP5
    File: alive.php
    Description: Main ALiVE file
    Requires: PHP5
    Developer: Dionyziz
*/

function w_assert( $condition, $description = '' ) {
    if ( !$condition ) {
        throw new Exception( 'Assertion failed: ' . $description );
    }
}

define( 'ALIVE_PI' , 3.14159265358979 );

require_once 'matrix.php';
require_once 'vector.php';
require_once 'plane.php';
require_once 'transform/transform.php';
require_once 'object/object.php';
require_once 'driver/driver.php';
require_once 'world.php';

final class ALiVE_Texture {
}

final class ALiVE_Material {
}

final class ALiVE_Colour {
    private $mRed;
    private $mGreen;
    private $mBlue;
    
    public function ALiVE_Colour( $red , $green , $blue ) {
        $this->mRed   = $red;
        $this->mGreen = $green;
        $this->mBlue  = $blue;
    }
    public function R() {
        return $this->mRed;
    }
    public function G() {
        return $this->mGreen;
    }
    public function B() {
        return $this->mBlue;
    }
}

function ALiVE_Tween( $start , $end , $percentage ) {
    return $start + $percentage * ( $end - $start );
}

?>
