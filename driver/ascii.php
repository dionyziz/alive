<?php
/*
    ALiVE 3D Rendering Engine Version 4 for PHP5
    File: driver/ascii.php
    Description: ASCII rendering driver
    Developer: Dionyziz
*/

final class ALiVE_Driver_ASCII extends ALiVE_Driver {
    private $mScreen;
    private $mWidth; // in number of columns
    private $mHeight; // in number of rows
    
    public function ALiVE_Driver_ASCII() {
        $this->SetSize( 100 , 50 );
    }
    public function SetSize( $width , $height ) {
        w_assert( is_int( $width ) );
        w_assert( is_int( $height ) );
        w_assert( $width > 0 );
        w_assert( $height > 0 );
        $this->mWidth = $width;
        $this->mHeight = $height;
    }
    private function TransformPoint( ALiVE_2D_Vector $point ) {
        return new ALiVE_2D_Vector(
            ( $this->mWidth  - 1 ) / 2 + $point->X() * ( ( $this->mWidth  - 1 ) / 2 ),
            ( $this->mHeight - 1 ) / 2 + $point->Y() * ( ( $this->mHeight - 1 ) / 2 )
        );
    }
    public function DrawPoint( ALiVE_2D_Vector $point , ALiVE_Colour $colour ) {
        $this->FillPoint( $point , '.' );
    }
    private function FillPoint( ALiVE_2D_Vector $point , $char = '.' ) {
        $point = $this->TransformPoint( $point );
        $this->mScreen[ $point->X() ][ $point->Y() ] = $char;
    }
    public function DrawLine( ALiVE_2D_Vector $a , ALiVE_2D_Vector $b , ALiVE_Colour $colour ) {
        for ( $tween = 0 ; $tween < 1 ; $tween += 0.09 ) {
            $x = ALiVE_Tween( $a->X() , $b->X() , $tween );
            $y = ALiVE_Tween( $a->Y() , $b->Y() , $tween );
            if (isset($prevx) && isset($prevy)) {
                $v = new ALiVE_2D_Vector( $x , $y );
                if ( $x - $prevx < 0.01 ) {
                    $this->FillPoint( $v , '|' );
                }
                else if ( $y - $prevy < 0.01 ) {
                    $this->FillPoint( $v , '_' );
                }
                else if ( $y - $prevy - $x + $prevx < 0.01 ) {
                    $this->FillPoint( $v , '\\' );
                }
                else if ( $y - $prevy + $x - $prevx < 0.01 ) {
                    $this->FillPoint( $v , '/' );
                }
                else {
                    $this->FillPoint( $v , '.' );
                }
            }
            $prevx = $x;
            $prevy = $y;
        }
    }
    public function Finalize() {
        header( 'Content-type: text/plain' );
        for ( $y = 0 ; $y < $this->mHeight ; ++$y ) {
            for ( $x = 0 ; $x < $this->mWidth ; ++$x ) {
                if ( isset( $this->mScreen[ $x ][ $y ] ) ) {
                    echo $this->mScreen[ $x ][ $y ];
                }
                else {
                    echo ' ';
                }
            }
            echo "\n";
        }
    }
}

?>
