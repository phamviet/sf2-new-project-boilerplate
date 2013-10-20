define([ "facebook" ], function(){

    FB.init({
        appId      : SITE.FACEBOOK_APP_ID,
        channelUrl : '//fb-channel',
        status     : true,
        cookie     : true,
        xfbml      : true
    });

    return {};
});