<?php
    require_once 'alive.php';    
    
    $zz = ( float )$_GET[ 'z' ];

    $world = new ALiVE_Universe();
    $a = new ALiVE_Cube();
    $a->SetScaling( new ALiVE_Scaling( 0.3 , 0.3 , 0.3 ) );
    $a->SetTranslation( new ALiVE_Translation( -0.7 , 0 , 1 ) );
    $a->SetRotation( new ALiVE_Rotation( $zz , 0 , 0 ) );
    $world->AddObject( $a );
    
    $b = new ALiVE_Cube();
    $b->SetScaling( new ALiVE_Scaling( 0.8 , 0.8 , 0.8 ) );
    $b->SetTranslation( new ALiVE_Translation( 0 , 0 , 2 ) );
    $b->SetRotation( new ALiVE_Rotation( $zz , $zz , $zz ) );
    $world->AddObject( $b );

    $c = new ALiVE_Cube();
    $c->SetScaling( new ALiVE_Scaling( 0.3 , 0.3 , 0.3 ) );
    $c->SetTranslation( new ALiVE_Translation( 0.7 , 0 , 1 ) );
    $c->SetRotation( new ALiVE_Rotation( 0 , $zz , $zz ) );
    $world->AddObject( $c );

    $camera = new ALiVE_Camera();
    $camera->SetPosition( 0 , 0 , 0.1 );
    $world->SetView( $camera );
    $projection = new ALiVE_Projection_Perspective();
    $world->SetProjection( $projection );
    $drv = new ALiVE_Driver_GD();
    $drv->SetSize( 650 , 500 );
    
    // average rendering time for two cubes in perspective projection: 200ms
    $drv->Render( $world );
?>
