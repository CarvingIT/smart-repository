const mix = require("laravel-mix");

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js("resources/js/app.js", "public/js").sass(
    "resources/sass/app.scss",
    "public/css",
);

// Copy npm dependencies to public folder
mix.copy(
    "node_modules/jquery/dist/jquery.min.js",
    "public/js/node/jquery.min.js",
);
mix.copy(
    "node_modules/jquery-ui/dist/jquery-ui.min.js",
    "public/js/node/jquery-ui.min.js",
);
mix.copy(
    "node_modules/jquery-ui/dist/themes/base/jquery-ui.min.css",
    "public/css/node/jquery-ui.min.css",
);
mix.copy(
    "node_modules/moment/min/moment.min.js",
    "public/js/node/moment.min.js",
);
mix.copy(
    "node_modules/@popperjs/core/dist/umd/popper.min.js",
    "public/js/node/popper.min.js",
);

// DataTables
mix.copy(
    "node_modules/datatables.net/js/dataTables.js",
    "public/js/node/jquery.dataTables.min.js",
);
mix.copy(
    "node_modules/datatables.net-dt/css/dataTables.dataTables.min.css",
    "public/css/node/jquery.dataTables.min.css",
);
mix.copy(
    "node_modules/datatables.net-fixedcolumns/js/dataTables.fixedColumns.js",
    "public/js/node/dataTables.fixedColumns.min.js",
);
mix.copy(
    "node_modules/datatables.net-fixedcolumns-dt/css/fixedColumns.dataTables.min.css",
    "public/css/node/fixedColumns.dataTables.min.css",
);

// Select2
mix.copy(
    "node_modules/select2/dist/js/select2.full.min.js",
    "public/js/node/select2.min.js",
);
mix.copy(
    "node_modules/select2/dist/css/select2.min.css",
    "public/css/node/select2.min.css",
);

// DateRangePicker
mix.copy(
    "resources/js/vendor/jquery.daterangepicker.min.js",
    "public/js/node/jquery.daterangepicker.min.js",
);
mix.copy(
    "resources/css/vendor/daterangepicker.min.css",
    "public/css/node/jquery.daterangepicker.min.css",
);

// PDF.js viewer - copy large binary assets from pdfjs-dist npm package
// (viewer UI files like viewer.html, images/ are committed to git)
mix.copyDirectory('node_modules/pdfjs-dist/build', 'public/js/pdfjs-viewer/build');
mix.copyDirectory('node_modules/pdfjs-dist/cmaps', 'public/js/pdfjs-viewer/cmaps');
mix.copyDirectory('node_modules/pdfjs-dist/standard_fonts', 'public/js/pdfjs-viewer/standard_fonts');

// Font Awesome (serves CSS + webfonts locally; CSS uses ../webfonts/ relative path)
mix.copyDirectory(
    "node_modules/@fortawesome/fontawesome-free",
    "public/vendor/font-awesome",
);

// Material Icons (iconfont CSS uses relative paths for font files - copy whole folder)
mix.copyDirectory(
    "node_modules/material-design-icons/iconfont",
    "public/vendor/material-icons",
);

// Fontsource fonts (self-contained: CSS references ./files/* relative paths)
mix.copyDirectory(
    "node_modules/@fontsource/nunito",
    "public/vendor/fontsource/nunito",
);
mix.copyDirectory(
    "node_modules/@fontsource/roboto",
    "public/vendor/fontsource/roboto",
);
mix.copyDirectory(
    "node_modules/@fontsource/cairo",
    "public/vendor/fontsource/cairo",
);
