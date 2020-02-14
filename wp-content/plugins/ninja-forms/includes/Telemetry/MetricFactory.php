<?php

class NF_Telemetry_MetricFactory
{
    public static function create( $metric, $option, $default = 0 )
    {
        $metric_class = 'NF_Telemetry_' . $metric;
        $repository = new NF_Telemetry_MetricRepository( $option, $default );
        return new $metric_class( $repository );
    }
}