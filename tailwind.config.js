module.exports = {
    mode: 'jit',
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    // These paths are just examples, customize them to match your project structure
    purge: [
        './public/**/*.html',
        './resources/**/**/*.{js,jsx,ts,tsx,vue}',
    ],
    theme: {
        extend: {},
    },
    plugins: [],
}
