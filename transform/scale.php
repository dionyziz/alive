<?php
/*
    ALiVE 3D Rendering Engine Version 4 for PHP5
    File: transform/scale.php
    Description: Handling scaling in 3D space
    Developer: Dionyziz
*/

final class ALiVE_Scaling extends ALiVE_Transformation {
    private $mVector; // scaling factors
    
    public function ALiVE_Scaling( $x, $y, $z ) {
        $this->mVector = new ALiVE_Vector( $x, $y, $z );
    }
    protected function MakeMatrix() {
        /*
            | X 0 0 0 |
            | 0 Y 0 0 |
            | 0 0 Z 0 |
            | 0 0 0 1 |
        */
        $this->mMatrix = new ALiVE_Matrix( 4, 4 );
        $this->mMatrix->Set( 0 , 0 , $this->mVector->X() );
        $this->mMatrix->Set( 1 , 1 , $this->mVector->Y() );
        $this->mMatrix->Set( 2 , 2 , $this->mVector->Z() );
        $this->mMatrix->Set( 3 , 3 , 1                   );
    }
}

?>
