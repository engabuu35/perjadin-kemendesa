import defaultTheme from 'tailwindcss/defaultTheme';
import colors from 'tailwindcss/colors';

export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./vendor/laravel/jetstream/**/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.js",
    ],

    theme: {
        extend: {
            backgroundImage: (theme) => ({
                "footer-pattern": "url('../images/pattern/footer-pattern.png')",
                "sidebar-pattern": "url('../images/pattern/sidebar-pattern.png')",
                "background-pattern": "url('../images/pattern/background-utama.png')",
            }),

            fontFamily: {
                poppins: ["Poppins", ...defaultTheme.fontFamily.sans],

            },
            colors: {
                merah: {
                    700: "#d52d2d",
                    600: "#771518",
                    500: "#D1494D",
                    1: "#800000",
                    2: "#B22222",
                    hover: "#ad2222",
                },
                kuning: {
                    800: "#7C691E",
                    700: "#56470C",
                    600: "#D1AB17",
                    500: "#FAD02C",
                    400: "#9E8831",
                    300: "#DCA565",
                    200: "#F0CC13",
                    hover: "#ccac00",
                    1: "#FFD700",
                },
                hijau: {
                    900: "#084A42",
                    800: "#0B2F2B",
                    700: "#11645A",
                    600: "#1caa0f",
                    1: "#26C218",
                    2: "#22721B",
                },
                abu: {
                    DEFAULT: "#919191",
                    1: "#919191",
                    hover: "#5B5B5B",
                },
                biru: {
                    500: "#44A7C6",
                    400: "#84BCCE",
                },
                putih: {
                    100: "#F5F5DC",
                    200: "#D9D9D9",
                    300: "#B5B5B5",
                    400: "#E9E9E9",
                    500: "#F7F7F7",
                },
                coklat: {
                    1: "#3F2A1D",
                    2: "#8B4513",
                    hover: "#5e2f0d",
                },
                "base-orange": {
                    500: "#FFA500",
                    600: "#e27c00",
                },
                "base-blue": {
                    100: "#D9DCDE",
                    200: "#B2B8BD",
                    300: "#8C959C",
                    400: "#66727B",
                    500: "#3F4E5A",
                    600: "#192B39",
                },
                "base-red": {
                    100: "#EBDCD8",
                    200: "#D6B8B1",
                    300: "#C2958A",
                    400: "#AE7163",
                    500: "#994E3C",
                    600: "#852A15",
                },
                "base-grey": {
                    100: "#EEF0F1",
                    200: "#DEE0E3",
                    300: "#CDD1D6",
                    400: "#BCC2C8",
                    500: "#ACB2BA",
                    600: "#9BA3AC",
                    700: "#CCCCCC",
                },
                "base-yellow": {
                    100: "#F5EFE1",
                    200: "#EADEC3",
                    300: "#E0CEA6",
                    400: "#D6BD88",
                    500: "#CBAD6A",
                    600: "#C19C4C",
                },
                "base-brown": {
                    50: "#f8f6ee",
                    100: "#eeead3",
                    200: "#ded5aa",
                    300: "#cab97a",
                    400: "#baa155",
                    500: "#ab8e47",
                    600: "#93733b",
                    700: "#765732",
                    800: "#64482f",
                    900: "#573e2c",
                    950: "#3f2a1d",
                },
                "base-white": "#F5F5FC",
                violet: colors.violet,
                indigo: colors.indigo,
                cyan: colors.cyan,
                emerald: colors.emerald,
                lime: colors.lime,
                amber: colors.amber,
                orange: colors.orange,
                rose: colors.rose,
                teal: colors.teal,
                sky: colors.sky,
                fuchsia: colors.fuchsia,
                slate: colors.slate,
            },
            maxHeight: {
                custom: "90vh",
            },
            width: {
                "2/9": "22.222222%",
                "47/100": "47%",
            },
            screens: {
                '2xl': '1560px',
            },
        },
    },

    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
};
