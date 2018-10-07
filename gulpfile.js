var gulp = require('gulp');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var sass = require('gulp-sass');
var plumber = require('gulp-plumber');
var auto_prefixer = require('gulp-autoprefixer');

/*
* Scripts Task
*/
gulp.task('scripts', function () {
    gulp.src(['includes/js/**/*.js', '!includes/js/**/*.min.js'])
        .pipe(plumber())
        .pipe(rename({suffix: '.min'}))
        .pipe(uglify())
        .pipe(gulp.dest('includes/js'));

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
        .pipe(gulp.dest('includes/css/'));

});

/*
* Watch task
*/
gulp.task('watch', function () {
    gulp.watch('includes/js/**/*.js', ['scripts']);
    gulp.watch('includes/scss/**/*.scss', ['sass']);
  });




  gulp.task('bs' , () => {
    gulp.src('includes/scss/scss/bootstrap.scss')
        .pipe(plumber())
        .pipe(sass().on('error', sass.logError))
        .pipe(auto_prefixer('last 2 versions'))
        .pipe(gulp.dest('includes/css/'));
  });

/*
* Default Task
*/
gulp.task('default', ['scripts','sass','watch']);
