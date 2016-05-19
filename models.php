<?php
$models = glob('models/*.php' );
foreach ( $models as $models )
    require( $models );

?>