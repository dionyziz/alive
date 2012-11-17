<?php
/*
    ALiVE 3D Rendering Engine Version 4 for PHP5
    File: world.php
    Description: World rendering file
    Developer: Dionyziz
*/

final class ALiVE_Universe {
    private $mView;
    private $mProjection;
    // objects contain preprocessed object data
    private $mObjects;
    // objects will be moved to points/polygons altogether after Build()
    private $mPoints; // world points after object transformations (3D vectors)
    private $mTransformedPoints; // $this->mPoints after world transformations (2D vectors)
    private $mPolygons;
    
    public function ALiVE_Universe() {
        $this->SetView(
            new ALiVE_Transformation_Neutral()
        ); // default view
        $this->SetProjection(
            new ALiVE_Projection_Isometric()
        ); // default projection
    }
    public function SetView( ALiVE_Transformation $view ) { // i.e. camera
        $this->mView = $view;
    }
    public function SetProjection( ALiVE_Projection $projection ) {
        $this->mProjection = $projection;
    }
    public function AddObject( ALiVE_Object $object ) {
        $this->mObjects[] = $object;
    }
    protected function Build() {
        $this->mPoints = array();
        $this->mPolygons = array();
        foreach ( $this->mObjects as $object ) {
            // apply object transformations (object scaling, object rotation, object translation)
            $object->Transform(); 
            $objectpoints = $object->GetTransformedPoints();
            // offset of first point of this object in world points array            
            $objectoffset = count( $this->mPoints );
            foreach ( $objectpoints as $vector ) {
                $this->mPoints[] = $vector;
            }
            $objectpolygons = $object->GetPolygons();
            foreach ( $objectpolygons as $polygon ) { // $polygon is a copy
                // offset should change from $object->GetTransformedPoints() offset
                // to $this->mPoints offset
                w_assert( isset( $polygon[ 0 ] ) );
                w_assert( isset( $polygon[ 1 ] ) );
                w_assert( isset( $polygon[ 2 ] ) );
                w_assert( isset( $objectpoints[ $polygon[ 0 ] ] ) );
                w_assert( isset( $objectpoints[ $polygon[ 1 ] ] ) );
                w_assert( isset( $objectpoints[ $polygon[ 2 ] ] ) );
                $polygon[ 0 ] += $objectoffset;
                $polygon[ 1 ] += $objectoffset;
                $polygon[ 2 ] += $objectoffset;
                $this->mPolygons[] = $polygon;
            }
        }
    }
    public function GetTransformedPoints() {
        return $this->mTransformedPoints;
    }
    public function GetPolygons() {
        return $this->mPolygons;
    }
    public function Transform() {
        $this->Build();
        // apply world transformations
        // OPTIMIZABLE: TODO: Find a way to combine a normal transformation and a projection?
        //                    and be able to make ->Apply() return a 2D vector?
        $afterview = $this->mView->Apply( $this->mPoints );
        $this->mTransformedPoints = $this->mProjection->Apply( $afterview );
    }
}

?>
