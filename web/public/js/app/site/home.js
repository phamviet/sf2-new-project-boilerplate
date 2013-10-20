define([
    '$',
    'underscore',
    "site/initialize"
], function($, underscore){


    $('body').append('<div>underscore version: ' + underscore.VERSION + '</div>');
    return {};
});