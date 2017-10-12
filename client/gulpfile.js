var gulp = require('gulp');

function getTask(task, inner) {
    inner = inner || '';
    return require('./gulp-tasks' + inner + '/' + task)(gulp, 'src', 'build', 'vendor');
}


/** init task list */
gulp.task('js', getTask('js'));
