// gulpfile.js

const { series, parallel } = require('gulp');
const gulp          = require('gulp');
const path          = require('path');
const del           = require('del');
const less          = require('gulp-less');
const rename        = require("gulp-rename");
const minify        = require('gulp-minify');
const cleanCSS      = require('gulp-clean-css');
const babel         = require('gulp-babel');

const CONFIG = require('./src/config.js');

// -------------------------------------------------------------------


function build(cb) {
    cb();
}

function buildLess() {
    return gulp.src(CONFIG.assetsSourceDir + '/styles/default.less')
    .pipe(less())
    .pipe(cleanCSS({ level: 1 }))
    .pipe(rename({ suffix: '.min' }))
    .pipe(gulp.dest(CONFIG.assetsDir + '/css'));
}

exports.default = series(build, buildLess);

exports.build = series(build, buildLess);

// END OF gulpfile.js
