require('../css/app.css');
// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
const $ = require('jquery');
global.$ = global.jQuery = $;
require('bootstrap/dist/js/bootstrap');
require('bootstrap/dist/css/bootstrap.css');
require('toastr/build/toastr.css');
toastr = require('toastr/build/toastr.min');

require('mdbootstrap/css/mdb.css')
require('mdbootstrap/js/mdb')
bsCustomFileInput = require('mdbootstrap/js/modules/bs-custom-file-input');