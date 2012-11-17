<?php
/*
    ALiVE 3D Rendering Engine Version 4 for PHP5
    File: object/cube.php
    Description: Defines the pyramid 3D object class
    Developer: Dionyziz
*/

class ALiVE_Pyramid extends ALiVE_Object {
    public function ALiVE_Pyramid() {
        $this->ALiVE_Object();
        
        /* (base)                          (pos)
            back                            /
         2_______0          (neg)__________/__________(pos) <-- X-axis
         |       |                        /
    left |   .   | right                 /   <-- Y-axis
         |_______|                     (neg)
         3       1
           front                       (pos)
                                         |
                                         |
                                         | <-- Z-axis
                                         |
                                       (neg)
        */
        // base points
        $this->AddPoint( new ALiVE_Vector(  0.5 , -0.5 ,  0.5 ) ); // 0
        $this->AddPoint( new ALiVE_Vector(  0.5 , -0.5 , -0.5 ) ); // 1
        $this->AddPoint( new ALiVE_Vector( -0.5 , -0.5 ,  0.5 ) ); // 2
        $this->AddPoint( new ALiVE_Vector( -0.5 , -0.5 , -0.5 ) ); // 3
        // top point
        $this->AddPoint( new ALiVE_Vector(    0 ,  0.5 ,  0   ) ); // 4
        
        // base
        $this->AddPolygon( 0 , 1 , 3 );
        $this->AddPolygon( 0 , 3 , 2 );
        
        // front
        $this->AddPolygon( 1 , 3 , 4 );
        
        // back
        $this->AddPolygon( 0 , 2 , 4 );
        
        // left
        $this->AddPolygon( 2 , 4 , 3 );
        
        // right
        $this->AddPolygon( 0 , 1 , 4 );
    }
}

?>
