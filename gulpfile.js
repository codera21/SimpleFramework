var gulp = require('gulp');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var sass = require('gulp-sass');
var plumber = require('gulp-plumber');
var auto_prefixer = require('gulp-autoprefixer');
var browser_sync = require('browser-sync');
var reload = browser_sync.reload;
var browserSync = require('browser-sync').create();
/*
* Scripts Task
*/
gulp.task('scripts', function () {
    gulp.src(['includes/js/**/*.js', '!includes/js/**/*.min.js'])
        .pipe(plumber())
        .pipe(rename({suffix: '.min'}))
        .pipe(uglify())
        .pipe(gulp.dest('includes/js'))
        .pipe(reload({stream: true}));
});

/*
* Sass Task
*/
// if i wanna compile and compress the css
//   sass({output_style: 'compressed'})
gulp.task('sass', function () {
    gulp.src('includes/scss/main.scss')
        .pipe(plumber())
        .pipe(sass().on('error', sass.logError))
        .pipe(auto_prefixer('last 2 versions'))
        .pipe(gulp.dest('includes/css/'))
        .pipe(reload({stream: true}));
});
/*
* Browser-Sync Task
*/
gulp.task('browser-sync', function () {
    browserSync.init({
        proxy: '127.0.0.1:90/SimpleFramework', // <host name:port>/<project name>
        open: true,
        notify: false
    })
});
/*
* Watch task
*/
gulp.task('watch', function () {
    gulp.watch('includes/js/**/*.js', ['scripts']);
    gulp.watch('includes/scss/**/*.scss', ['sass']);
    gulp.watch('Application/WebInterface/Views/**/*.twig', ['twig']);
    gulp.watch('Application/Admin/Views/**/*.twig', ['twig']);

});
/*
* Default Task
*/
gulp.task('default', ['scripts','sass', 'browser-sync', 'watch']);