<?php

//Boot Carbon Custom Fields

add_filter('setup_theme',function(){
    \Carbon_Fields\Carbon_Fields::boot();
});

include_once 'custom-fields-theme-options.php';