<?php
/*
    ALiVE 3D Rendering Engine Version 4 for PHP5
    File: matrix.php
    Description: Matrices math!
    Developer: Dionyziz
*/

class ALiVE_Matrix {
    private $mM; /* number of rows; item represented by i */
    private $mN; /* number of columns; item represented by j */
    protected $mA;
    
    public function ALiVE_Matrix( $arg0 , $arg1 = false ) {
        if ( $arg1 === false ) {
            // new ALiVE_Matrix( $order );
            w_assert( is_array( $arg0 ) && isset( $arg0[ 0 ] ) && isset( $arg0[ 1 ] ) );
            $this->Initialize( $arg0[ 0 ] , $arg0[ 1 ] );
        }
        else {
            // new ALiVE_Matrix( $m , $n );
            $this->Initialize( $arg0 , $arg1 );
        }
    }
    public function Initialize( $m , $n ) {
        w_assert( is_int( $m ) && is_int( $n ) );
        $this->mM = $m;
        $this->mN = $n;
        $this->mA = array_fill( 0 , $m , array_fill( 0 , $n , 0 ) );
    }
    public function Slice( $i, $j, $m, $n ) {
        // returns a matrix of size $m x $n which is a slice of the original matrix
        // starting at ($i, $j)
        $ret = new ALiVE_Matrix( $m, $n );
        w_assert( $i + $m - 1 < $this->M() );
        w_assert( $j + $n - 1 < $this->N() );
        for ( $x = $i; $x < $i + $m; ++$x ) {
            for ( $y = $j; $y < $j + $m; ++$i ) {
                $ret->Set( $x - $i, $y - $j, $this->mA[ $x ][ $y ] );
            }
        }
        return $ret;
    }
    public function Get( $i , $j ) {
        w_assert( $i >= 0 && $i < $this->mM && $j >= 0 && $j < $this->mN );
        return $this->mA[ $i ][ $j ];
    }
    public function Set( $i , $j , $value ) {
        w_assert( $i >= 0 );
        w_assert( $i < $this->mM );
        w_assert( $j >= 0 );
        w_assert( $j < $this->mN );
        w_assert( is_int( $value ) || is_float( $value ) );
        $this->mA[ $i ][ $j ] = $value;
    }
    public function Transpose() {
        w_assert( $this->mM == $this->mN );
        $result = new ALiVE_Matrix( $this->Order() );
        for ( $i = 0; $i < $result->M(); ++$i ) {
            for ( $j = 0; $j < $result->N(); ++$j ) {
                $result->Set( $i , $j , $this->Get( $j , $i ) );
            }
        }
        return $result;
    }
    public function Inverse() {
        
    }
    public function Opposite() {
        $result = new ALiVE_Matrix( $this->Order() );
        for ( $i = 0; $i < $result->M(); ++$i ) {
            for ( $j = 0; $j < $result->N(); ++$j ) {
                $result->Set( $i , $j , -$this->Get( $i, $j ) );
            }
        }
        return $result;
    }
    public function IsIdentity() {
        for ( $i = 0; $i < $result->M(); ++$i ) {
            for ( $j = 0; $j < $result->N(); ++$j ) {
                if ( $this->mA[ $i ][ $j ] != ( $i == $j ? 1 : 0 ) ) {
                    return false;
                }
            }
        }
        return true;
    }
    public function IsIsotropic() {
        
    }
    public function Order() {
        return array( $this->mM , $this->mN );
    }
    public function M() {
        return $this->mM;
    }
    public function N() {
        return $this->mN;
    }
    public function __toString() {
        $ret = '<table>';
        for ( $i = 0; $i < $this->mM; ++$i ) {
            $ret .= '<tr><td>[</td>';
            for ( $j = 0; $j < $this->mN; ++$j ) {
                $ret .= '<td>' . $this->mA[ $i ][ $j ] . '</td>';
            }
            $ret .= '<td>]</td></tr>';
        }
        $ret .= '</table>';
        return $ret;
    }
}

class ALiVE_Orthogonal_Matrix extends ALiVE_Matrix {
    public function ALiVE_Orthogonal_Matrix( $order ) {
        if ( is_array( $order ) ) {
            w_assert( count( $order ) == 2 && isset( $order[ 0 ] ) && isset( $order[ 1 ] ) );
            w_assert( is_int( $order[ 0 ] ) && $order[ 0 ] === $order[ 1 ] );
            $order = $order[ 0 ];
        }
        w_assert( is_int( $order ) && $order >= 1 );
        $this->ALiVE_Matrix( $order, $order );
    }
    public function Power( $pow ) {
        w_assert( is_int( $pow ) && $pow >= 0 );
        
        switch ( $pow ) {
            case 1:
                return clone $this;
            default:
                $result = ALiVE_Matrix_Create_Identity( $this->Order() );
                for ( $i = 0 ; $i < $pow ; ++$i ) {
                    $result = ALiVE_Matrices_Multiply( $result, $this );
                }
                return $result;
        }
    }
}

function ALiVE_Matrix_Create_Identity( $order ) {
    w_assert( is_int( $order ) && $order >= 1 );
    $result = new ALiVE_Orthogonal_Matrix( $order );
    for ( $i = 0; $i < $order; ++$i ) { // set the major diagonal to 1
        $result->Set( $i , $i , 1 );
    }
    return $result;
}

function ALiVE_Matrices_Homodimentional( ALiVE_Matrix $a , ALiVE_Matrix $b ) {
    return $a->Order() == $b->Order();
}

function ALiVE_Matrices_Add( ALiVE_Matrix $a , ALiVE_Matrix $b ) {
    w_assert( ALiVE_Matrices_Homodimentional( $a , $b ) );
    $result = new ALiVE_Matrix( $a->Dimentions() );
    for ( $i = 0; $i < $result->M() ; ++$i ) {
        for ( $j = 0 ; $j < $result->N() ; ++$j ) {
            // OPTIMIZE: set/get/get operations could be performed friendly on the objects
            $result->Set( $i , $j , $a->Get( $i , $j ) + $b->Get( $i , $j ) );
        }
    }
    return $result;
}

function ALiVE_Matrices_Subtract( ALiVE_Matrix $a , ALiVE_Matrix $b ) {
    return ALiVE_Matrices_Add( $a , $b->Inverse() );
}

function ALiVE_Matrices_Multiplicable( ALiVE_Matrix $a , ALiVE_Matrix $b ) {
    return $a->N() == $b->M();
}

function ALiVE_Matrices_Multiply( ALiVE_Matrix $a , ALiVE_Matrix $b ) {
    w_assert( ALiVE_Matrices_Multiplicable( $a , $b ) );
    $result = new ALiVE_Matrix( $a->M() , $b->N() );
    for ( $i = 0; $i < $result->M(); ++$i ) {
        for ( $j = 0; $j < $result->N(); ++$j ) {
            $cell = 0;
            for ( $k = 0; $k < $a->N() ; ++$k ) {
                $cell += $a->Get( $i , $k ) * $b->Get( $k , $j );
            }
            $result->Set( $i , $j , $cell );
        }
    }
    return $result;
}

function ALiVE_Matrices_Equal( ALiVE_Matrix $a , ALiVE_Matrix $b ) {
    w_assert( ALiVE_Matrices_Homodimentional( $a , $b ) );
    
    for ( $i = 0; $i < $a->M(); ++$i ) {
        for ( $j = 0; $j < $a->N(); ++$j ) {
            if ( $a->Get( $i , $j ) != $b->Get( $i , $j ) ) {
                return false;
            }
        }
    }
    return true;
}

?>
