import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { viteStaticCopy } from 'vite-plugin-static-copy'

// import react from '@vitejs/plugin-react';
// import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel([
            //'resources/css/app.css',
            'resources/js/app.js',
        ]),
        viteStaticCopy({
           targets: [
            {
            src: 'node_modules/jquery/dist/jquery.min.js',
            dest: 'public/js/node/jquery.min.js'
            },
            {
            src: 'node_modules/jquery-ui/dist/jquery-ui.min.js',
            dest: 'public/js/node/jquery-ui.min.js'
            },
            {
            src: 'node_modules/jquery-ui/dist/themes/base/jquery-ui.min.css',
            dest: 'public/css/node/jquery-ui.min.css'
            },
            {
            src: 'node_modules/moment/min/moment.min.js',
            dest: 'public/js/node/moment.min.js'
            },
            {
            src: 'node_modules/@popperjs/core/dist/umd/popper.min.js',
            dest: 'public/js/node/popper.min.js'
            },
            {
            src: 'node_modules/datatables.net/js/dataTables.js',
            dest: 'public/js/node/jquery.dataTables.min.js'
            },
            {
            src: 'node_modules/datatables.net-dt/css/dataTables.dataTables.min.css',
            dest: 'public/css/node/jquery.dataTables.min.css'
            },
            {
            src: 'node_modules/datatables.net-fixedcolumns/js/dataTables.fixedColumns.js',
            dest: 'public/js/node/dataTables.fixedColumns.min.js'
            },
            {
            src: 'node_modules/datatables.net-fixedcolumns-dt/css/fixedColumns.dataTables.min.css',
            dest: 'public/css/node/fixedColumns.dataTables.min.css'
            },
            {
            src: 'node_modules/select2/dist/js/select2.full.min.js',
            dest: 'public/js/node/select2.min.js'
            },
            {
            src: 'node_modules/select2/dist/css/select2.min.css',
            dest: 'public/css/node/select2.min.css'
            },
            {
            src: 'resources/js/vendor/jquery.daterangepicker.min.js',
            dest: 'public/js/node/jquery.daterangepicker.min.js'
            },
            {
            src: 'resources/css/vendor/daterangepicker.min.css',
            dest: 'public/css/node/jquery.daterangepicker.min.css'
            },
            {
            src: 'node_modules/pdfjs-dist/build',
            dest: 'public/js/pdfjs-viewer/build'
            },
            {
            src: 'node_modules/pdfjs-dist/cmaps',
            dest: 'public/js/pdfjs-viewer/cmaps'
            },
            {
            src: 'node_modules/pdfjs-dist/standard_fonts',
            dest: 'public/js/pdfjs-viewer/standard_fonts'
            },
            {
            src: 'node_modules/@fortawesome/fontawesome-free',
            dest: 'public/vendor/font-awesome'
            },
            {
            src: 'node_modules/material-design-icons/iconfont',
            dest: 'public/vendor/material-icons'
            },
            {
            src: 'node_modules/@fontsource/nunito',
            dest: 'public/vendor/fontsource/nunito'
            },
            {
            src: 'node_modules/@fontsource/roboto',
            dest: 'public/vendor/fontsource/roboto'
            },
            {
            src: 'node_modules/@fontsource/cairo',
            dest: 'public/vendor/fontsource/cairo'
            },
            
           ]
        }),
        // react(),
        // vue({
        //     template: {
        //         transformAssetUrls: {
        //             base: null,
        //             includeAbsolute: false,
        //         },
        //     },
        // }),
    ],
});
