    <?php

    use GuzzleHttp\Client;

    //Register a function to handle Schedule events
    ActionFramework::subscribe("Schedule", $_SERVER['PHP_SELF'], function($payload)
    {
    });


