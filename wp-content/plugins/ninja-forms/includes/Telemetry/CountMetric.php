<?php

class NF_Telemetry_CountMetric extends NF_Telemetry_Metric
{
    public function increment( $increment = 1 )
    {
        $count = $this->get();
        $new_count = $count + $increment;
        return $this->save( $new_count );
    }
}