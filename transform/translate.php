<?php
/*
    ALiVE 3D Rendering Engine Version 4 for PHP5
    File: transform/translate.php
    Description: Handling translation in 3D space
    Developer: Dionyziz
*/

final class ALiVE_Translation extends ALiVE_Transformation {
    private $mVector; // translation offsets
    
    public function ALiVE_Translation( $x, $y, $z ) {
        $this->mVector = new ALiVE_Vector( $x, $y, $z );
    }
    protected function MakeMatrix() {
        /*
            | 1 0 0 0 |
            | 0 1 0 0 |
            | 0 0 1 0 |
            | X Y Z 1 |
             
             <x y z w>
        */
        $this->mMatrix = new ALiVE_Matrix( 4, 4 );
        $this->mMatrix->Set( 0 , 0 , 1 );
        $this->mMatrix->Set( 1 , 1 , 1 );
        $this->mMatrix->Set( 2 , 2 , 1 );

        $this->mMatrix->Set( 3 , 0 , $this->mVector->X() );
        $this->mMatrix->Set( 3 , 1 , $this->mVector->Y() );
        $this->mMatrix->Set( 3 , 2 , $this->mVector->Z() );
        $this->mMatrix->Set( 3 , 3 , 1                   );
    }
}

?>
