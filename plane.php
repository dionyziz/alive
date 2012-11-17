<?php
/*
    ALiVE 3D Rendering Engine Version 4 for PHP5
    File: plane.php
    Description: Plane geometry!
    Developer: Dionyziz
*/

final class ALiVE_Plane {
    private $mPoint;
    private $mNormal;
    private $mD;
    
    public function ALiVE_Plane( $arg0, $arg1, $arg2 = null ) {
        if ( $arg2 === null ) {
            // new ALiVE_Plane( $point, $normal )
            w_assert( $arg0 instanceof ALiVE_Vector );
            w_assert( $arg1 instanceof ALiVE_Unit_Vector );
            $this->mPoint = $arg0;
            $this->mNormal = $arg1;
        }
        else {
            // new ALiVE_Plane( $point1, $point2, $point3 );
            w_assert( $arg0 instanceof ALiVE_Vector );
            w_assert( $arg1 instanceof ALiVE_Vector );
            w_assert( $arg2 instanceof ALiVE_Vector );
            
            // make sure we have three distinct points
            w_assert( $arg0 != $arg1 );
            w_assert( $arg1 != $arg2 );
            w_assert( $arg0 != $arg2 );
            
            // compute the normal vector
            $this->mNormal = ALiVE_Vectors_CrossProduct(
                ALiVE_Vectors_Subtract( $arg1, $arg0 ),
                ALiVE_Vectors_Subtract( $arg2, $arg0 )
            );
            $this->mPoint = $arg0;
        }
    }
    public function Contains( ALiVE_Vector $point ) {
        // check if $point is on plane
        $planeline = ALiVE_Vectors_Subtract(
            $point, $this->mPoint
        );
        return ALiVE_Vectors_InnerProduct( $planeline , $this->mNormal ) == 0;
    }
    public function Distance( ALiVE_Vector $point ) {
        // calculate the distance of $point from plane
        return abs(
              $this->mNormal->X() * $point->X() 
            + $this->mNormal->Y() * $point->Y() 
            + $this->mNormal->Z() * $point->Z() 
            + $this->D()
        );
        // normally we would also need to divide this number by $this->mNormal->Length(), 
        // but since we assert normal is a unit vector, this is unnecessary
    }
    public function D() {
        if ( !isset( $this->mD ) ) {
            $this->mD = -ALiVE_Vectors_InnerProduct( $this->mNormal , $this->mPoint );
        }
        return $this->mD;
    }
}

?>
