({
    appDir: '../app',
    mainConfigFile: "../app/main.js",
    dir: "../../js-min",
    baseUrl: '.',
    modules: [
        {
            name: "main",
            include: [
                '$',
                'bootstrap',
                'select2',
                'underscore'
            ]
        },
        {
            name: "site/home",
            exclude: [
                "main"
            ],
            include: [
            ]
        }
    ]
})