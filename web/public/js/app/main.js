// r.js -o web/public/js/tools/build.js paths.facebook=empty:
var SITE = SITE || { deps: [], FACEBOOK_APP_ID: '' };
SITE.url = function(path) {
    var url = this.BASE_URL + ((path[0] || '') == '/' ? path : '/' + path);
    return url.replace(/\/\//g,'/');
};

(function() {
    require.config({
        waitSeconds: 30,
        paths: {
            '$': '../libs/jquery.min',
            underscore: '../libs/underscore-min',
            html5shiv: '../libs/html5shiv',
            respond: '../libs/respond.min',
            bootstrap: "../../vendor/bootstrap/dist/js/bootstrap.min",
            select2: "../../vendor/select2/select2.min",
            plupload: "../../vendor/plupload/js/plupload.full.min",
            facebook: '//connect.facebook.net/en_US/all'
        },
        shim: {
            '$': { exports: '$' },

            'plupload': { exports: 'plupload' },

            underscore: {
                exports: '_'
            },

            'facebook' : {
                exports: 'FB'
            },
            'bootstrap': {
                deps: ['$']
            },
            'select2': { deps: ['$'] },
            'site/initialize': { deps: ['html5shiv', 'respond'] }
        }
    });

    if(SITE.deps.length) {
        require(SITE.deps, function() { });
    }

})();