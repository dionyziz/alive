<?php
/*
    ALiVE 3D Rendering Engine Version 4 for PHP5
    File: driver/gd.php
    Description: GD rendering driver
    Developer: Dionyziz
*/

final class ALiVE_Driver_GD extends ALiVE_Driver {
    private $mWidth;
    private $mHeight;
    private $mImg;
    private $mType;
    private $mBlack;
    private $mWhite;
    private $mAntialiasing;
    
    public function ALiVE_Driver_GD() {
        $this->SetSize( 800 , 600 ); // default size
        $this->SetImageType( 'png' ); // default type
        $this->SetAntialias( true );
    }
    public function SetAntialias( $setting ) {
        w_assert( is_bool( $setting ) );
        $this->mAntialiasing = $setting;
    }
    public function SetSize( $width , $height ) {
        w_assert( is_int( $width ) );
        w_assert( is_int( $height ) );
        w_assert( $width > 0 );
        w_assert( $height > 0 );
        $this->mWidth = $width;
        $this->mHeight = $height;
    }
    public function SetImageType( $type ) {
        w_assert( in_array( $type , array(
            'png', 'jpeg', 'gif'
        ) ) );
        $this->mType = 'image/' . $type;
    }
    public function Initialize() {
        $this->mImg = imagecreatetruecolor( $this->mWidth , $this->mHeight );
        imageantialias( $this->mImg , $this->mAntialiasing );
        $this->mBlack = imagecolorallocate( $this->mImg , 0 , 0 , 0 );
        $this->mWhite = imagecolorallocate( $this->mImg , 255 , 255 , 255 );
        imagefill( $this->mImg , 0 , 0 , $this->mWhite );
    }
    public function Finalize() {
        header( 'Content-type: ' . $this->mType );
        switch ( $this->mType ) {
            case 'image/png':
                imagepng( $this->mImg );
                break;
            case 'image/jpeg':
                imagejpeg( $this->mImg );
                break;
            case 'image/gif':
                imagegif( $this->mImg );
                break;
            default:
                w_assert( false );
        }
    }
    private function TransformPoint( ALiVE_2D_Vector $point ) {
        // 0       W-1
        // . . . . .
        //-1   0  +1
        // (W - 1) / 2 + x * ((W - 1) / 2)
        $x = ( $this->mWidth  - 1 ) / 2 + $point->X() * ( ( $this->mWidth  - 1 ) / 2 );
        $y = ( $this->mHeight - 1 ) / 2 + $point->Y() * ( ( $this->mHeight - 1 ) / 2 );
        return new ALiVE_2D_Vector( $x , $y );
    }
    public function DrawPoint( ALiVE_2D_Vector $point , ALiVE_Colour $colour ) {
        $finalPoint = $this->TransformPoint( $point );
        // TODO
    }
    public function DrawLine( ALiVE_2D_Vector $a , ALiVE_2D_Vector $b , ALiVE_Colour $colour ) {
        /* echo $a->X() / $this->mWidth , ' ' , $a->Y() / $this->mHeight , ' ' , $b->X() / $this->mWidth , ' ' , $b->Y() / $this->mHeight; */
        $finalA = $this->TransformPoint( $a );
        $finalB = $this->TransformPoint( $b );
        imageline( $this->mImg , 
            $finalA->X(), 
            $finalA->Y(), 
            $finalB->X(), 
            $finalB->Y(), 
            $this->mBlack
        );
    }
    public function DrawPolygon( ALiVE_2D_Vector $a , ALiVE_2D_Vector $b , ALiVE_2D_Vector $c , ALiVE_Colour $colour ) {
        /*
        $finalA = $this->TransformPoint( $a );
        $finalB = $this->TransformPoint( $b );
        $finalC = $this->TransformPoint( $c );
        imagepolygon(
            $this->mImg,
            $finalA->X(),
            $finalA->Y(),
            $finalB->X(),
            $finalB->Y(),
            $finalC->X(),
            $finalC->Y(),
            $this->mBlack
        );
        */ // TODO
        $this->DrawLine( $a , $b , $colour );
        $this->DrawLine( $b , $c , $colour );
        $this->DrawLine( $c , $a , $colour );
    }
}

?>
