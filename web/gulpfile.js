var gulp = require('gulp'),
  sass = require('gulp-sass'),
  autoprefixer = require('gulp-autoprefixer'),
  cleanCSS = require('gulp-clean-css'),
  rename = require('gulp-rename'),
  notify = require('gulp-notify'),
  uglify = require('gulp-uglify'),
  webpack = require('webpack'),
  webpackStream = require('webpack-stream'),
  ExtractTextPlugin = require('extract-text-webpack-plugin'),
  merge = require('merge-stream'),
  concat = require('gulp-concat'),
  bulkSass = require('gulp-sass-bulk-import'),
  sourcemaps = require('gulp-sourcemaps');
  touch = require('gulp-touch-fd');

const sourcesPath = './src';
const assetsPath = './dist';

gulp.task('scripts', function() {
  return gulp
    .src(sourcesPath + '/js/app.js')
    .pipe(
      webpackStream({
        output: {
          filename: 'app.min.js',
        },
        module: {
          rules: [
            {
              test: /\.(js)$/,
              exclude: /node_modules/,
              loader: 'babel-loader',
              query: {
                presets: ['@babel/preset-env'],
              },
            },
            // {
            //   test: /\.css$/,
            //   use: ExtractTextPlugin.extract({
            //     use: "css-loader"
            //   })
            // }
          ],
        },
        // mode: 'development',
        mode: 'production',
        // plugins: [
        //   new ExtractTextPlugin("/../../src/css/modules.css"),
        // ]
      })
    )
    .pipe(gulp.dest(assetsPath + '/js/'));
});

gulp.task('styles', function(done) {
  sassStream = gulp
    .src(sourcesPath + '/sass/**/*.scss')
    .pipe(sourcemaps.init())
    .pipe(bulkSass())
    .pipe(sass().on('error', sass.logError))
    // cssStream = gulp.src(sourcesPath + '/css/**/*.css');
    // return merge(sassStream, cssStream)
    .pipe(concat('app.css'))
    .pipe(autoprefixer())

    .pipe(cleanCSS())
    .pipe(rename({ suffix: '.min' }))
    .pipe(sourcemaps.write('./'))
    .pipe(gulp.dest(assetsPath + '/css')).pipe(touch());
  done();
});

gulp.task('watch', function(done) {
  gulp.watch(sourcesPath + '/js/**/*.js', gulp.series('scripts'));
  gulp.watch(sourcesPath + '/sass/**/*.scss', gulp.series('styles'));
  gulp.watch(sourcesPath + '/css/**/*.css', gulp.series('styles'));
  done();
});
