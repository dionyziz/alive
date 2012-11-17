<?php
    require_once 'alive.php';    
    
    $invector = new ALiVE_Vector( 5 , -3 , 12 );
    $outvector = new ALiVE_Vector( 1 , 0 , 0 );
    $myplane = new ALiVE_Plane( new ALiVE_Vector( 0, 0, 0 ), new ALiVE_Vector( 12, 3, 108 ), new ALiVE_Vector( 5, -3, 12 ) );
    w_assert( $myplane->Contains( $invector ) );
    w_assert( !$myplane->Contains( $outvector ) );
	
    $i = new ALiVE_Vector_I();
    $j = new ALiVE_Vector_J();
    $k = new ALiVE_Vector_K();
    
    w_assert( ALiVE_Vectors_Equal( ALiVE_Vectors_CrossProduct( $i, $j ) , $k ) );
    w_assert( ALiVE_Vectors_Equal( ALiVE_Vectors_CrossProduct( $j, $k ) , $i ) );
    w_assert( ALiVE_Vectors_Equal( ALiVE_Vectors_CrossProduct( $k, $i ) , $j ) );
    
    $matrix = ALiVE_Matrix_Create_Identity( 4 );
    w_assert( $matrix->M() == 4 && $matrix->N() == 4);
    w_assert( $matrix->Get( 0 , 1 ) == 0 );
    w_assert( $matrix->Get( 0 , 0 ) == 1 );
    w_assert( $matrix->Get( 1 , 1 ) == 1 );
    w_assert( $matrix->Get( 2 , 2 ) == 1 );
    w_assert( $matrix->Get( 3 , 3 ) == 1 );
    
    $neutral = new ALiVE_Transformation_Neutral();
    $vector = new ALiVE_Vector( 10.5 , 7 , -34.67 );
    $result = $neutral->Apply( $vector );
    w_assert( $result instanceof ALiVE_Vector );
    w_assert( $result->X() ==  10.5  );
    w_assert( $result->Y() ==   7    );
    w_assert( $result->Z() == -34.67 );
    
    $p = new ALiVE_Vector( 0.4 , 0 , 0 );
    $neutral1 = new ALiVE_Translation( 0 , 0 , 0 );
    $neutral2 = new ALiVE_Rotation( 0 , 0 , 0 );
    $neutral3 = new ALiVE_Scaling( 1 , 1 , 1 );
    w_assert( ALiVE_Matrices_Equal( $p->ToMatrix() , $neutral1->Apply( $p->ToMatrix() ) ) );
    w_assert( ALiVE_Matrices_Equal( $p->ToMatrix() , $neutral2->Apply( $p->ToMatrix() ) ) );
    w_assert( ALiVE_Matrices_Equal( $p->ToMatrix() , $neutral3->Apply( $p->ToMatrix() ) ) );
?>
