const { src, dest, watch, series, parallel } = require('gulp');
const sass = require('gulp-sass');
const header = require('gulp-header');
const cleanCSS = require('gulp-clean-css');
const rename = require('gulp-rename');
const uglify = require('gulp-uglify');
const autoprefixer = require('gulp-autoprefixer');
const pkg = require('./package.json');
const sync = require('browser-sync').create();

// Set the banner content
let banner = ['/*!\n',
  ' * Start Bootstrap - <%= pkg.title %> v<%= pkg.version %> (<%= pkg.homepage %>)\n',
  ' * Copyright 2013-' + (new Date()).getFullYear(), ' <%= pkg.author %>\n',
  ' * Licensed under <%= pkg.license %> (https://github.com/BlackrockDigital/<%= pkg.name %>/blob/master/LICENSE)\n',
  ' */\n',
  '\n'
].join('');

// Copy third party libraries from /node_modules into /vendor
function fromNodeToVendor(cb){
    //Bootstrap
    src([
        './node_modules/bootstrap/dist/**/*',
        '!./node_modules/bootstrap/dist/css/bootstrap-grid*',
        '!./node_modules/bootstrap/dist/css/bootstrap-reboot*'
    ])
        .pipe(dest('./vendor/bootstrap'));

    //Font Awesome
    src([
        '.node_modules/@fortawesome/**/*',
    ])
        .pipe(dest('./vendor'));

    //jQuery
    src([
        './node_modules/jquery/dist/*',
        '!.node_modules/jquery/dist/core.js'
    ])
        .pipe(dest('./vendor/jquery'));

    //jQuery Easing
    src([
        './node_modules/jquery.easing/*.js'
    ])
        .pipe(dest('./vendor/jquery-easing'));
    cb();
};

// Compile SCSS
function compileSCSS(){
    return src('./scss/**/*.scss')
        .pipe(sass.sync({
            outputStyle: 'expanded'
        }).on('error', sass.logError))
        .pipe(autoprefixer({
            cascade: false
        }))
        .pipe(header(banner, {
            pkg: pkg
        }))
        .pipe(dest('./css'));
}

// Minify CSS
function minifyCSS(){
    return src([
        './css/*.css',
        '!./css/*.min.css'
    ])
        .pipe(cleanCSS())
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(dest('./css'))
        .pipe(sync.stream());
};

// CSS
function css(cb){
    compileSCSS();
    minifyCSS();
    cb();
}

// Minify JavaScript
function minifyJavaScript(){
    return src([
        './js/*.js',
        '!./js/*.min.js'
    ])
        .pipe(uglify())
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(header(banner, {
            pkg: pkg
        }))
        .pipe(dest('./js'))
        .pipe(sync.stream());
};

// JS
function javascript(cb){
    minifyJavaScript();
    cb();
}

// Configure the browserSync task
function browserSync(){
    sync.init({
        server: {
            baseDir: './'
        }
    });

    watch('./scss/*.scss', css);
    watch('./js/*.js', javascript);
    watch('./*html').on('change', sync.reload);
};

// Default task
exports.default = series(fromNodeToVendor, parallel(css, javascript));

