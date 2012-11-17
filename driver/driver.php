<?php
/*
    ALiVE 3D Rendering Engine Version 4 for PHP5
    File: driver/driver.php
    Description: Master driver file
    Developer: Dionyziz
*/

require_once 'driver/ascii.php';
require_once 'driver/gd.php';

abstract class ALiVE_Driver {
    abstract public function DrawPoint( ALiVE_2D_Vector $point , ALiVE_Colour $colour );
    abstract public function DrawLine( ALiVE_2D_Vector $a , ALiVE_2D_Vector $b , ALiVE_Colour $colour );
    public function Render( ALiVE_Universe $world ) { // do NOT overload me
        $black = new ALiVe_Colour( 0 , 0 , 0 );
        $this->Initialize(); // virtual
        $world->Transform();
        $polygons = $world->GetPolygons();
        $points = $world->GetTransformedPoints();
        foreach ( $polygons as $polygon ) {
            w_assert( isset( $polygon[ 0 ] ) );
            w_assert( isset( $polygon[ 1 ] ) );
            w_assert( isset( $polygon[ 2 ] ) );
            w_assert( isset( $points[ $polygon[ 0 ] ] ) );
            w_assert( isset( $points[ $polygon[ 1 ] ] ) );
            w_assert( isset( $points[ $polygon[ 2 ] ] ) );
            $a = $points[ $polygon[ 0 ] ];
            $b = $points[ $polygon[ 1 ] ];
            $c = $points[ $polygon[ 2 ] ];
            // TODO: Don't draw polygons to +oo / -oo 
            //       (Z-buffer it)
            $this->DrawPolygon( $a , $b , $c , $black );
        }
        $this->Finalize(); // virtual
    }
    public function DrawPolygon( ALiVE_2D_Vector $a , ALiVE_2D_Vector $b , ALiVE_2D_Vector $c , ALiVE_Colour $colour ) { // overload me
        $this->DrawLine( $a , $b , $colour );
        $this->DrawLine( $b , $c , $colour );
        $this->DrawLine( $c , $a , $colour );
    }
    public function Initialize() { // overload me
    }
    public function Finalize() { // overload me
    }
    public function SetSize( $width , $height ) { // overload me
    }
}

?>
