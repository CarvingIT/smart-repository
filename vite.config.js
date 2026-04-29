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
            dest: 'assets/js',
            rename: {stripBase:true}
            },
            {
            src: 'node_modules/jquery-ui/dist/jquery-ui.min.js',
            dest: 'assets/js',
            rename: {stripBase:true}
            },
            {
            src: 'node_modules/jquery-ui/dist/themes/base/jquery-ui.min.css',
            dest: 'assets/css',
            rename: {stripBase:true}
            },
            {
            src: 'node_modules/moment/min/moment.min.js',
            dest: 'assets/js',
            rename: {stripBase:true}
            },
            {
            src: 'node_modules/@popperjs/core/dist/umd/popper.min.js',
            dest: 'assets/js',
            rename: {stripBase:true}
            },
            {
            src: 'node_modules/datatables.net/js/dataTables.js',
            dest: 'assets/js',
            rename: {stripBase:true}
            },
            {
            src: 'node_modules/datatables.net-dt/css/dataTables.dataTables.min.css',
            dest: 'assets/css',
            rename: {stripBase:true}
            },
            {
            src: 'node_modules/datatables.net-fixedcolumns/js/dataTables.fixedColumns.js',
            dest: 'assets/js',
            rename: {stripBase:true}
            },
            {
            src: 'node_modules/datatables.net-fixedcolumns-dt/css/fixedColumns.dataTables.min.css',
            dest: 'assets/css',
            rename: {stripBase:true}
            },
            {
            src: 'node_modules/select2/dist/js/select2.full.min.js',
            dest: 'assets/js',
            rename: {stripBase:true}
            },
            {
            src: 'node_modules/select2/dist/css/select2.min.css',
            dest: 'assets/css',
            rename: {stripBase:true}
            },
            {
            src: 'resources/js/vendor/jquery.daterangepicker.min.js',
            dest: 'assets/js',
            rename: {stripBase:true}
            },
            {
            src: 'resources/css/vendor/daterangepicker.min.css',
            dest: 'assets/css',
            rename: {stripBase:true}
            },
            {
            src: 'node_modules/pdfjs-dist',
            dest: 'assets/pdfjs',
            },
            {
            src: 'node_modules/@fortawesome/fontawesome-free',
            dest: 'assets/fonts',
            rename: {stripBase:true}
            },
            {
            src: 'node_modules/material-design-icons/iconfont',
            dest: 'assets/fonts',
            rename: {stripBase:true}
            },
            {
            src: 'node_modules/@fontsource/nunito',
            dest: 'assets/fonts',
            rename: {stripBase:true}
            },
            {
            src: 'node_modules/@fontsource/roboto',
            dest: 'assets/fonts',
            rename: {stripBase:true}
            },
            {
            src: 'node_modules/@fontsource/cairo',
            dest: 'assets/fonts',
            rename: {stripBase:true}
            },
            {
            src: 'node_modules/jquery-ui/dist/themes/ui-darkness/images',
            dest: 'assets/css/images',
            rename: {stripBase:true}
            },
            {
            src: 'node_modules/jquery-ui/dist/themes/base/images',
            dest: 'assets/css/images',
            rename: {stripBase:true}
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
