<?php
/*
    ALiVE 3D Rendering Engine Version 4 for PHP5
    File: object/object.php
    Description: Main 3D objects file
    Developer: Dionyziz
*/

require_once 'object/cube.php';
require_once 'object/pyramid.php';
 
class ALiVE_Object {
    private $mPoints; // array of vertices
    private $mTransformedPoints; // array of vertices, after object transformations have been applied
    private $mPolygons; // array of arrays of three integers, keys in $mPoints
    private $mScaling; // scaling
    private $mRotation; // rotation
    private $mTranslation; // translation
    private $mTransformed;
    
    public function ALiVE_Object() {
        $this->mPoints = array();
        $this->mScaling = new ALiVE_Scaling( 1 , 1 , 1 ); // no scaling
        $this->mRotation = new ALiVE_Rotation( 0 , 0 , 0 ); // no rotation
        $this->mTranslation = new ALiVE_Translation( 0 , 0 , 0 ); // no translation
        $this->mTransformed = false;
    }
    public function SetScaling( ALiVE_Scaling $scaling ) {
        $this->mTransformed = false; // invalidate current transformation
        $this->mScaling = $scaling;
    }
    public function SetRotation( ALiVE_Rotation $rotation ) {
        $this->mTransformed = false; // invalidate current transformation
        $this->mRotation = $rotation;
    }
    public function SetTranslation( ALiVE_Translation $translation ) { // i.e. position in the world
        $this->mTransformed = false; // invalidate current transformation
        $this->mTranslation = $translation;
    }
    public function AddPoint( ALiVE_Vector $point ) {
        $this->mTransformed = false; // invalidate current transformation
        $this->mPoints[] = $point;
    }
    public function AddPolygon( $i, $j, $k ) { // keep the points in clockwise-order from viewpoint!
        w_assert(    is_int( $i ) 
                  && is_int( $j )
                  && is_int( $k ) );
        w_assert(    isset( $this->mPoints[ $i ] )
                  && isset( $this->mPoints[ $j ] ) 
                  && isset( $this->mPoints[ $k ] ) );
        $this->mPolygons[] = array( $i , $j , $k );
    }
    public function GetTransformedPoints() {
        if ( !$this->mTransformed ) {
            throw new Exception( 'ALiVE_Object has not been transformed', $this );
        }
        return $this->mTransformedPoints;
    }
    public function GetPolygons() {
        return $this->mPolygons;
    }
    public function Transform() {
        if ( $this->mTransformed ) {
            return;
        }
        // 1. Scale
        // 2. Rotate
        // 3. Translate
        
        $transformation = ALiVE_Transformations_Combine( $this->mScaling , $this->mRotation );
        $transformation = ALiVE_Transformations_Combine( $transformation , $this->mTranslation );
        
        // OPTIMIZABLE: TODO: Change this to use only one ->Apply() call to a combined matrix
        //                    of all vectors-to-be-transformed
        foreach ( $this->mPoints as $i => $point ) {
            $this->mTransformedPoints[ $i ] = $transformation->Apply( $point );
        }
        $this->mTransformed = true;
    }
}

?>
