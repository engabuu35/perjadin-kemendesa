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
                "background1-pattern": "url('@/images/pattern/2025/backgrounddesktop1.png')",
                "background2-pattern": "url('@/images/pattern/2025/pattern-desktop.png')",
                "motif-pattern": "url('@/images/pattern/2025/pattern.png')",
                "motif2-pattern": "url('@/images/pattern/2025/pattern-login.png')",
                "motif3-pattern": "url('@/images/pattern/2025/motif 3.png')",
                "putih-pattern": "url('@/images/pattern/2025/putih-pattern.png')",
                "footer-wave": "url('@/images/pattern/2025/footer-all.png')",
                "profil-left": "url('@/images/pattern/2025/profil-info.png')",
                "profil-pattern": "url('@/images/pattern/2025/patternprofil.png')",
                "login-kanan": "url('@/images/pattern/2025/login-kanan.png')",
                "login-mobile": "url('@/images/pattern/2025/login-mobile.png')",
                "galeri": "url('@/images/pattern/2025/pattern galeri.png')",
                "linimasa-gradient": "linear-gradient(to bottom, rgba(139,47,75,0.9), rgba(139,47,75,0.9), rgba(30,42,74,0.95)), url('@/images/pattern/2025/motif linimasa.png')",



            }),
            fontFamily: {
                bhinneka: ["Bhinneka", ...defaultTheme.fontFamily.sans],
                archivo: ["Frank Ruhl Libre", ...defaultTheme.fontFamily.sans],
                poppins: ["Poppins", ...defaultTheme.fontFamily.sans],
                nunito: ["Nunito", ...defaultTheme.fontFamily.sans],
                caruban: ["CARUBAN", "cursive"],
                bachelor: ["BACHELOR", "cursive"],
                bachelorReg: ["BACHELOR REGULAR", "cursive"],
                aringgo: ["ARINGGO", "cursive"],
                bohemianSoul: ["BOHEMIAN SOUL", "cursive"],
                brasikaDisplay: ["BRASIKA DISPLAY"],
                chaTime: ["CHATIME"],
                poppins: ["POPPINS"]

            },
            colors: {
                "footer-solid": {
                    DEFAULT: "#3F2A1D",
                },
                "navbar-solid": {
                    DEFAULT: "#D9D9D9",
                },
                "base-theme": {
                    700: "#012E4F",
                    600: "#254B67",
                    500: "#49687F",
                    400: "#778E9E",
                    100: "#CDCAEE",
                },
                2025: {
                    1:"#8B2F4B",
                    2:"#E15585",
                    3:"#1E2A4A",
                    4:"#74C2F0",
                    5:"#F9C46B",
                    login:"#FBE2B8"

                },
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

        keyframes: {
            bounceSlow: {
                '0%, 100%': {
                    transform: 'translateY(-10%)',
                    animationTimingFunction: 'cubic-bezier(0.8, 0, 1, 1)',
                            },
                    '50%': {
                        transform: 'translateY(0)',
                        animationTimingFunction: 'cubic-bezier(0, 0, 0.2, 1)',
                           },
            },
            swayslow1: {
                '0%, 100%': { 
                    transform: 'translateX(-2%)', 
                },
                '50%': { 
                    transform: 'translateX(2%)',  
                },
            },
            swayslow2: {
                '0%, 100%': { 
                    transform: 'translateX(2%)', 
                },
                '50%': { 
                    transform: 'translateX(-2%)', 
                },
            },    
        },
            animation: {
                'swayslow-1': 'swayslow1 8s ease-in-out infinite',
                'swayslow-2': 'swayslow2 8s ease-in-out infinite',
                'bounce-slow': 'bounceSlow 3s infinite',
                       },
    },


    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
};
