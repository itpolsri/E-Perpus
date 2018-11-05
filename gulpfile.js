const
  gulp = require('gulp'),
  concat = require('gulp-concat'),
  order = require('gulp-order'),
  plumber = require('gulp-plumber'),
  gsass = require('gulp-sass'),
  uglify = require('gulp-uglify'),
  cssMinify = require('gulp-csso'),
  del = require('del');

const app = {
  sass: {
    src: 'src/sass/main.scss',
    dest: 'assets/css/'
  },
  script: {
    src: 'src/js/**/*.js',
    dest: 'assets/js/',
    order: [
      "src/js/**/*.js"
    ]
  }
}

const clean = () => {
  return del([
    'assets/css/',
    'assets/js/',
  ]);
};

const sass = () => {
  return gulp.src(app.sass.src)
  .pipe(plumber())
  .pipe(gsass())
  .pipe(cssMinify())
  .pipe(gulp.dest(app.sass.dest))
};

const script = () => {
  return gulp.src(app.script.src)
  .pipe(plumber())
	.pipe(order(app.script.order, { base: './' }))
  .pipe(concat('main.js'))
  .pipe(uglify()) // {mangle: true}
  .pipe(gulp.dest(app.script.dest));
};

const watch = () => {
  gulp.watch(app.sass.src, sass);
  gulp.watch(app.script.src, script);
};

const build = gulp.series(clean, gulp.parallel(sass, script));

gulp.task('default', build);
