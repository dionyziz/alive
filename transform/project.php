<?php
/*
    ALiVE 3D Rendering Engine Version 4 for PHP5
    File: transform/project.php
    Description: Projections file; allows creation of basic projection matrices
    Developer: Dionyziz
*/

class ALiVE_Projection extends ALiVE_Transformation {
    public function Apply( $target ) {
        if ( $target instanceof ALiVE_Matrix ) {
            return ALiVE_Matrices_Multiply(
                $target, $this->ToMatrix()
            );
        }
        else if ( $target instanceof ALiVE_Vector ) {
            return new ALiVE_2D_Vector(
                $this->Apply( $target->ToMatrix() )
            );
        }
        else if ( is_array( $target ) ) {
            foreach ( $target as $vector ) {
                w_assert( $vector instanceof ALiVE_Vector );
            }
            $result = $this->Apply(
                ALiVE_Vectors_ToMatrix( $target )
            );
            $ret = array();
            for ( $i = 0; $i < $result->M(); ++$i ) {
                $thismatrix = new ALiVE_Matrix( 1, 4 );
                $thismatrix->Set( 0 , 0 , $result->Get( $i , 0 ) );
                $thismatrix->Set( 0 , 1 , $result->Get( $i , 1 ) );
                $thismatrix->Set( 0 , 2 , $result->Get( $i , 2 ) );
                $thismatrix->Set( 0 , 3 , $result->Get( $i , 3 ) );
                $ret[] = new ALiVE_2D_Vector( $thismatrix );
            }
            return $ret;
        }
        throw new Exception( 'Invalid projection application argument' );
    }
}

/*
    Projections, given a 4D vector in the form of ( x, y, z, w )
    should result in a 4D vector in the form of ( x', y', z', w' )
    which should correspond to the actual 2D coordinates on which 
    a point should be drawn:
    
    ( FinalX, FinalY ) = ( x'/w' , y'/w' )
    
    where FinalX and FinalY correspond to screen coordinates with 
    ( 0,  0) being the centre of the screen and
    (-1, -1) being the bottom-left corner and
    ( 1,  1) being the top-right corner

(-1, 1)  ,______________________, (1, 1)
         |                      |
         |  . (FinalX, FinalY)  |
         |          . (0, 0)    |    <-- 2D Projection (screen)
         |                      |
         |______________________|
(-1, -1) `                      ` (1, -1)

*/

// this equivalent to creating an empty class multiply inheriting from ALiVE_Projection and ALiVE_Transformation_Neutral
final class ALiVE_Projection_Isometric extends ALiVE_Projection {
    public function ALiVE_Projection_Isometric() {
    }
    protected function MakeMatrix() { // no change whatsoever
        /*
            | 1 0 0 0 |
            | 0 1 0 0 |
            | 0 0 1 0 |
            | 0 0 0 1 |
        */
        $this->mMatrix = new ALiVE_Matrix( 4, 4 );
        $this->mMatrix->Set( 0, 0, 1 );
        $this->mMatrix->Set( 1, 1, 1 );
        $this->mMatrix->Set( 2, 2, 1 );
        $this->mMatrix->Set( 3, 3, 1 ); // perspective denominator of 1 everywhere
    }
}

// TODO: Z-Buffer
final class ALiVE_Projection_Perspective extends ALiVE_Projection {
    //
    //
    //                            ^
    private $mMu; // M = angle(0, 3, 1) in radians; see diagram below for the points
    //
    //
    //                            ^
    private $mNu; // N = angle(0, 3, 2) in radians; see diagram below for the points
    private $mNuRatio; // mNu = mMu * mNuRatio
    /*
----------------------------------------------------------------------------------------------
         =="The world before perspective projection"==
                            .            .                                                * 3D
                            .         ( B ) <-- Back clipping
                            .          .
                            .         .
                            |        /
                            |       / <-- Z-axis
                  Top edge  |      /
                      |     |     /
                      |     |    /
                      v     |   /   
                 ___________2_____________
                 |          | /          | <-- Right edge
                 |          |/           |
...______________|__________0____________1_____________________________________... <-- X-axis
                 |         /| (0, 0)     |
                 |        / |            |
                 |_______/__|____________| <-- Projection Plane ("Screen")
                        /   |
Front-clipping -->   ( F )  |
                      3 <-- Camera
                     /      |
                    /       |
                   /        | <-- Y-axis
                  .         .
                 .          .
                .           .
----------------------------------------------------------------------------------------------
        =="The world after perspective projection"==
                            .                                                             + 2D
                            .
                            .
                            | <-- Y-axis
                            |
        (-1, 1)  ,__________|___________, (1, 1)
                 |          |           |
                 |          |           |
      ...________|__________0`__________| _________... <-- X-axis   
                 |          | (0, 0)    |
                 |          |           |
                 |__________|___________| <-- 2D Projection (Screen)
        (-1, -1) `          |           ` (1, -1)
                            |
                            |
                            .
                            .
                            .
----------------------------------------------------------------------------------------------
    */
    private $mPhi; // F (front clipping)
    private $mBeta; // B (back clipping)
    private $mZBuffer; // accuracy of the Z-buffer

    public function ALiVE_Projection_Perspective() {
        // let's set some defaults
        $this->SetMu();
        $this->SetNuRatio();
        $this->mPhi = 1;
        $this->mBeta = 10000;
    }
    public function SetMu( $mu = 0.785398 ) {
        $this->mMu = $mu;
    }
    public function SetNuRatio( $nuratio = 1.3333 ) {
        $this->mNuRatio = $nuratio;
    }
    protected function MakeMatrix() {
        /*
            | mu 0            0                      0           | // x': X distortion
            | 0  nu           0                      0           | // y': Y distortion
            | 0  0  (Beta+Phi)/(Beta-Phi)   -2BetaPhi/(Beta-Phi) | // z': Z-buffer coordinates
            | 0  0            1                      0           | // w': crucial in perspective distortion; depending on z,
                                                                   //     it determines how FinalX/FinalY change as z changes
        */
        $this->mNu = $this->mMu * $this->mNuRatio;
        $this->mMatrix = new ALiVE_Matrix( 4 , 4 );
        $this->mMatrix->Set( 0 , 0 , $this->mMu );
        $this->mMatrix->Set( 1 , 1 , $this->mNu );
        $this->mMatrix->Set( 2 , 2 , ( $this->mBeta + $this->mPhi ) / ( $this->mBeta - $this->mPhi ) );
        $this->mMatrix->Set( 2 , 3 , -( $this->mBeta * $this->mPhi ) / ( $this->mBeta - $this->mPhi ) );
        $this->mMatrix->Set( 3 , 2 , 1 );
    }
}

?>
