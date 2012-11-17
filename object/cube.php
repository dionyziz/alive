<?php
/*
    ALiVE 3D Rendering Engine Version 4 for PHP5
    File: object/cube.php
    Description: Defines the cube 3D object class
    Developer: Dionyziz
*/

class ALiVE_Cube extends ALiVE_Object {
    public function ALiVE_Cube() {
        $this->ALiVE_Object();
        
        $this->AddPoint( new ALiVE_Vector(  0.5 ,  0.5 ,  0.5 ) ); // 0
        $this->AddPoint( new ALiVE_Vector(  0.5 ,  0.5 , -0.5 ) ); // 1
        $this->AddPoint( new ALiVE_Vector(  0.5 , -0.5 ,  0.5 ) ); // 2
        $this->AddPoint( new ALiVE_Vector(  0.5 , -0.5 , -0.5 ) ); // 3
        
        $this->AddPoint( new ALiVE_Vector( -0.5 ,  0.5 ,  0.5 ) ); // 4
        $this->AddPoint( new ALiVE_Vector( -0.5 ,  0.5 , -0.5 ) ); // 5
        $this->AddPoint( new ALiVE_Vector( -0.5 , -0.5 ,  0.5 ) ); // 6
        $this->AddPoint( new ALiVE_Vector( -0.5 , -0.5 , -0.5 ) ); // 7
        
        // parallel to (P = (0, 0, 0), n = (0, 0, 1))
        //  front
        $this->AddPolygon( 1 , 7 , 5 );
        $this->AddPolygon( 1 , 3 , 7 );
        //  back
        $this->AddPolygon( 0 , 4 , 2 );
        $this->AddPolygon( 2 , 4 , 6 );
        
        // parallel to (P = (0, 0, 0), n = (1, 0, 0))
        //  left
        $this->AddPolygon( 4 , 5 , 6 );
        $this->AddPolygon( 5 , 7 , 6 );
        //  right
        $this->AddPolygon( 0 , 2 , 1 );
        $this->AddPolygon( 1 , 2 , 3 );
        
        // parallel to (P = (0, 0, 0), n = (0, 1, 0))
        //  bottom
        $this->AddPolygon( 2 , 6 , 7 );
        $this->AddPolygon( 2 , 7 , 3 );
        //  top
        $this->AddPolygon( 0 , 5 , 4 );
        $this->AddPolygon( 0 , 1 , 5 );
    }
}

?>
