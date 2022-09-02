<?php
function deb($data, $die = false)
{
    echo "<pre>".print_r($data, 1)."</pre>";
    if($die){
        die;
    }
}
