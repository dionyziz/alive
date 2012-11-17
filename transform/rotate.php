<?php
/*
    ALiVE 3D Rendering Engine Version 4 for PHP5
    File: transform/rotate.php
    Description: Handling rotations in 3D space
    Developer: Dionyziz
*/

final class ALiVE_Rotation extends ALiVE_Transformation {
    private $mPitch; // rotation around X axis (float in radians)
    private $mYaw; // rotation around Y axis (float in radians)
    private $mRoll; // rotation around Z axis (float in radians)
    
    public function ALiVE_Rotation( $pitch , $yaw, $roll ) {
        w_assert( is_int( $pitch ) || is_float( $pitch ) );
        w_assert( is_int( $yaw   ) || is_float( $yaw   ) );
        w_assert( is_int( $roll  ) || is_float( $roll  ) );
        $this->mPitch = $pitch;
        $this->mYaw   = $yaw;
        $this->mRoll  = $roll;
    }
    public function Pitch() {
        return $this->mPitch;
    }
    public function Yaw() {
        return $this->mYaw;
    }
    public function Roll() {
        return $this->mRoll;
    }
    protected function MakeMatrix() {
        $this->mMatrix = new ALiVE_Matrix( 4, 4 );
        $a = cos( $this->mPitch );
        $b = sin( $this->mPitch );
        $c = cos( $this->mYaw   );
        $d = sin( $this->mYaw   );
        $e = cos( $this->mRoll  );
        $f = sin( $this->mRoll  );
        $ad = $a * $d;
        $bd = $b * $d;
        /* 
            this is basically a multiplication of the following matrices,
            but fast:
            
            Let:
            (A, B) = (cos(pitch), sin(pitch))
            (C, D) = (cos(yaw)  , sin(yaw)  )
            (E, F) = (cos(roll) , sin(roll) )
            
              ( X-axis rotation matrix )
                    | 1 0  0 0 |
                    | 0 A -B 0 |
                    | 0 B -A 0 |
                    | 0 0  0 1 |
                         
                         x
                         
              ( Y-axis rotation matrix )
                    | C 0 -D 0 |
                    | 0 1  0 0 |
                    | D 0  C 0 |
                    | 0 0  0 1 |
                         
                         x
                         
              ( Z-axis rotation matrix )
                    | E -F 0 0 |
                    | F  E 0 0 |
                    | 0  0 0 0 |
                    | 0  0 0 1 |
                         
                         =
                         
                   $this->mMatrix
             |  CE      -CF      -D   0 |
             | -BDE+AF   BDF+AE  -BC  0 |
             |  ADE+BF  -ADF+BE   AC  0 |
             |  0        0        0   1 |
         */
        $this->mMatrix->Set( 0, 0,  $c  * $e           );
        $this->mMatrix->Set( 1, 0, -$bd * $e + $a * $f );
        $this->mMatrix->Set( 2, 0,  $ad * $e + $b * $f );

        $this->mMatrix->Set( 0, 1, -$c * $f            );
        $this->mMatrix->Set( 1, 1,  $bd * $f + $a * $e );
        $this->mMatrix->Set( 2, 1, -$ad * $f + $b * $e );

        $this->mMatrix->Set( 0, 2, -$d                 );
        $this->mMatrix->Set( 1, 2, -$b * $c            );
        $this->mMatrix->Set( 2, 2,  $a * $c            );

        $this->mMatrix->Set( 3, 3,  1                  );
    }
}

?>
