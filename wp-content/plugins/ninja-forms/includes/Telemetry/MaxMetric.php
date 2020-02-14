<?php

class NF_Telemetry_MaxMetric extends NF_Telemetry_Metric
{
    public function update( $value )
    {
        $old_max = $this->get();
        $new_max = max( $old_max, $value );
        return $this->save( $new_max );
    }
}