module.exports = {
    mode: 'jit',
    content: [
        "./resources/**/*.blade.php",
        './resources/js/components/*.{js,vue}',
        './resources/js/views/*.{js,vue}',
    ],
    // These paths are just examples, customize them to match your project structure
    purge: [
        './public/js/*.{html,js}',
        './public/css/*.css',
    ],
    theme: {
        extend: {},
    },
    plugins: [],
}
