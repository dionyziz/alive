<?php
/*
    ALiVE 3D Rendering Engine Version 4 for PHP5
    File: transform/transform.php
    Description: Main transformations file; defines basic transformation routines and includes derivatives
    Developer: Dionyziz
*/

include 'transform/rotate.php';
include 'transform/scale.php';
include 'transform/translate.php';
include 'transform/project.php';

class ALiVE_Transformation {
    protected $mMatrix;
    
    public function ALiVE_Transformation( ALiVE_Matrix $matrix ) {
        w_assert( $matrix->N() == 4 && $matrix->M() == 4 );
        $this->mMatrix = $matrix;
    }
    public function Apply( $target ) {
        if ( $target instanceof ALiVE_Matrix ) {
            return ALiVE_Matrices_Multiply(
                $target, $this->ToMatrix()
            );
        }
        else if ( $target instanceof ALiVE_Vector ) {
            return new ALiVE_Vector(
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
                $ret[] = new ALiVE_Vector( $thismatrix );
            }
            return $ret;
        }

        throw new Exception( 'Invalid transformation application argument' );
    }
    protected function MakeMatrix() { // makes transformation matrix; overload me
        return;
    }
    public function ToMatrix() {
        if ( !isset( $this->mMatrix ) ) {
            $this->MakeMatrix(); // method of child
            if ( /* still? */ !isset( $this->mMatrix ) ) {
                throw new Exception( 'Attempt to convert 3D Transformation to Matrix without prior initialization' );
            }
        }
        return $this->mMatrix;
    }
}

final class ALiVE_Transformation_Neutral extends ALiVE_Transformation { // neutral (no-effect) transformation
    public function ALiVE_Transformation_Neutral() {
    }
    protected function MakeMatrix() {
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
        $this->mMatrix->Set( 3, 3, 1 );
    }
}

final class ALiVE_Camera extends ALiVE_Transformation { // combination of rotation, scaling, and translation
    private $mRotationVector;
    private $mScalingVector;
    private $mTranslationVector;
    private $mRotation;
    private $mScaling;
    private $mTranslation;
    
    public function SetRotation( $pitch , $yaw , $roll ) {
        $this->mRotationVector = new ALiVE_Vector( $pitch , $yaw , $roll );
    }
    public function SetPosition( $x , $y , $z ) {
        $this->mTranslationVector = new ALiVE_Vector( $x , $y , $z );
    }
    public function SetScaling( $x , $y , $z ) {
        $this->mScalingVector = new ALiVE_Vector( $x , $y , $z );
    }
    public function ALiVE_Camera() {
        $this->SetRotation( 0 , 0 , 0 );
        $this->SetPosition( 0 , 0 , 0 );
        $this->SetScaling(  1 , 1 , 1 );
    }
    protected function MakeMatrix() {
        $this->mRotation = new ALiVE_Rotation(
            $this->mRotationVector->X(),
            $this->mRotationVector->Y(),
            $this->mRotationVector->Z()
        );
        $this->mScaling = new ALiVE_Scaling(
            $this->mScalingVector->X(),
            $this->mScalingVector->Y(),
            $this->mScalingVector->Z()
        );
        $this->mTranslation = new ALiVE_Translation(
            $this->mTranslationVector->X(),
            $this->mTranslationVector->Y(),
            $this->mTranslationVector->Z()
        );
        $result = ALiVE_Transformations_Combine( 
                            $this->mScaling,
                            $this->mRotation,
                            $this->mTranslation
                         );
        $this->mMatrix = $result->ToMatrix();
    }
}

// order DOES matter, transformation $a is going to be applied BEFORE $b in the result when using
// combined transformations
function ALiVE_Transformations_Combine( /* ALiVE_Transformation $a1 , ALiVE_Transformation $a2, ... */ ) {
    $args = func_get_args();
    w_assert( count( $args ) >= 2 );
    w_assert( $args[ 0 ] instanceof ALiVE_Transformation );
    
    $ret = $args[ 0 ]->ToMatrix();
    for ( $i = 1 ; $i < count( $args ); ++$i ) {
        w_assert( $args[ $i ] instanceof ALiVE_Transformation );
        $ret = ALiVE_Matrices_Multiply( $ret , $args[ $i ]->ToMatrix() );
    }
    
    return new ALiVE_Transformation( $ret );
}

?>
