<?php
/*
    ALiVE 3D Rendering Engine Version 4 for PHP5
    File: vector.php
    Description: Vector math!
    Developer: Dionyziz
*/

class ALiVE_2D_Vector { // 2D vector
    private $mX;
    private $mY;
    private $mLength;
    
    public function ALiVE_2D_Vector( $x , $y = false ) {
        // new ALiVE_Vector( x , y )
        // new ALiVE_Vector( 4x1_Matrix )
        if ( $y === false ) {
            $this->FromMatrix( $x );
        }
        else {
            $this->Initialize( $x , $y );
        }
    }
    private function Initialize( $x , $y ) {
        w_assert( is_int( $x ) || is_float( $x ) );
        w_assert( is_int( $y ) || is_float( $y ) );
        $this->mX = $x;
        $this->mY = $y;
    }
    public function X() {
        return $this->mX;
    }
    public function Y() {
        return $this->mY;
    }
    private function FromMatrix( ALiVE_Matrix $x ) {
        w_assert( $x->M() == 1 && $x->N() == 4 );
        w_assert( $x->Get( 0 , 3 ) != 0 );
        
        $this->Initialize( // (x'/w', y'/w')
            $x->Get( 0, 0 ) / $x->Get( 0 , 3 ),
            $x->Get( 0, 1 ) / $x->Get( 0 , 3 )
        );
    }
    public function Opposite() {
        return new ALiVE_2D_Vector( -$this->X() , -$this->Y() );
    }
    public function Length() {
        if ( !isset( $this->mLength ) ) { // memoize
            $this->mLength = sqrt( pow( $this->X() , 2 ) + pow( $this->Y() , 2 ) );
        }
        return $this->mLength;
    }
    public function __toString() {
        return '(' . $this->X() . ', ' . $this->Y() . ')';
    }
}

class ALiVE_Vector { // 3D vector
    private $mX;
    private $mY;
    private $mZ;
    private $mLength;
    
    public function ALiVE_Vector( $x , $y = false , $z = false ) {
        // new ALiVE_Vector( x , y , z )
        // new ALiVE_Vector( 4x1_Matrix )
        if ( $y === false && $z === false ) {
            $this->FromMatrix( $x );
        }
        else {
            $this->Initialize( $x, $y, $z );
        }
    }
    private function Initialize( $x , $y , $z ) {
        w_assert( is_int( $x ) || is_float( $x ) );
        w_assert( is_int( $y ) || is_float( $y ) );
        w_assert( is_int( $z ) || is_float( $z ) );
        $this->mX = $x;
        $this->mY = $y;
        $this->mZ = $z;
    }
    private function FromMatrix( ALiVE_Matrix $x ) {
        w_assert( $x->M() == 1 && $x->N() == 4 );
        $this->Initialize(
            $x->Get( 0, 0 ),
            $x->Get( 0, 1 ),
            $x->Get( 0, 2 )
        );
    }
    public function X() {
        return $this->mX;
    }
    public function Y() {
        return $this->mY;
    }
    public function Z() {
        return $this->mZ;
    }
    public function ToMatrix() {
        return ALiVE_Vectors_ToMatrix( array( $this ) );
    }
    public function Opposite() {
        return new ALiVE_Vector( -$this->X(), -$this->Y(), -$this->Z() );
    }
    public function Length() {
        if ( !isset( $this->mLength ) ) { // memoize
            $this->mLength = sqrt( pow( $this->mX , 2 ) + pow( $this->mY , 2 ) + pow( $this->mZ , 2 ) );
        }
        return $this->mLength;
    }
    public function __toString() {
        return '(' . $this->X() . ', ' . $this->Y() . ', ' . $this->Z() . ')';
    }
}

class ALiVE_Unit_Vector extends ALiVE_Vector {
    public function ALiVE_Unit_Vector( $x , $y , $z ) {
        $this->ALiVE_Vector( $x , $y , $z );
        w_assert( $this->Length() == 1 );
    }
}

function ALiVE_Vector_Create( $x , $y , $z ) {
    $vector = new ALiVE_Vector( $x , $y , $z );
    if ( $vector->Length() == 1 ) {
        $vector = new ALiVE_Unit_Vector( $x , $y , $z );
    }
    return $vector;
}

final class ALiVE_Vector_I extends ALiVE_Unit_Vector {
    public function ALiVE_Vector_I() {
        $this->ALiVE_Unit_Vector( 1 , 0 , 0 );
    }
}

final class ALiVE_Vector_J extends ALiVE_Unit_Vector {
    public function ALiVE_Vector_J() {
        $this->ALiVE_Unit_Vector( 0 , 1 , 0 );
    }
}

final class ALiVE_Vector_K extends ALiVE_Unit_Vector {
    public function ALiVE_Vector_K() {
        $this->ALiVE_Unit_Vector( 0 , 0 , 1 );
    }
}

function ALiVE_Vectors_ToMatrix( $vectors ) {
    w_assert( is_array( $vectors ) && count( $vectors ) );
    
    $result = new ALiVE_Matrix( count( $vectors ) , 4 );
    $j = 0;
    foreach ( $vectors as $vector ) {
        w_assert( $vector instanceof ALiVE_Vector );
        $result->Set( $j , 0 , $vector->X() );
        $result->Set( $j , 1 , $vector->Y() );
        $result->Set( $j , 2 , $vector->Z() );
        $result->Set( $j , 3 , 1            );
        ++$j;
    }
    return $result;
}

function ALiVE_Vectors_FromMatrix( ALiVE_Matrix $matrix ) {
    w_assert( $matrix->N() == 4 );
    
    $ret = array();
    for ( $j = 0; $j < $matrix->M(); ++$j ) {
        $ret[] = new ALiVE_Vector( 
            $matrix->Get( $j , 0 ),
            $matrix->Get( $j , 1 ),
            $matrix->Get( $j , 2 )
        );
    }
    
    return $ret;
}

function ALiVE_Vectors_Add( ALiVE_Vector $a, ALiVE_Vector $b ) {
    return new ALiVE_Vector( $a->X() + $b->X(),
                             $a->Y() + $b->Y(),
                             $a->Z() + $b->Z() );
}

function ALiVE_Vectors_Subtract( ALiVE_Vector $a, ALiVE_Vector $b ) {
    return ALiVE_Vectors_Add( $a , $b->Opposite() );
}

function ALiVE_Vectors_InnerProduct( ALiVE_Vector $a , ALiVE_Vector $b ) {
    return   $a->X() * $b->X() 
           + $a->Y() * $b->Y() 
           + $a->Z() * $b->Z();
}

function ALiVE_Vectors_CrossProduct( ALiVE_Vector $a , ALiVE_Vector $b ) {
    return new ALiVE_Vector( $a->Y() * $b->Z() - $a->Z() * $b->Y(),
                             $a->Z() * $b->X() - $a->X() * $b->Z(),
                             $a->X() * $b->Y() - $a->Y() * $b->X() );
}

function ALiVE_Vectors_Equal( ALiVE_Vector $a , ALiVE_Vector $b ) {
    return    $a->X() == $b->X() 
           && $a->Y() == $b->Y()
           && $a->Z() == $b->Z();
}

?>
