module.exports = function (gulp, source, build, vendorDir) {
    'use strict';

    var concat = require('gulp-concat'),
        uglify = require('gulp-uglify'),
        ngAnnotate = require('gulp-ng-annotate'),
        argv = require('yargs').argv,

        configFileName = argv['config-file'] || 'docker.js',
        configFilePath = source + '/config/' + configFileName;

    return function () {
        var src  = source + '/';
        var dest = build  + '/';

        gulp.src([
                 configFilePath,
                 src +'/**/bootstrap.js',
                 src +'/**/*.js',
                 '!' + src + '/**/test/**/*.js'
            ])
            .pipe(concat('app.js'))
            .pipe(ngAnnotate())
            .pipe(uglify())
            .pipe(gulp.dest(dest));
    };
};