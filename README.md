# ticker
A simple PHP profiler for use in production environments


## Installation

Ticker is easy to install into a PHP project by using [Composer](https://getcomposer.org/):

    composer require deltasystems/ticker


## Usage

Once installed in your project, you can start the server:

    vendor/bin/ticker

Ticker will provide some initial output that contains a cookie value  used for associating your browser's requests with
the need to track the length of time of some operation(s) in your application. You can simply copy and paste the line
that starts with "document.cookie" into the JavaScript console of your browser's developer tools:

    document.cookie = "TICKER=somethingrandomenoughforthepurposeofidentifyingaprofilerequest"
    
    Event                                    | From Start     | From Previous  
    --------------------------------------------------------------------------

The output also contains table headers for identifying a particular event, the amount of time elapsed since Ticker was
initialized, and the amount of time elapsed since the previous event.

Once your browser has the needed cookie value set, you can add a bit of code to your application to track some events:

    require_once __DIR__ . '/vendor/autoload.php';
    
    ticker_init();
    
    // ...
    
    ticker('something');
    
    sleep(2);
    
    ticker('something else');

Then, use your browser to trigger the application code, and the Ticker output should look something like the following:

    Event                                    | From Start     | From Previous  
    --------------------------------------------------------------------------
    request-started                          |            0ms |            0ms
    ticker-started                           |          261ms |          261ms
    something                                |          261ms |            0ms
    something else                           |         2262ms |         2000ms
    request-ended                            |         3792ms |         1529ms
    --------------------------------------------------------------------------

As you can see, in addition to the custom events that we specified, Ticker provides some built-in events that
correspond to the start of the request, when Ticker was started, and when the request ended.

