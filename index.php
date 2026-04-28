<?php
@session_start();
include('./conexao.php');

// Ordenar por valor decrescente (maior para menor) primeiro
$sql = "
    SELECT r.*, 
           MAX(p.valor) AS maior_premio
      FROM raspadinhas r
 LEFT JOIN raspadinha_premios p ON p.raspadinha_id = r.id
  GROUP BY r.id
  ORDER BY r.valor DESC, r.created_at DESC
";
$cartelas = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $nomeSite;?> - Raspadinhas Online</title>
    <meta name="description" content="Raspe e ganhe prêmios incríveis! PIX na conta instantâneo.">
    
    <!-- Preload Critical Resources -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="assets/style/globalStyles.css?v=<?php echo time();?>"/>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/notiflix@3.2.8/dist/notiflix-aio-3.2.8.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/notiflix@3.2.8/src/notiflix.min.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo $nomeSite;?> - Raspadinhas Online">
    <meta property="og:description" content="Raspe e ganhe prêmios incríveis! PIX na conta instantâneo.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo $urlSite;?>">
    
    <style>
        /* Loading Animation */
        /* Solução definitiva para loading spinner fixo */
        .loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: #0a0a0a;
            z-index: 9999;
            transition: opacity 0.5s ease;
            
            /* Centralização perfeita */
            display: grid;
            place-items: center;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            position: relative;
            /* Remove todas as propriedades de borda do elemento principal */
        }

        .loading-spinner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 3px solid rgba(34, 197, 94, 0.3);
            border-top-color: #22c55e;
            border-radius: 50%;
            
            /* Chaves para rotação sem movimento */
            transform-origin: 50% 50%; /* Centro exato */
            animation: spinFixed 1s linear infinite;
            
            /* Força o elemento a manter posição */
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @keyframes spinFixed {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        /* Alternativa ainda mais simples usando apenas border-image */
        .loading-spinner-simple {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: conic-gradient(#22c55e, rgba(34, 197, 94, 0.3));
            animation: rotateSimple 1s linear infinite;
            position: relative;
            
            /* Máscara para criar o efeito de spinner */
            mask: radial-gradient(circle at center, transparent 18px, black 21px);
            -webkit-mask: radial-gradient(circle at center, transparent 18px, black 21px);
        }

        @keyframes rotateSimple {
            to {
                transform: rotate(360deg);
            }
        }

        /* Versão com CSS puro - mais moderna */
        .loading-spinner-modern {
            width: 50px;
            height: 50px;
            background: 
                conic-gradient(from 0deg, transparent, #22c55e, transparent),
                conic-gradient(from 180deg, transparent, rgba(34, 197, 94, 0.3), transparent);
            border-radius: 50%;
            animation: rotateModern 1s linear infinite;
            position: relative;
            
            /* Efeito de máscara para criar o anel */
            mask: radial-gradient(circle, transparent 17px, black 20px);
            -webkit-mask: radial-gradient(circle, transparent 17px, black 20px);
        }

        @keyframes rotateModern {
            100% {
                transform: rotate(360deg);
            }
        }

        .hidden {
            opacity: 0;
            pointer-events: none;
        }

        /* Reset completo para garantir que não há interferências */
        .loading-screen * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }
        
        /* Parallax effect */
        .parallax-element {
            transform: translateZ(0);
            will-change: transform;
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }
        
        /* Floating elements animation */
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        
        @keyframes floating {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        /* Glowing effect */
        .glow {
            box-shadow: 0 0 20px rgba(34, 197, 94, 0.3);
        }
        
        .glow:hover {
            box-shadow: 0 0 30px rgba(34, 197, 94, 0.5);
        }
        *,:before,:after {
    --tw-border-spacing-x: 0;
    --tw-border-spacing-y: 0;
    --tw-translate-x: 0;
    --tw-translate-y: 0;
    --tw-rotate: 0;
    --tw-skew-x: 0;
    --tw-skew-y: 0;
    --tw-scale-x: 1;
    --tw-scale-y: 1;
    --tw-pan-x: ;
    --tw-pan-y: ;
    --tw-pinch-zoom: ;
    --tw-scroll-snap-strictness: proximity;
    --tw-gradient-from-position: ;
    --tw-gradient-via-position: ;
    --tw-gradient-to-position: ;
    --tw-ordinal: ;
    --tw-slashed-zero: ;
    --tw-numeric-figure: ;
    --tw-numeric-spacing: ;
    --tw-numeric-fraction: ;
    --tw-ring-inset: ;
    --tw-ring-offset-width: 0px;
    --tw-ring-offset-color: #fff;
    --tw-ring-color: rgb(59 130 246 / .5);
    --tw-ring-offset-shadow: 0 0 #0000;
    --tw-ring-shadow: 0 0 #0000;
    --tw-shadow: 0 0 #0000;
    --tw-shadow-colored: 0 0 #0000;
    --tw-blur: ;
    --tw-brightness: ;
    --tw-contrast: ;
    --tw-grayscale: ;
    --tw-hue-rotate: ;
    --tw-invert: ;
    --tw-saturate: ;
    --tw-sepia: ;
    --tw-drop-shadow: ;
    --tw-backdrop-blur: ;
    --tw-backdrop-brightness: ;
    --tw-backdrop-contrast: ;
    --tw-backdrop-grayscale: ;
    --tw-backdrop-hue-rotate: ;
    --tw-backdrop-invert: ;
    --tw-backdrop-opacity: ;
    --tw-backdrop-saturate: ;
    --tw-backdrop-sepia: ;
    --tw-contain-size: ;
    --tw-contain-layout: ;
    --tw-contain-paint: ;
    --tw-contain-style:
}

::backdrop {
    --tw-border-spacing-x: 0;
    --tw-border-spacing-y: 0;
    --tw-translate-x: 0;
    --tw-translate-y: 0;
    --tw-rotate: 0;
    --tw-skew-x: 0;
    --tw-skew-y: 0;
    --tw-scale-x: 1;
    --tw-scale-y: 1;
    --tw-pan-x: ;
    --tw-pan-y: ;
    --tw-pinch-zoom: ;
    --tw-scroll-snap-strictness: proximity;
    --tw-gradient-from-position: ;
    --tw-gradient-via-position: ;
    --tw-gradient-to-position: ;
    --tw-ordinal: ;
    --tw-slashed-zero: ;
    --tw-numeric-figure: ;
    --tw-numeric-spacing: ;
    --tw-numeric-fraction: ;
    --tw-ring-inset: ;
    --tw-ring-offset-width: 0px;
    --tw-ring-offset-color: #fff;
    --tw-ring-color: rgb(59 130 246 / .5);
    --tw-ring-offset-shadow: 0 0 #0000;
    --tw-ring-shadow: 0 0 #0000;
    --tw-shadow: 0 0 #0000;
    --tw-shadow-colored: 0 0 #0000;
    --tw-blur: ;
    --tw-brightness: ;
    --tw-contrast: ;
    --tw-grayscale: ;
    --tw-hue-rotate: ;
    --tw-invert: ;
    --tw-saturate: ;
    --tw-sepia: ;
    --tw-drop-shadow: ;
    --tw-backdrop-blur: ;
    --tw-backdrop-brightness: ;
    --tw-backdrop-contrast: ;
    --tw-backdrop-grayscale: ;
    --tw-backdrop-hue-rotate: ;
    --tw-backdrop-invert: ;
    --tw-backdrop-opacity: ;
    --tw-backdrop-saturate: ;
    --tw-backdrop-sepia: ;
    --tw-contain-size: ;
    --tw-contain-layout: ;
    --tw-contain-paint: ;
    --tw-contain-style:
}

*,:before,:after {
    box-sizing: border-box;
    border-width: 0;
    border-style: solid;
    border-color: #e5e7eb
}

:before,:after {
    --tw-content: ""
}

html,:host {
    line-height: 1.5;
    -webkit-text-size-adjust: 100%;
    -moz-tab-size: 4;
    -o-tab-size: 4;
    tab-size: 4;
    font-family: Poppins,ui-sans-serif,system-ui;
    font-feature-settings: normal;
    font-variation-settings: normal;
    -webkit-tap-highlight-color: transparent
}

body {
    margin: 0;
    line-height: inherit
}

hr {
    height: 0;
    color: inherit;
    border-top-width: 1px
}

abbr:where([title]) {
    -webkit-text-decoration: underline dotted;
    text-decoration: underline dotted
}

h1,h2,h3,h4,h5,h6 {
    font-size: inherit;
    font-weight: inherit
}

a {
    color: inherit;
    text-decoration: inherit
}

b,strong {
    font-weight: bolder
}

code,kbd,samp,pre {
    font-family: ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,Liberation Mono,Courier New,monospace;
    font-feature-settings: normal;
    font-variation-settings: normal;
    font-size: 1em
}

small {
    font-size: 80%
}

sub,sup {
    font-size: 75%;
    line-height: 0;
    position: relative;
    vertical-align: baseline
}

sub {
    bottom: -.25em
}

sup {
    top: -.5em
}

table {
    text-indent: 0;
    border-color: inherit;
    border-collapse: collapse
}

button,input,optgroup,select,textarea {
    font-family: inherit;
    font-feature-settings: inherit;
    font-variation-settings: inherit;
    font-size: 100%;
    font-weight: inherit;
    line-height: inherit;
    letter-spacing: inherit;
    color: inherit;
    margin: 0;
    padding: 0
}

button,select {
    text-transform: none
}

button,input:where([type=button]),input:where([type=reset]),input:where([type=submit]) {
    -webkit-appearance: button;
    background-color: transparent;
    background-image: none
}

:-moz-focusring {
    outline: auto
}

:-moz-ui-invalid {
    box-shadow: none
}

progress {
    vertical-align: baseline
}

::-webkit-inner-spin-button,::-webkit-outer-spin-button {
    height: auto
}

[type=search] {
    -webkit-appearance: textfield;
    outline-offset: -2px
}

::-webkit-search-decoration {
    -webkit-appearance: none
}

::-webkit-file-upload-button {
    -webkit-appearance: button;
    font: inherit
}

summary {
    display: list-item
}

blockquote,dl,dd,h1,h2,h3,h4,h5,h6,hr,figure,p,pre {
    margin: 0
}

fieldset {
    margin: 0;
    padding: 0
}

legend {
    padding: 0
}

ol,ul,menu {
    list-style: none;
    margin: 0;
    padding: 0
}

dialog {
    padding: 0
}

textarea {
    resize: vertical
}

input::-moz-placeholder,textarea::-moz-placeholder {
    opacity: 1;
    color: #9ca3af
}

input::placeholder,textarea::placeholder {
    opacity: 1;
    color: #9ca3af
}

button,[role=button] {
    cursor: pointer
}

:disabled {
    cursor: default
}

img,svg,video,canvas,audio,iframe,embed,object {
    display: block;
    vertical-align: middle
}

img,video {
    max-width: 100%;
    height: auto
}

[hidden]:where(:not([hidden=until-found])) {
    display: none
}

.container {
    width: 100%
}

@media (min-width: 640px) {
    .container {
        max-width:640px
    }
}

@media (min-width: 768px) {
    .container {
        max-width:768px
    }
}

@media (min-width: 1024px) {
    .container {
        max-width:1024px
    }
}

@media (min-width: 1280px) {
    .container {
        max-width:1280px
    }
}

@media (min-width: 1536px) {
    .container {
        max-width:1536px
    }
}

.pointer-events-none {
    pointer-events: none
}

.fixed {
    position: fixed
}

.absolute {
    position: absolute
}

.relative {
    position: relative
}

.inset-0 {
    top: 0;
    right: 0;
    bottom: 0;
    left: 0
}

.inset-4 {
    top: 1rem;
    right: 1rem;
    bottom: 1rem;
    left: 1rem
}

.-bottom-1 {
    bottom: -.25rem
}

.-bottom-2 {
    bottom: -.5rem
}

.-left-2 {
    left: -.5rem
}

.-left-3 {
    left: -.75rem
}

.-right-1 {
    right: -.25rem
}

.-right-3 {
    right: -.75rem
}

.-top-1 {
    top: -.25rem
}

.-top-2 {
    top: -.5rem
}

.bottom-0 {
    bottom: 0
}

.bottom-2 {
    bottom: .5rem
}

.bottom-3 {
    bottom: .75rem
}

.bottom-4 {
    bottom: 1rem
}

.bottom-6 {
    bottom: 1.5rem
}

.left-0 {
    left: 0
}

.left-1\/2 {
    left: 50%
}

.left-12 {
    left: 3rem
}

.left-2 {
    left: .5rem
}

.left-2\/4 {
    left: 50%
}

.left-20 {
    left: 5rem
}

.left-3 {
    left: .75rem
}

.left-4 {
    left: 1rem
}

.right-0 {
    right: 0
}

.right-2 {
    right: .5rem
}

.right-3 {
    right: .75rem
}

.right-4 {
    right: 1rem
}

.right-6 {
    right: 1.5rem
}

.top-0 {
    top: 0
}

.top-1\.5 {
    top: .375rem
}

.top-1\/2 {
    top: 50%
}

.top-2 {
    top: .5rem
}

.top-3 {
    top: .75rem
}

.top-4 {
    top: 1rem
}

.top-6 {
    top: 1.5rem
}

.top-full {
    top: 100%
}

.z-0 {
    z-index: 0
}

.z-10 {
    z-index: 10
}

.z-50 {
    z-index: 50
}

.z-\[9999\] {
    z-index: 9999
}

.col-span-1 {
    grid-column: span 1 / span 1
}

.col-span-3 {
    grid-column: span 3 / span 3
}

.-m-6 {
    margin: -1.5rem
}

.mx-auto {
    margin-left: auto;
    margin-right: auto
}

.my-6 {
    margin-top: 1.5rem;
    margin-bottom: 1.5rem
}

.-ml-1 {
    margin-left: -.25rem
}

.mb-0 {
    margin-bottom: 0
}

.mb-1 {
    margin-bottom: .25rem
}

.mb-2 {
    margin-bottom: .5rem
}

.mb-2\.5 {
    margin-bottom: .625rem
}

.mb-3 {
    margin-bottom: .75rem
}

.mb-4 {
    margin-bottom: 1rem
}

.mb-6 {
    margin-bottom: 1.5rem
}

.mb-8 {
    margin-bottom: 2rem
}

.ml-0\.5 {
    margin-left: .125rem
}

.ml-1 {
    margin-left: .25rem
}

.ml-2 {
    margin-left: .5rem
}

.ml-4 {
    margin-left: 1rem
}

.ml-auto {
    margin-left: auto
}

.mr-1 {
    margin-right: .25rem
}

.mr-2 {
    margin-right: .5rem
}

.mr-4 {
    margin-right: 1rem
}

.mt-0\.5 {
    margin-top: .125rem
}

.mt-1 {
    margin-top: .25rem
}

.mt-12 {
    margin-top: 3rem
}

.mt-2 {
    margin-top: .5rem
}

.mt-3 {
    margin-top: .75rem
}

.mt-4 {
    margin-top: 1rem
}

.mt-6 {
    margin-top: 1.5rem
}

.mt-8 {
    margin-top: 2rem
}

.line-clamp-1 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 1
}

.line-clamp-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2
}

.block {
    display: block
}

.inline-block {
    display: inline-block
}

.inline {
    display: inline
}

.flex {
    display: flex
}

.inline-flex {
    display: inline-flex
}

.table {
    display: table
}

.grid {
    display: grid
}

.hidden {
    display: none
}

.aspect-\[5\/1\] {
    aspect-ratio: 5/1
}

.aspect-square {
    aspect-ratio: 1 / 1
}

.size-12 {
    width: 3rem;
    height: 3rem
}

.size-24 {
    width: 6rem;
    height: 6rem
}

.size-3 {
    width: .75rem;
    height: .75rem
}

.size-3\.5 {
    width: .875rem;
    height: .875rem
}

.size-4 {
    width: 1rem;
    height: 1rem
}

.size-5 {
    width: 1.25rem;
    height: 1.25rem
}

.size-6 {
    width: 1.5rem;
    height: 1.5rem
}

.size-8 {
    width: 2rem;
    height: 2rem
}

.size-\[0\.64rem\] {
    width: .64rem;
    height: .64rem
}

.size-\[1\.6rem\] {
    width: 1.6rem;
    height: 1.6rem
}

.size-full {
    width: 100%;
    height: 100%
}

.h-1 {
    height: .25rem
}

.h-1\.5 {
    height: .375rem
}

.h-10 {
    height: 2.5rem
}

.h-12 {
    height: 3rem
}

.h-14 {
    height: 3.5rem
}

.h-16 {
    height: 4rem
}

.h-2 {
    height: .5rem
}

.h-20 {
    height: 5rem
}

.h-24 {
    height: 6rem
}

.h-28 {
    height: 7rem
}

.h-3 {
    height: .75rem
}

.h-32 {
    height: 8rem
}

.h-4 {
    height: 1rem
}

.h-40 {
    height: 10rem
}

.h-48 {
    height: 12rem
}

.h-5 {
    height: 1.25rem
}

.h-6 {
    height: 1.5rem
}

.h-64 {
    height: 16rem
}

.h-8 {
    height: 2rem
}

.h-9 {
    height: 2.25rem
}

.h-\[42px\] {
    height: 42px
}

.h-\[72px\] {
    height: 72px
}

.h-auto {
    height: auto
}

.h-full {
    height: 100%
}

.h-px {
    height: 1px
}

.h-screen {
    height: 100vh
}

.h-svh {
    height: 100svh
}

.max-h-\[13\.604rem\] {
    max-height: 13.604rem
}

.max-h-\[90vh\] {
    max-height: 90vh
}

.min-h-\[600px\] {
    min-height: 600px
}

.min-h-\[80vh\] {
    min-height: 80vh
}

.min-h-screen {
    min-height: 100vh
}

.w-1 {
    width: .25rem
}

.w-1\.5 {
    width: .375rem
}

.w-12 {
    width: 3rem
}

.w-16 {
    width: 4rem
}

.w-2 {
    width: .5rem
}

.w-20 {
    width: 5rem
}

.w-28 {
    width: 7rem
}

.w-3 {
    width: .75rem
}

.w-32 {
    width: 8rem
}

.w-36 {
    width: 9rem
}

.w-4 {
    width: 1rem
}

.w-48 {
    width: 12rem
}

.w-5 {
    width: 1.25rem
}

.w-56 {
    width: 14rem
}

.w-6 {
    width: 1.5rem
}

.w-8 {
    width: 2rem
}

.w-fit {
    width: -moz-fit-content;
    width: fit-content
}

.w-full {
    width: 100%
}

.w-px {
    width: 1px
}

.min-w-0 {
    min-width: 0px
}

.min-w-\[220px\] {
    min-width: 220px
}

.min-w-\[260px\] {
    min-width: 260px
}

.min-w-\[9rem\] {
    min-width: 9rem
}

.max-w-2xl {
    max-width: 42rem
}

.max-w-4xl {
    max-width: 56rem
}

.max-w-7xl {
    max-width: 80rem
}

.max-w-\[26\.083rem\] {
    max-width: 26.083rem
}

.max-w-\[360px\] {
    max-width: 360px
}

.max-w-\[9rem\] {
    max-width: 9rem
}

.max-w-lg {
    max-width: 32rem
}

.max-w-md {
    max-width: 28rem
}

.max-w-sm {
    max-width: 24rem
}

.max-w-xs {
    max-width: 20rem
}

.flex-1 {
    flex: 1 1 0%
}

.flex-shrink-0,.shrink-0 {
    flex-shrink: 0
}

.-translate-x-1\/2,.-translate-x-2\/4 {
    --tw-translate-x: -50%;
    transform: translate(var(--tw-translate-x),var(--tw-translate-y)) rotate(var(--tw-rotate)) skew(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))
}

.-translate-x-full {
    --tw-translate-x: -100%;
    transform: translate(var(--tw-translate-x),var(--tw-translate-y)) rotate(var(--tw-rotate)) skew(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))
}

.-translate-y-1\/2 {
    --tw-translate-y: -50%;
    transform: translate(var(--tw-translate-x),var(--tw-translate-y)) rotate(var(--tw-rotate)) skew(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))
}

.-translate-y-10 {
    --tw-translate-y: -2.5rem;
    transform: translate(var(--tw-translate-x),var(--tw-translate-y)) rotate(var(--tw-rotate)) skew(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))
}

.-translate-y-2\/4 {
    --tw-translate-y: -50%;
    transform: translate(var(--tw-translate-x),var(--tw-translate-y)) rotate(var(--tw-rotate)) skew(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))
}

.-translate-y-\[1\.25rem\] {
    --tw-translate-y: -1.25rem;
    transform: translate(var(--tw-translate-x),var(--tw-translate-y)) rotate(var(--tw-rotate)) skew(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))
}

.translate-x-10 {
    --tw-translate-x: 2.5rem;
    transform: translate(var(--tw-translate-x),var(--tw-translate-y)) rotate(var(--tw-rotate)) skew(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))
}

.rotate-180 {
    --tw-rotate: 180deg;
    transform: translate(var(--tw-translate-x),var(--tw-translate-y)) rotate(var(--tw-rotate)) skew(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))
}

.scale-100 {
    --tw-scale-x: 1;
    --tw-scale-y: 1;
    transform: translate(var(--tw-translate-x),var(--tw-translate-y)) rotate(var(--tw-rotate)) skew(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))
}

.scale-110 {
    --tw-scale-x: 1.1;
    --tw-scale-y: 1.1;
    transform: translate(var(--tw-translate-x),var(--tw-translate-y)) rotate(var(--tw-rotate)) skew(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))
}

.transform {
    transform: translate(var(--tw-translate-x),var(--tw-translate-y)) rotate(var(--tw-rotate)) skew(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))
}

@keyframes bounce {
    0%,to {
        transform: translateY(-25%);
        animation-timing-function: cubic-bezier(.8,0,1,1)
    }

    50% {
        transform: none;
        animation-timing-function: cubic-bezier(0,0,.2,1)
    }
}

.animate-bounce {
    animation: bounce 1s infinite
}

@keyframes ping {
    75%,to {
        transform: scale(2);
        opacity: 0
    }
}

.animate-ping {
    animation: ping 1s cubic-bezier(0,0,.2,1) infinite
}

@keyframes pulse {
    50% {
        opacity: .5
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(.4,0,.6,1) infinite
}

@keyframes spin {
    to {
        transform: rotate(360deg)
    }
}

.animate-spin {
    animation: spin 1s linear infinite
}

.cursor-pointer {
    cursor: pointer
}

.select-none {
    -webkit-user-select: none;
    -moz-user-select: none;
    user-select: none
}

.resize-none {
    resize: none
}

.appearance-none {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none
}

.grid-cols-1 {
    grid-template-columns: repeat(1,minmax(0,1fr))
}

.grid-cols-2 {
    grid-template-columns: repeat(2,minmax(0,1fr))
}

.grid-cols-3 {
    grid-template-columns: repeat(3,minmax(0,1fr))
}

.grid-cols-5 {
    grid-template-columns: repeat(5,minmax(0,1fr))
}

.flex-row {
    flex-direction: row
}

.flex-col {
    flex-direction: column
}

.flex-wrap {
    flex-wrap: wrap
}

.items-start {
    align-items: flex-start
}

.items-end {
    align-items: flex-end
}

.items-center {
    align-items: center
}

.justify-start {
    justify-content: flex-start
}

.justify-end {
    justify-content: flex-end
}

.justify-center {
    justify-content: center
}

.justify-between {
    justify-content: space-between
}

.gap-1 {
    gap: .25rem
}

.gap-1\.5 {
    gap: .375rem
}

.gap-2 {
    gap: .5rem
}

.gap-3 {
    gap: .75rem
}

.gap-3\.5 {
    gap: .875rem
}

.gap-4 {
    gap: 1rem
}

.gap-6 {
    gap: 1.5rem
}

.gap-7 {
    gap: 1.75rem
}

.gap-8 {
    gap: 2rem
}

.space-x-2>:not([hidden])~:not([hidden]) {
    --tw-space-x-reverse: 0;
    margin-right: calc(.5rem * var(--tw-space-x-reverse));
    margin-left: calc(.5rem * calc(1 - var(--tw-space-x-reverse)))
}

.space-x-3>:not([hidden])~:not([hidden]) {
    --tw-space-x-reverse: 0;
    margin-right: calc(.75rem * var(--tw-space-x-reverse));
    margin-left: calc(.75rem * calc(1 - var(--tw-space-x-reverse)))
}

.space-x-4>:not([hidden])~:not([hidden]) {
    --tw-space-x-reverse: 0;
    margin-right: calc(1rem * var(--tw-space-x-reverse));
    margin-left: calc(1rem * calc(1 - var(--tw-space-x-reverse)))
}

.space-x-6>:not([hidden])~:not([hidden]) {
    --tw-space-x-reverse: 0;
    margin-right: calc(1.5rem * var(--tw-space-x-reverse));
    margin-left: calc(1.5rem * calc(1 - var(--tw-space-x-reverse)))
}

.space-x-8>:not([hidden])~:not([hidden]) {
    --tw-space-x-reverse: 0;
    margin-right: calc(2rem * var(--tw-space-x-reverse));
    margin-left: calc(2rem * calc(1 - var(--tw-space-x-reverse)))
}

.space-y-1>:not([hidden])~:not([hidden]) {
    --tw-space-y-reverse: 0;
    margin-top: calc(.25rem * calc(1 - var(--tw-space-y-reverse)));
    margin-bottom: calc(.25rem * var(--tw-space-y-reverse))
}

.space-y-2>:not([hidden])~:not([hidden]) {
    --tw-space-y-reverse: 0;
    margin-top: calc(.5rem * calc(1 - var(--tw-space-y-reverse)));
    margin-bottom: calc(.5rem * var(--tw-space-y-reverse))
}

.space-y-3>:not([hidden])~:not([hidden]) {
    --tw-space-y-reverse: 0;
    margin-top: calc(.75rem * calc(1 - var(--tw-space-y-reverse)));
    margin-bottom: calc(.75rem * var(--tw-space-y-reverse))
}

.space-y-4>:not([hidden])~:not([hidden]) {
    --tw-space-y-reverse: 0;
    margin-top: calc(1rem * calc(1 - var(--tw-space-y-reverse)));
    margin-bottom: calc(1rem * var(--tw-space-y-reverse))
}

.space-y-6>:not([hidden])~:not([hidden]) {
    --tw-space-y-reverse: 0;
    margin-top: calc(1.5rem * calc(1 - var(--tw-space-y-reverse)));
    margin-bottom: calc(1.5rem * var(--tw-space-y-reverse))
}

.space-y-8>:not([hidden])~:not([hidden]) {
    --tw-space-y-reverse: 0;
    margin-top: calc(2rem * calc(1 - var(--tw-space-y-reverse)));
    margin-bottom: calc(2rem * var(--tw-space-y-reverse))
}

.divide-y>:not([hidden])~:not([hidden]) {
    --tw-divide-y-reverse: 0;
    border-top-width: calc(1px * calc(1 - var(--tw-divide-y-reverse)));
    border-bottom-width: calc(1px * var(--tw-divide-y-reverse))
}

.divide-gray-700>:not([hidden])~:not([hidden]) {
    --tw-divide-opacity: 1;
    border-color: rgb(55 65 81 / var(--tw-divide-opacity, 1))
}

.self-center {
    align-self: center
}

.overflow-hidden {
    overflow: hidden
}

.overflow-x-auto {
    overflow-x: auto
}

.overflow-y-auto {
    overflow-y: auto
}

.truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap
}

.text-ellipsis {
    text-overflow: ellipsis
}

.whitespace-nowrap {
    white-space: nowrap
}

.text-nowrap {
    text-wrap: nowrap
}

.break-words {
    overflow-wrap: break-word
}

.break-all {
    word-break: break-all
}

.rounded {
    border-radius: .25rem
}

.rounded-2xl {
    border-radius: 1rem
}

.rounded-3xl {
    border-radius: 1.5rem
}

.rounded-full {
    border-radius: 9999px
}

.rounded-lg {
    border-radius: .5rem
}

.rounded-md {
    border-radius: .375rem
}

.rounded-sm {
    border-radius: .125rem
}

.rounded-xl {
    border-radius: .75rem
}

.rounded-t-3xl {
    border-top-left-radius: 1.5rem;
    border-top-right-radius: 1.5rem
}

.rounded-t-lg {
    border-top-left-radius: .5rem;
    border-top-right-radius: .5rem
}

.border {
    border-width: 1px
}

.border-2 {
    border-width: 2px
}

.border-4 {
    border-width: 4px
}

.border-b {
    border-bottom-width: 1px
}

.border-b-2 {
    border-bottom-width: 2px
}

.border-t {
    border-top-width: 1px
}

.border-dashed {
    border-style: dashed
}

.border-\[\#00FF8830\] {
    border-color: #00ff8830
}

.border-\[\#00FF88\] {
    --tw-border-opacity: 1;
    border-color: rgb(0 255 136 / var(--tw-border-opacity, 1))
}

.border-\[\#00FF88\]\/30 {
    border-color: #00ff884d
}

.border-\[\#00FF88\]\/60 {
    border-color: #0f89
}

.border-\[\#262626\] {
    --tw-border-opacity: 1;
    border-color: rgb(38 38 38 / var(--tw-border-opacity, 1))
}

.border-\[\#404040\] {
    --tw-border-opacity: 1;
    border-color: rgb(64 64 64 / var(--tw-border-opacity, 1))
}

.border-\[\#EF444430\] {
    border-color: #ef444430
}

.border-\[\#FFD700\] {
    --tw-border-opacity: 1;
    border-color: rgb(255 215 0 / var(--tw-border-opacity, 1))
}

.border-black {
    --tw-border-opacity: 1;
    border-color: rgb(0 0 0 / var(--tw-border-opacity, 1))
}

.border-blue-500\/30 {
    border-color: #3b82f64d
}

.border-current {
    border-color: currentColor
}

.border-gray-200 {
    --tw-border-opacity: 1;
    border-color: rgb(229 231 235 / var(--tw-border-opacity, 1))
}

.border-gray-400 {
    --tw-border-opacity: 1;
    border-color: rgb(156 163 175 / var(--tw-border-opacity, 1))
}

.border-gray-600 {
    --tw-border-opacity: 1;
    border-color: rgb(75 85 99 / var(--tw-border-opacity, 1))
}

.border-gray-700 {
    --tw-border-opacity: 1;
    border-color: rgb(55 65 81 / var(--tw-border-opacity, 1))
}

.border-gray-700\/50 {
    border-color: #37415180
}

.border-gray-800 {
    --tw-border-opacity: 1;
    border-color: rgb(31 41 55 / var(--tw-border-opacity, 1))
}

.border-green-500\/30 {
    border-color: #22c55e4d
}

.border-red-200 {
    --tw-border-opacity: 1;
    border-color: rgb(254 202 202 / var(--tw-border-opacity, 1))
}

.border-red-500\/20 {
    border-color: #ef444433
}

.border-red-500\/30 {
    border-color: #ef44444d
}

.border-red-500\/50 {
    border-color: #ef444480
}

.border-transparent {
    border-color: transparent
}

.border-yellow-400 {
    --tw-border-opacity: 1;
    border-color: rgb(250 204 21 / var(--tw-border-opacity, 1))
}

.border-yellow-500\/30 {
    border-color: #eab3084d
}

.border-t-transparent {
    border-top-color: transparent
}

.bg-\[\#00FF8810\] {
    background-color: #00ff8810
}

.bg-\[\#00FF88\] {
    --tw-bg-opacity: 1;
    background-color: rgb(0 255 136 / var(--tw-bg-opacity, 1))
}

.bg-\[\#00FF88\]\/10 {
    background-color: #00ff881a
}

.bg-\[\#00FF88\]\/20 {
    background-color: #0f83
}

.bg-\[\#0a0d0b\] {
    --tw-bg-opacity: 1;
    background-color: rgb(10 13 11 / var(--tw-bg-opacity, 1))
}

.bg-\[\#171717\] {
    --tw-bg-opacity: 1;
    background-color: rgb(23 23 23 / var(--tw-bg-opacity, 1))
}

.bg-\[\#1a1a1a\] {
    --tw-bg-opacity: 1;
    background-color: rgb(26 26 26 / var(--tw-bg-opacity, 1))
}

.bg-\[\#20232a\] {
    --tw-bg-opacity: 1;
    background-color: rgb(32 35 42 / var(--tw-bg-opacity, 1))
}

.bg-\[\#23272f\] {
    --tw-bg-opacity: 1;
    background-color: rgb(35 39 47 / var(--tw-bg-opacity, 1))
}

.bg-\[\#262626\] {
    --tw-bg-opacity: 1;
    background-color: rgb(38 38 38 / var(--tw-bg-opacity, 1))
}

.bg-\[\#EF444410\] {
    background-color: #ef444410
}

.bg-\[\#EF4444\] {
    --tw-bg-opacity: 1;
    background-color: rgb(239 68 68 / var(--tw-bg-opacity, 1))
}

.bg-\[\#FFD70020\] {
    background-color: #ffd70020
}

.bg-\[\#FFD700\] {
    --tw-bg-opacity: 1;
    background-color: rgb(255 215 0 / var(--tw-bg-opacity, 1))
}

.bg-black {
    --tw-bg-opacity: 1;
    background-color: rgb(0 0 0 / var(--tw-bg-opacity, 1))
}

.bg-black\/20 {
    background-color: #0003
}

.bg-black\/50 {
    background-color: #00000080
}

.bg-black\/80 {
    background-color: #000c
}

.bg-black\/90 {
    background-color: #000000e6
}

.bg-blue-400 {
    --tw-bg-opacity: 1;
    background-color: rgb(96 165 250 / var(--tw-bg-opacity, 1))
}

.bg-blue-500 {
    --tw-bg-opacity: 1;
    background-color: rgb(59 130 246 / var(--tw-bg-opacity, 1))
}

.bg-blue-500\/10 {
    background-color: #3b82f61a
}

.bg-blue-500\/20 {
    background-color: #3b82f633
}

.bg-blue-600 {
    --tw-bg-opacity: 1;
    background-color: rgb(37 99 235 / var(--tw-bg-opacity, 1))
}

.bg-blue-900\/20 {
    background-color: #1e3a8a33
}

.bg-gray-400 {
    --tw-bg-opacity: 1;
    background-color: rgb(156 163 175 / var(--tw-bg-opacity, 1))
}

.bg-gray-50 {
    --tw-bg-opacity: 1;
    background-color: rgb(249 250 251 / var(--tw-bg-opacity, 1))
}

.bg-gray-500 {
    --tw-bg-opacity: 1;
    background-color: rgb(107 114 128 / var(--tw-bg-opacity, 1))
}

.bg-gray-600 {
    --tw-bg-opacity: 1;
    background-color: rgb(75 85 99 / var(--tw-bg-opacity, 1))
}

.bg-gray-700 {
    --tw-bg-opacity: 1;
    background-color: rgb(55 65 81 / var(--tw-bg-opacity, 1))
}

.bg-gray-700\/30 {
    background-color: #3741514d
}

.bg-gray-700\/50 {
    background-color: #37415180
}

.bg-gray-800 {
    --tw-bg-opacity: 1;
    background-color: rgb(31 41 55 / var(--tw-bg-opacity, 1))
}

.bg-gray-800\/30 {
    background-color: #1f29374d
}

.bg-gray-800\/40 {
    background-color: #1f293766
}

.bg-gray-800\/50 {
    background-color: #1f293780
}

.bg-gray-900 {
    --tw-bg-opacity: 1;
    background-color: rgb(17 24 39 / var(--tw-bg-opacity, 1))
}

.bg-green-400 {
    --tw-bg-opacity: 1;
    background-color: rgb(74 222 128 / var(--tw-bg-opacity, 1))
}

.bg-green-50 {
    --tw-bg-opacity: 1;
    background-color: rgb(240 253 244 / var(--tw-bg-opacity, 1))
}

.bg-green-500 {
    --tw-bg-opacity: 1;
    background-color: rgb(34 197 94 / var(--tw-bg-opacity, 1))
}

.bg-green-500\/20 {
    background-color: #22c55e33
}

.bg-green-500\/80 {
    background-color: #22c55ecc
}

.bg-green-600 {
    --tw-bg-opacity: 1;
    background-color: rgb(22 163 74 / var(--tw-bg-opacity, 1))
}

.bg-green-600\/20 {
    background-color: #16a34a33
}

.bg-green-900\/20 {
    background-color: #14532d33
}

.bg-orange-500 {
    --tw-bg-opacity: 1;
    background-color: rgb(249 115 22 / var(--tw-bg-opacity, 1))
}

.bg-orange-500\/80 {
    background-color: #f97316cc
}

.bg-orange-600 {
    --tw-bg-opacity: 1;
    background-color: rgb(234 88 12 / var(--tw-bg-opacity, 1))
}

.bg-purple-400 {
    --tw-bg-opacity: 1;
    background-color: rgb(192 132 252 / var(--tw-bg-opacity, 1))
}

.bg-purple-500\/10 {
    background-color: #a855f71a
}

.bg-purple-500\/20 {
    background-color: #a855f733
}

.bg-red-50 {
    --tw-bg-opacity: 1;
    background-color: rgb(254 242 242 / var(--tw-bg-opacity, 1))
}

.bg-red-500 {
    --tw-bg-opacity: 1;
    background-color: rgb(239 68 68 / var(--tw-bg-opacity, 1))
}

.bg-red-500\/10 {
    background-color: #ef44441a
}

.bg-red-500\/20 {
    background-color: #ef444433
}

.bg-red-500\/80 {
    background-color: #ef4444cc
}

.bg-red-600 {
    --tw-bg-opacity: 1;
    background-color: rgb(220 38 38 / var(--tw-bg-opacity, 1))
}

.bg-red-600\/20 {
    background-color: #dc262633
}

.bg-red-900\/20 {
    background-color: #7f1d1d33
}

.bg-transparent {
    background-color: transparent
}

.bg-white {
    --tw-bg-opacity: 1;
    background-color: rgb(255 255 255 / var(--tw-bg-opacity, 1))
}

.bg-white\/50 {
    background-color: #ffffff80
}

.bg-yellow-400 {
    --tw-bg-opacity: 1;
    background-color: rgb(250 204 21 / var(--tw-bg-opacity, 1))
}

.bg-yellow-400\/10 {
    background-color: #facc151a
}

.bg-yellow-50 {
    --tw-bg-opacity: 1;
    background-color: rgb(254 252 232 / var(--tw-bg-opacity, 1))
}

.bg-yellow-500 {
    --tw-bg-opacity: 1;
    background-color: rgb(234 179 8 / var(--tw-bg-opacity, 1))
}

.bg-yellow-500\/10 {
    background-color: #eab3081a
}

.bg-yellow-500\/20 {
    background-color: #eab30833
}

.bg-yellow-600 {
    --tw-bg-opacity: 1;
    background-color: rgb(202 138 4 / var(--tw-bg-opacity, 1))
}

.bg-yellow-900\/20 {
    background-color: #713f1233
}

.bg-opacity-0 {
    --tw-bg-opacity: 0
}

.bg-opacity-70 {
    --tw-bg-opacity: .7
}

.bg-gradient-to-b {
    background-image: linear-gradient(to bottom,var(--tw-gradient-stops))
}

.bg-gradient-to-br {
    background-image: linear-gradient(to bottom right,var(--tw-gradient-stops))
}

.bg-gradient-to-l {
    background-image: linear-gradient(to left,var(--tw-gradient-stops))
}

.bg-gradient-to-r {
    background-image: linear-gradient(to right,var(--tw-gradient-stops))
}

.bg-gradient-to-t {
    background-image: linear-gradient(to top,var(--tw-gradient-stops))
}

.from-\[\#00FF88\] {
    --tw-gradient-from: #00FF88 var(--tw-gradient-from-position);
    --tw-gradient-to: rgb(0 255 136 / 0) var(--tw-gradient-to-position);
    --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to)
}

.from-\[\#00FF88\]\/10 {
    --tw-gradient-from: rgb(0 255 136 / .1) var(--tw-gradient-from-position);
    --tw-gradient-to: rgb(0 255 136 / 0) var(--tw-gradient-to-position);
    --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to)
}

.from-\[\#171717\] {
    --tw-gradient-from: #171717 var(--tw-gradient-from-position);
    --tw-gradient-to: rgb(23 23 23 / 0) var(--tw-gradient-to-position);
    --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to)
}

.from-\[\#262626\] {
    --tw-gradient-from: #262626 var(--tw-gradient-from-position);
    --tw-gradient-to: rgb(38 38 38 / 0) var(--tw-gradient-to-position);
    --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to)
}

.from-black\/60 {
    --tw-gradient-from: rgb(0 0 0 / .6) var(--tw-gradient-from-position);
    --tw-gradient-to: rgb(0 0 0 / 0) var(--tw-gradient-to-position);
    --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to)
}

.from-gray-700 {
    --tw-gradient-from: #374151 var(--tw-gradient-from-position);
    --tw-gradient-to: rgb(55 65 81 / 0) var(--tw-gradient-to-position);
    --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to)
}

.from-gray-900 {
    --tw-gradient-from: #111827 var(--tw-gradient-from-position);
    --tw-gradient-to: rgb(17 24 39 / 0) var(--tw-gradient-to-position);
    --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to)
}

.from-purple-500 {
    --tw-gradient-from: #a855f7 var(--tw-gradient-from-position);
    --tw-gradient-to: rgb(168 85 247 / 0) var(--tw-gradient-to-position);
    --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to)
}

.from-transparent {
    --tw-gradient-from: transparent var(--tw-gradient-from-position);
    --tw-gradient-to: rgb(0 0 0 / 0) var(--tw-gradient-to-position);
    --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to)
}

.from-yellow-400 {
    --tw-gradient-from: #facc15 var(--tw-gradient-from-position);
    --tw-gradient-to: rgb(250 204 21 / 0) var(--tw-gradient-to-position);
    --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to)
}

.from-yellow-500\/20 {
    --tw-gradient-from: rgb(234 179 8 / .2) var(--tw-gradient-from-position);
    --tw-gradient-to: rgb(234 179 8 / 0) var(--tw-gradient-to-position);
    --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to)
}

.via-\[\#00D4AA\] {
    --tw-gradient-to: rgb(0 212 170 / 0) var(--tw-gradient-to-position);
    --tw-gradient-stops: var(--tw-gradient-from), #00D4AA var(--tw-gradient-via-position), var(--tw-gradient-to)
}

.via-\[\#232323\] {
    --tw-gradient-to: rgb(35 35 35 / 0) var(--tw-gradient-to-position);
    --tw-gradient-stops: var(--tw-gradient-from), #232323 var(--tw-gradient-via-position), var(--tw-gradient-to)
}

.via-gray-600 {
    --tw-gradient-to: rgb(75 85 99 / 0) var(--tw-gradient-to-position);
    --tw-gradient-stops: var(--tw-gradient-from), #4b5563 var(--tw-gradient-via-position), var(--tw-gradient-to)
}

.via-gray-800 {
    --tw-gradient-to: rgb(31 41 55 / 0) var(--tw-gradient-to-position);
    --tw-gradient-stops: var(--tw-gradient-from), #1f2937 var(--tw-gradient-via-position), var(--tw-gradient-to)
}

.via-white\/20 {
    --tw-gradient-to: rgb(255 255 255 / 0) var(--tw-gradient-to-position);
    --tw-gradient-stops: var(--tw-gradient-from), rgb(255 255 255 / .2) var(--tw-gradient-via-position), var(--tw-gradient-to)
}

.to-\[\#00DD77\] {
    --tw-gradient-to: #00DD77 var(--tw-gradient-to-position)
}

.to-\[\#00FF88\]\/10 {
    --tw-gradient-to: rgb(0 255 136 / .1) var(--tw-gradient-to-position)
}

.to-\[\#1a1a1a\] {
    --tw-gradient-to: #1a1a1a var(--tw-gradient-to-position)
}

.to-\[\#FFD700\] {
    --tw-gradient-to: #FFD700 var(--tw-gradient-to-position)
}

.to-black {
    --tw-gradient-to: #000 var(--tw-gradient-to-position)
}

.to-blue-600\/10 {
    --tw-gradient-to: rgb(37 99 235 / .1) var(--tw-gradient-to-position)
}

.to-gray-900 {
    --tw-gradient-to: #111827 var(--tw-gradient-to-position)
}

.to-orange-400 {
    --tw-gradient-to: #fb923c var(--tw-gradient-to-position)
}

.to-orange-500\/20 {
    --tw-gradient-to: rgb(249 115 22 / .2) var(--tw-gradient-to-position)
}

.to-pink-500 {
    --tw-gradient-to: #ec4899 var(--tw-gradient-to-position)
}

.to-transparent {
    --tw-gradient-to: transparent var(--tw-gradient-to-position)
}

.fill-current {
    fill: currentColor
}

.object-contain {
    -o-object-fit: contain;
    object-fit: contain
}

.object-cover {
    -o-object-fit: cover;
    object-fit: cover
}

.p-0\.5 {
    padding: .125rem
}

.p-1 {
    padding: .25rem
}

.p-1\.5 {
    padding: .375rem
}

.p-2 {
    padding: .5rem
}

.p-3 {
    padding: .75rem
}

.p-4 {
    padding: 1rem
}

.p-6 {
    padding: 1.5rem
}

.p-8 {
    padding: 2rem
}

.\!px-1 {
    padding-left: .25rem!important;
    padding-right: .25rem!important
}

.\!py-5 {
    padding-top: 1.25rem!important;
    padding-bottom: 1.25rem!important
}

.px-1 {
    padding-left: .25rem;
    padding-right: .25rem
}

.px-1\.5 {
    padding-left: .375rem;
    padding-right: .375rem
}

.px-12 {
    padding-left: 3rem;
    padding-right: 3rem
}

.px-2 {
    padding-left: .5rem;
    padding-right: .5rem
}

.px-3 {
    padding-left: .75rem;
    padding-right: .75rem
}

.px-3\.5 {
    padding-left: .875rem;
    padding-right: .875rem
}

.px-4 {
    padding-left: 1rem;
    padding-right: 1rem
}

.px-5 {
    padding-left: 1.25rem;
    padding-right: 1.25rem
}

.px-6 {
    padding-left: 1.5rem;
    padding-right: 1.5rem
}

.px-8 {
    padding-left: 2rem;
    padding-right: 2rem
}

.py-0\.5 {
    padding-top: .125rem;
    padding-bottom: .125rem
}

.py-1 {
    padding-top: .25rem;
    padding-bottom: .25rem
}

.py-12 {
    padding-top: 3rem;
    padding-bottom: 3rem
}

.py-2 {
    padding-top: .5rem;
    padding-bottom: .5rem
}

.py-2\.5 {
    padding-top: .625rem;
    padding-bottom: .625rem
}

.py-20 {
    padding-top: 5rem;
    padding-bottom: 5rem
}

.py-3 {
    padding-top: .75rem;
    padding-bottom: .75rem
}

.py-4 {
    padding-top: 1rem;
    padding-bottom: 1rem
}

.py-5 {
    padding-top: 1.25rem;
    padding-bottom: 1.25rem
}

.py-6 {
    padding-top: 1.5rem;
    padding-bottom: 1.5rem
}

.py-8 {
    padding-top: 2rem;
    padding-bottom: 2rem
}

.pb-0\.5 {
    padding-bottom: .125rem
}

.pb-1 {
    padding-bottom: .25rem
}

.pb-12 {
    padding-bottom: 3rem
}

.pb-2 {
    padding-bottom: .5rem
}

.pb-3 {
    padding-bottom: .75rem
}

.pb-4 {
    padding-bottom: 1rem
}

.pb-6 {
    padding-bottom: 1.5rem
}

.pl-10 {
    padding-left: 2.5rem
}

.pl-20 {
    padding-left: 5rem
}

.pl-9 {
    padding-left: 2.25rem
}

.pr-12 {
    padding-right: 3rem
}

.pr-4 {
    padding-right: 1rem
}

.pt-2 {
    padding-top: .5rem
}

.pt-3 {
    padding-top: .75rem
}

.pt-4 {
    padding-top: 1rem
}

.pt-5 {
    padding-top: 1.25rem
}

.pt-6 {
    padding-top: 1.5rem
}

.pt-8 {
    padding-top: 2rem
}

.text-left {
    text-align: left
}

.text-center {
    text-align: center
}

.text-right {
    text-align: right
}

.font-mono {
    font-family: ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,Liberation Mono,Courier New,monospace
}

.text-2xl {
    font-size: 1.5rem;
    line-height: 2rem
}

.text-3xl {
    font-size: 1.875rem;
    line-height: 2.25rem
}

.text-4xl {
    font-size: 2.25rem;
    line-height: 2.5rem
}

.text-5xl {
    font-size: 3rem;
    line-height: 1
}

.text-6xl {
    font-size: 3.75rem;
    line-height: 1
}

.text-7xl {
    font-size: 4.5rem;
    line-height: 1
}

.text-\[0\.7rem\] {
    font-size: .7rem
}

.text-\[0\.92rem\] {
    font-size: .92rem
}

.text-\[10px\] {
    font-size: 10px
}

.text-base {
    font-size: 1rem;
    line-height: 1.5rem
}

.text-lg {
    font-size: 1.125rem;
    line-height: 1.75rem
}

.text-sm {
    font-size: .875rem;
    line-height: 1.25rem
}

.text-xl {
    font-size: 1.25rem;
    line-height: 1.75rem
}

.text-xs {
    font-size: .75rem;
    line-height: 1rem
}

.font-bold {
    font-weight: 700
}

.font-extrabold {
    font-weight: 800
}

.font-medium {
    font-weight: 500
}

.font-normal {
    font-weight: 400
}

.font-semibold {
    font-weight: 600
}

.uppercase {
    text-transform: uppercase
}

.capitalize {
    text-transform: capitalize
}

.leading-4 {
    line-height: 1rem
}

.leading-none {
    line-height: 1
}

.leading-relaxed {
    line-height: 1.625
}

.tracking-tight {
    letter-spacing: -.025em
}

.\!text-rose-500 {
    --tw-text-opacity: 1 !important;
    color: rgb(244 63 94 / var(--tw-text-opacity, 1))!important
}

.text-\[\#00D4AA\] {
    --tw-text-opacity: 1;
    color: rgb(0 212 170 / var(--tw-text-opacity, 1))
}

.text-\[\#00FF88\] {
    --tw-text-opacity: 1;
    color: rgb(0 255 136 / var(--tw-text-opacity, 1))
}

.text-\[\#EF4444\] {
    --tw-text-opacity: 1;
    color: rgb(239 68 68 / var(--tw-text-opacity, 1))
}

.text-\[\#FFD700\] {
    --tw-text-opacity: 1;
    color: rgb(255 215 0 / var(--tw-text-opacity, 1))
}

.text-amber-400 {
    --tw-text-opacity: 1;
    color: rgb(251 191 36 / var(--tw-text-opacity, 1))
}

.text-amber-400\/75 {
    color: #fbbf24bf
}

.text-black {
    --tw-text-opacity: 1;
    color: rgb(0 0 0 / var(--tw-text-opacity, 1))
}

.text-black\/70 {
    color: #000000b3
}

.text-blue-100 {
    --tw-text-opacity: 1;
    color: rgb(219 234 254 / var(--tw-text-opacity, 1))
}

.text-blue-200 {
    --tw-text-opacity: 1;
    color: rgb(191 219 254 / var(--tw-text-opacity, 1))
}

.text-blue-400 {
    --tw-text-opacity: 1;
    color: rgb(96 165 250 / var(--tw-text-opacity, 1))
}

.text-blue-500 {
    --tw-text-opacity: 1;
    color: rgb(59 130 246 / var(--tw-text-opacity, 1))
}

.text-blue-600 {
    --tw-text-opacity: 1;
    color: rgb(37 99 235 / var(--tw-text-opacity, 1))
}

.text-emerald-300 {
    --tw-text-opacity: 1;
    color: rgb(110 231 183 / var(--tw-text-opacity, 1))
}

.text-emerald-400 {
    --tw-text-opacity: 1;
    color: rgb(52 211 153 / var(--tw-text-opacity, 1))
}

.text-gray-200 {
    --tw-text-opacity: 1;
    color: rgb(229 231 235 / var(--tw-text-opacity, 1))
}

.text-gray-300 {
    --tw-text-opacity: 1;
    color: rgb(209 213 219 / var(--tw-text-opacity, 1))
}

.text-gray-400 {
    --tw-text-opacity: 1;
    color: rgb(156 163 175 / var(--tw-text-opacity, 1))
}

.text-gray-500 {
    --tw-text-opacity: 1;
    color: rgb(107 114 128 / var(--tw-text-opacity, 1))
}

.text-gray-600 {
    --tw-text-opacity: 1;
    color: rgb(75 85 99 / var(--tw-text-opacity, 1))
}

.text-gray-900 {
    --tw-text-opacity: 1;
    color: rgb(17 24 39 / var(--tw-text-opacity, 1))
}

.text-green-100 {
    --tw-text-opacity: 1;
    color: rgb(220 252 231 / var(--tw-text-opacity, 1))
}

.text-green-200 {
    --tw-text-opacity: 1;
    color: rgb(187 247 208 / var(--tw-text-opacity, 1))
}

.text-green-400 {
    --tw-text-opacity: 1;
    color: rgb(74 222 128 / var(--tw-text-opacity, 1))
}

.text-green-500 {
    --tw-text-opacity: 1;
    color: rgb(34 197 94 / var(--tw-text-opacity, 1))
}

.text-green-600 {
    --tw-text-opacity: 1;
    color: rgb(22 163 74 / var(--tw-text-opacity, 1))
}

.text-orange-500 {
    --tw-text-opacity: 1;
    color: rgb(249 115 22 / var(--tw-text-opacity, 1))
}

.text-purple-400 {
    --tw-text-opacity: 1;
    color: rgb(192 132 252 / var(--tw-text-opacity, 1))
}

.text-red-100 {
    --tw-text-opacity: 1;
    color: rgb(254 226 226 / var(--tw-text-opacity, 1))
}

.text-red-200 {
    --tw-text-opacity: 1;
    color: rgb(254 202 202 / var(--tw-text-opacity, 1))
}

.text-red-400 {
    --tw-text-opacity: 1;
    color: rgb(248 113 113 / var(--tw-text-opacity, 1))
}

.text-red-500 {
    --tw-text-opacity: 1;
    color: rgb(239 68 68 / var(--tw-text-opacity, 1))
}

.text-red-600 {
    --tw-text-opacity: 1;
    color: rgb(220 38 38 / var(--tw-text-opacity, 1))
}

.text-red-700 {
    --tw-text-opacity: 1;
    color: rgb(185 28 28 / var(--tw-text-opacity, 1))
}

.text-rose-500 {
    --tw-text-opacity: 1;
    color: rgb(244 63 94 / var(--tw-text-opacity, 1))
}

.text-rose-600\/90 {
    color: #e11d48e6
}

.text-white {
    --tw-text-opacity: 1;
    color: rgb(255 255 255 / var(--tw-text-opacity, 1))
}

.text-white\/80 {
    color: #fffc
}

.text-yellow-100 {
    --tw-text-opacity: 1;
    color: rgb(254 249 195 / var(--tw-text-opacity, 1))
}

.text-yellow-200 {
    --tw-text-opacity: 1;
    color: rgb(254 240 138 / var(--tw-text-opacity, 1))
}

.text-yellow-300 {
    --tw-text-opacity: 1;
    color: rgb(253 224 71 / var(--tw-text-opacity, 1))
}

.text-yellow-400 {
    --tw-text-opacity: 1;
    color: rgb(250 204 21 / var(--tw-text-opacity, 1))
}

.text-yellow-500 {
    --tw-text-opacity: 1;
    color: rgb(234 179 8 / var(--tw-text-opacity, 1))
}

.text-yellow-600 {
    --tw-text-opacity: 1;
    color: rgb(202 138 4 / var(--tw-text-opacity, 1))
}

.underline {
    text-decoration-line: underline
}

.placeholder-gray-400::-moz-placeholder {
    --tw-placeholder-opacity: 1;
    color: rgb(156 163 175 / var(--tw-placeholder-opacity, 1))
}

.placeholder-gray-400::placeholder {
    --tw-placeholder-opacity: 1;
    color: rgb(156 163 175 / var(--tw-placeholder-opacity, 1))
}

.placeholder-transparent::-moz-placeholder {
    color: transparent
}

.placeholder-transparent::placeholder {
    color: transparent
}

.opacity-0 {
    opacity: 0
}

.opacity-100 {
    opacity: 1
}

.opacity-20 {
    opacity: .2
}

.opacity-25 {
    opacity: .25
}

.opacity-30 {
    opacity: .3
}

.opacity-35 {
    opacity: .35
}

.opacity-50 {
    opacity: .5
}

.opacity-75 {
    opacity: .75
}

.opacity-80 {
    opacity: .8
}

.opacity-90 {
    opacity: .9
}

.shadow {
    --tw-shadow: 0 1px 3px 0 rgb(0 0 0 / .1), 0 1px 2px -1px rgb(0 0 0 / .1);
    --tw-shadow-colored: 0 1px 3px 0 var(--tw-shadow-color), 0 1px 2px -1px var(--tw-shadow-color);
    box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000),var(--tw-ring-shadow, 0 0 #0000),var(--tw-shadow)
}

.shadow-2xl {
    --tw-shadow: 0 25px 50px -12px rgb(0 0 0 / .25);
    --tw-shadow-colored: 0 25px 50px -12px var(--tw-shadow-color);
    box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000),var(--tw-ring-shadow, 0 0 #0000),var(--tw-shadow)
}

.shadow-lg {
    --tw-shadow: 0 10px 15px -3px rgb(0 0 0 / .1), 0 4px 6px -4px rgb(0 0 0 / .1);
    --tw-shadow-colored: 0 10px 15px -3px var(--tw-shadow-color), 0 4px 6px -4px var(--tw-shadow-color);
    box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000),var(--tw-ring-shadow, 0 0 #0000),var(--tw-shadow)
}

.shadow-md {
    --tw-shadow: 0 4px 6px -1px rgb(0 0 0 / .1), 0 2px 4px -2px rgb(0 0 0 / .1);
    --tw-shadow-colored: 0 4px 6px -1px var(--tw-shadow-color), 0 2px 4px -2px var(--tw-shadow-color);
    box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000),var(--tw-ring-shadow, 0 0 #0000),var(--tw-shadow)
}

.shadow-sm {
    --tw-shadow: 0 1px 2px 0 rgb(0 0 0 / .05);
    --tw-shadow-colored: 0 1px 2px 0 var(--tw-shadow-color);
    box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000),var(--tw-ring-shadow, 0 0 #0000),var(--tw-shadow)
}

.shadow-xl {
    --tw-shadow: 0 20px 25px -5px rgb(0 0 0 / .1), 0 8px 10px -6px rgb(0 0 0 / .1);
    --tw-shadow-colored: 0 20px 25px -5px var(--tw-shadow-color), 0 8px 10px -6px var(--tw-shadow-color);
    box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000),var(--tw-ring-shadow, 0 0 #0000),var(--tw-shadow)
}

.shadow-\[\#00FF88\]\/20 {
    --tw-shadow-color: rgb(0 255 136 / .2);
    --tw-shadow: var(--tw-shadow-colored)
}

.shadow-\[\#00FF88\]\/25 {
    --tw-shadow-color: rgb(0 255 136 / .25);
    --tw-shadow: var(--tw-shadow-colored)
}

.outline-none {
    outline: 2px solid transparent;
    outline-offset: 2px
}

.ring-2 {
    --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
    --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);
    box-shadow: var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow, 0 0 #0000)
}

.ring-yellow-400 {
    --tw-ring-opacity: 1;
    --tw-ring-color: rgb(250 204 21 / var(--tw-ring-opacity, 1))
}

.blur {
    --tw-blur: blur(8px);
    filter: var(--tw-blur) var(--tw-brightness) var(--tw-contrast) var(--tw-grayscale) var(--tw-hue-rotate) var(--tw-invert) var(--tw-saturate) var(--tw-sepia) var(--tw-drop-shadow)
}

.drop-shadow-lg {
    --tw-drop-shadow: drop-shadow(0 10px 8px rgb(0 0 0 / .04)) drop-shadow(0 4px 3px rgb(0 0 0 / .1));
    filter: var(--tw-blur) var(--tw-brightness) var(--tw-contrast) var(--tw-grayscale) var(--tw-hue-rotate) var(--tw-invert) var(--tw-saturate) var(--tw-sepia) var(--tw-drop-shadow)
}

.filter {
    filter: var(--tw-blur) var(--tw-brightness) var(--tw-contrast) var(--tw-grayscale) var(--tw-hue-rotate) var(--tw-invert) var(--tw-saturate) var(--tw-sepia) var(--tw-drop-shadow)
}

.backdrop-blur-md {
    --tw-backdrop-blur: blur(12px);
    -webkit-backdrop-filter: var(--tw-backdrop-blur) var(--tw-backdrop-brightness) var(--tw-backdrop-contrast) var(--tw-backdrop-grayscale) var(--tw-backdrop-hue-rotate) var(--tw-backdrop-invert) var(--tw-backdrop-opacity) var(--tw-backdrop-saturate) var(--tw-backdrop-sepia);
    backdrop-filter: var(--tw-backdrop-blur) var(--tw-backdrop-brightness) var(--tw-backdrop-contrast) var(--tw-backdrop-grayscale) var(--tw-backdrop-hue-rotate) var(--tw-backdrop-invert) var(--tw-backdrop-opacity) var(--tw-backdrop-saturate) var(--tw-backdrop-sepia)
}

.backdrop-blur-sm {
    --tw-backdrop-blur: blur(4px);
    -webkit-backdrop-filter: var(--tw-backdrop-blur) var(--tw-backdrop-brightness) var(--tw-backdrop-contrast) var(--tw-backdrop-grayscale) var(--tw-backdrop-hue-rotate) var(--tw-backdrop-invert) var(--tw-backdrop-opacity) var(--tw-backdrop-saturate) var(--tw-backdrop-sepia);
    backdrop-filter: var(--tw-backdrop-blur) var(--tw-backdrop-brightness) var(--tw-backdrop-contrast) var(--tw-backdrop-grayscale) var(--tw-backdrop-hue-rotate) var(--tw-backdrop-invert) var(--tw-backdrop-opacity) var(--tw-backdrop-saturate) var(--tw-backdrop-sepia)
}

.transition {
    transition-property: color,background-color,border-color,text-decoration-color,fill,stroke,opacity,box-shadow,transform,filter,-webkit-backdrop-filter;
    transition-property: color,background-color,border-color,text-decoration-color,fill,stroke,opacity,box-shadow,transform,filter,backdrop-filter;
    transition-property: color,background-color,border-color,text-decoration-color,fill,stroke,opacity,box-shadow,transform,filter,backdrop-filter,-webkit-backdrop-filter;
    transition-timing-function: cubic-bezier(.4,0,.2,1);
    transition-duration: .15s
}

.transition-\[color\,box-shadow\] {
    transition-property: color,box-shadow;
    transition-timing-function: cubic-bezier(.4,0,.2,1);
    transition-duration: .15s
}

.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(.4,0,.2,1);
    transition-duration: .15s
}

.transition-colors {
    transition-property: color,background-color,border-color,text-decoration-color,fill,stroke;
    transition-timing-function: cubic-bezier(.4,0,.2,1);
    transition-duration: .15s
}

.transition-opacity {
    transition-property: opacity;
    transition-timing-function: cubic-bezier(.4,0,.2,1);
    transition-duration: .15s
}

.transition-shadow {
    transition-property: box-shadow;
    transition-timing-function: cubic-bezier(.4,0,.2,1);
    transition-duration: .15s
}

.transition-transform {
    transition-property: transform;
    transition-timing-function: cubic-bezier(.4,0,.2,1);
    transition-duration: .15s
}

.duration-1000 {
    transition-duration: 1s
}

.duration-200 {
    transition-duration: .2s
}

.duration-300 {
    transition-duration: .3s
}

.duration-500 {
    transition-duration: .5s
}

.ease-in-out {
    transition-timing-function: cubic-bezier(.4,0,.2,1)
}

.ease-out {
    transition-timing-function: cubic-bezier(0,0,.2,1)
}

.scrollbar-hide::-webkit-scrollbar {
    display: none
}

.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none
}

.\*\:rounded-sm>* {
    border-radius: .125rem
}

.\*\:px-2>* {
    padding-left: .5rem;
    padding-right: .5rem
}

.\*\:py-1>* {
    padding-top: .25rem;
    padding-bottom: .25rem
}

.\*\:transition-transform>* {
    transition-property: transform;
    transition-timing-function: cubic-bezier(.4,0,.2,1);
    transition-duration: .15s
}

.placeholder\:text-gray-400::-moz-placeholder {
    --tw-text-opacity: 1;
    color: rgb(156 163 175 / var(--tw-text-opacity, 1))
}

.placeholder\:text-gray-400::placeholder {
    --tw-text-opacity: 1;
    color: rgb(156 163 175 / var(--tw-text-opacity, 1))
}

.hover\:rotate-90:hover {
    --tw-rotate: 90deg;
    transform: translate(var(--tw-translate-x),var(--tw-translate-y)) rotate(var(--tw-rotate)) skew(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))
}

.hover\:scale-105:hover {
    --tw-scale-x: 1.05;
    --tw-scale-y: 1.05;
    transform: translate(var(--tw-translate-x),var(--tw-translate-y)) rotate(var(--tw-rotate)) skew(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))
}

.hover\:scale-110:hover {
    --tw-scale-x: 1.1;
    --tw-scale-y: 1.1;
    transform: translate(var(--tw-translate-x),var(--tw-translate-y)) rotate(var(--tw-rotate)) skew(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))
}

.hover\:border-gray-300:hover {
    --tw-border-opacity: 1;
    border-color: rgb(209 213 219 / var(--tw-border-opacity, 1))
}

.hover\:border-gray-600:hover {
    --tw-border-opacity: 1;
    border-color: rgb(75 85 99 / var(--tw-border-opacity, 1))
}

.hover\:bg-\[\#00DD77\]:hover {
    --tw-bg-opacity: 1;
    background-color: rgb(0 221 119 / var(--tw-bg-opacity, 1))
}

.hover\:bg-\[\#00FF88\]\/10:hover {
    background-color: #00ff881a
}

.hover\:bg-\[\#1a1a1a\]:hover {
    --tw-bg-opacity: 1;
    background-color: rgb(26 26 26 / var(--tw-bg-opacity, 1))
}

.hover\:bg-\[\#262626\]:hover {
    --tw-bg-opacity: 1;
    background-color: rgb(38 38 38 / var(--tw-bg-opacity, 1))
}

.hover\:bg-\[\#2a2a2a\]:hover {
    --tw-bg-opacity: 1;
    background-color: rgb(42 42 42 / var(--tw-bg-opacity, 1))
}

.hover\:bg-\[\#DC2626\]:hover {
    --tw-bg-opacity: 1;
    background-color: rgb(220 38 38 / var(--tw-bg-opacity, 1))
}

.hover\:bg-black\/70:hover {
    background-color: #000000b3
}

.hover\:bg-blue-500\/10:hover {
    background-color: #3b82f61a
}

.hover\:bg-blue-500\/30:hover {
    background-color: #3b82f64d
}

.hover\:bg-blue-700:hover {
    --tw-bg-opacity: 1;
    background-color: rgb(29 78 216 / var(--tw-bg-opacity, 1))
}

.hover\:bg-gray-600:hover {
    --tw-bg-opacity: 1;
    background-color: rgb(75 85 99 / var(--tw-bg-opacity, 1))
}

.hover\:bg-gray-700:hover {
    --tw-bg-opacity: 1;
    background-color: rgb(55 65 81 / var(--tw-bg-opacity, 1))
}

.hover\:bg-gray-700\/30:hover {
    background-color: #3741514d
}

.hover\:bg-gray-700\/50:hover {
    background-color: #37415180
}

.hover\:bg-gray-800:hover {
    --tw-bg-opacity: 1;
    background-color: rgb(31 41 55 / var(--tw-bg-opacity, 1))
}

.hover\:bg-gray-800\/30:hover {
    background-color: #1f29374d
}

.hover\:bg-gray-800\/50:hover {
    background-color: #1f293780
}

.hover\:bg-green-500\/30:hover {
    background-color: #22c55e4d
}

.hover\:bg-green-600:hover {
    --tw-bg-opacity: 1;
    background-color: rgb(22 163 74 / var(--tw-bg-opacity, 1))
}

.hover\:bg-green-700:hover {
    --tw-bg-opacity: 1;
    background-color: rgb(21 128 61 / var(--tw-bg-opacity, 1))
}

.hover\:bg-red-500\/10:hover {
    background-color: #ef44441a
}

.hover\:bg-red-500\/20:hover {
    background-color: #ef444433
}

.hover\:bg-red-500\/30:hover {
    background-color: #ef44444d
}

.hover\:bg-red-600:hover {
    --tw-bg-opacity: 1;
    background-color: rgb(220 38 38 / var(--tw-bg-opacity, 1))
}

.hover\:bg-red-700:hover {
    --tw-bg-opacity: 1;
    background-color: rgb(185 28 28 / var(--tw-bg-opacity, 1))
}

.hover\:bg-white\/10:hover {
    background-color: #ffffff1a
}

.hover\:bg-yellow-700:hover {
    --tw-bg-opacity: 1;
    background-color: rgb(161 98 7 / var(--tw-bg-opacity, 1))
}

.hover\:bg-opacity-10:hover {
    --tw-bg-opacity: .1
}

.hover\:bg-opacity-20:hover {
    --tw-bg-opacity: .2
}

.hover\:from-\[\#00DD77\]:hover {
    --tw-gradient-from: #00DD77 var(--tw-gradient-from-position);
    --tw-gradient-to: rgb(0 221 119 / 0) var(--tw-gradient-to-position);
    --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to)
}

.hover\:to-\[\#00BB66\]:hover {
    --tw-gradient-to: #00BB66 var(--tw-gradient-to-position)
}

.hover\:text-\[\#00D4AA\]:hover {
    --tw-text-opacity: 1;
    color: rgb(0 212 170 / var(--tw-text-opacity, 1))
}

.hover\:text-\[\#00DD77\]:hover {
    --tw-text-opacity: 1;
    color: rgb(0 221 119 / var(--tw-text-opacity, 1))
}

.hover\:text-\[\#00FF88\]:hover {
    --tw-text-opacity: 1;
    color: rgb(0 255 136 / var(--tw-text-opacity, 1))
}

.hover\:text-\[var\(--primary-color\)\]: hover {
    color:var(--primary-color)
}

.hover\:text-blue-400:hover {
    --tw-text-opacity: 1;
    color: rgb(96 165 250 / var(--tw-text-opacity, 1))
}

.hover\:text-gray-800:hover {
    --tw-text-opacity: 1;
    color: rgb(31 41 55 / var(--tw-text-opacity, 1))
}

.hover\:text-red-400:hover {
    --tw-text-opacity: 1;
    color: rgb(248 113 113 / var(--tw-text-opacity, 1))
}

.hover\:text-white:hover {
    --tw-text-opacity: 1;
    color: rgb(255 255 255 / var(--tw-text-opacity, 1))
}

.hover\:text-yellow-400:hover {
    --tw-text-opacity: 1;
    color: rgb(250 204 21 / var(--tw-text-opacity, 1))
}

.hover\:shadow-sm:hover {
    --tw-shadow: 0 1px 2px 0 rgb(0 0 0 / .05);
    --tw-shadow-colored: 0 1px 2px 0 var(--tw-shadow-color);
    box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000),var(--tw-ring-shadow, 0 0 #0000),var(--tw-shadow)
}

.hover\:brightness-110:hover {
    --tw-brightness: brightness(1.1);
    filter: var(--tw-blur) var(--tw-brightness) var(--tw-contrast) var(--tw-grayscale) var(--tw-hue-rotate) var(--tw-invert) var(--tw-saturate) var(--tw-sepia) var(--tw-drop-shadow)
}

.hover\:brightness-125:hover {
    --tw-brightness: brightness(1.25);
    filter: var(--tw-blur) var(--tw-brightness) var(--tw-contrast) var(--tw-grayscale) var(--tw-hue-rotate) var(--tw-invert) var(--tw-saturate) var(--tw-sepia) var(--tw-drop-shadow)
}

.\*\:hover\:bg-gray-800\/50:hover>* {
    background-color: #1f293780
}

.focus\:border-\[\#00FF88\]:focus {
    --tw-border-opacity: 1;
    border-color: rgb(0 255 136 / var(--tw-border-opacity, 1))
}

.focus\:border-green-500:focus {
    --tw-border-opacity: 1;
    border-color: rgb(34 197 94 / var(--tw-border-opacity, 1))
}

.focus\:outline-none:focus {
    outline: 2px solid transparent;
    outline-offset: 2px
}

.focus\:ring-2:focus {
    --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
    --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);
    box-shadow: var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow, 0 0 #0000)
}

.focus\:ring-\[\#00FF88\]:focus {
    --tw-ring-opacity: 1;
    --tw-ring-color: rgb(0 255 136 / var(--tw-ring-opacity, 1))
}

.focus\:ring-green-500:focus {
    --tw-ring-opacity: 1;
    --tw-ring-color: rgb(34 197 94 / var(--tw-ring-opacity, 1))
}

.active\:scale-90:active {
    --tw-scale-x: .9;
    --tw-scale-y: .9;
    transform: translate(var(--tw-translate-x),var(--tw-translate-y)) rotate(var(--tw-rotate)) skew(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))
}

.active\:scale-95:active {
    --tw-scale-x: .95;
    --tw-scale-y: .95;
    transform: translate(var(--tw-translate-x),var(--tw-translate-y)) rotate(var(--tw-rotate)) skew(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))
}

.\*\:active\:scale-90:active>* {
    --tw-scale-x: .9;
    --tw-scale-y: .9;
    transform: translate(var(--tw-translate-x),var(--tw-translate-y)) rotate(var(--tw-rotate)) skew(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))
}

.disabled\:pointer-events-none:disabled {
    pointer-events: none
}

.disabled\:cursor-not-allowed:disabled {
    cursor: not-allowed
}

.disabled\:opacity-40:disabled {
    opacity: .4
}

.disabled\:opacity-50:disabled {
    opacity: .5
}

.group:hover .group-hover\:translate-x-1 {
    --tw-translate-x: .25rem;
    transform: translate(var(--tw-translate-x),var(--tw-translate-y)) rotate(var(--tw-rotate)) skew(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))
}

.group:hover .group-hover\:translate-x-full {
    --tw-translate-x: 100%;
    transform: translate(var(--tw-translate-x),var(--tw-translate-y)) rotate(var(--tw-rotate)) skew(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))
}

.group:hover .group-hover\:scale-105 {
    --tw-scale-x: 1.05;
    --tw-scale-y: 1.05;
    transform: translate(var(--tw-translate-x),var(--tw-translate-y)) rotate(var(--tw-rotate)) skew(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))
}

.group:hover .group-hover\:bg-gray-600\/50 {
    background-color: #4b556380
}

.group:hover .group-hover\:opacity-100 {
    opacity: 1
}

.group:active .group-active\:scale-90 {
    --tw-scale-x: .9;
    --tw-scale-y: .9;
    transform: translate(var(--tw-translate-x),var(--tw-translate-y)) rotate(var(--tw-rotate)) skew(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))
}

.has-\[\>svg\]\:px-2\.5:has(>svg) {
    padding-left: .625rem;
    padding-right: .625rem
}

@media (min-width: 640px) {
    .sm\:mb-8 {
        margin-bottom:2rem
    }

    .sm\:mt-10 {
        margin-top: 2.5rem
    }

    .sm\:block {
        display: block
    }

    .sm\:inline {
        display: inline
    }

    .sm\:flex {
        display: flex
    }

    .sm\:inline-flex {
        display: inline-flex
    }

    .sm\:hidden {
        display: none
    }

    .sm\:size-4 {
        width: 1rem;
        height: 1rem
    }

    .sm\:w-auto {
        width: auto
    }

    .sm\:grid-cols-2 {
        grid-template-columns: repeat(2,minmax(0,1fr))
    }

    .sm\:grid-cols-3 {
        grid-template-columns: repeat(3,minmax(0,1fr))
    }

    .sm\:grid-cols-4 {
        grid-template-columns: repeat(4,minmax(0,1fr))
    }

    .sm\:flex-row {
        flex-direction: row
    }

    .sm\:items-center {
        align-items: center
    }

    .sm\:items-stretch {
        align-items: stretch
    }

    .sm\:justify-between {
        justify-content: space-between
    }

    .sm\:gap-2\.5 {
        gap: .625rem
    }

    .sm\:p-6 {
        padding: 1.5rem
    }

    .sm\:px-6 {
        padding-left: 1.5rem;
        padding-right: 1.5rem
    }

    .sm\:pb-0 {
        padding-bottom: 0
    }

    .sm\:pt-3 {
        padding-top: .75rem
    }
}

@media (min-width: 768px) {
    .md\:col-span-2 {
        grid-column:span 2 / span 2
    }

    .md\:mt-0 {
        margin-top: 0
    }

    .md\:flex {
        display: flex
    }

    .md\:hidden {
        display: none
    }

    .md\:h-80 {
        height: 20rem
    }

    .md\:grid-cols-2 {
        grid-template-columns: repeat(2,minmax(0,1fr))
    }

    .md\:grid-cols-3 {
        grid-template-columns: repeat(3,minmax(0,1fr))
    }

    .md\:grid-cols-5 {
        grid-template-columns: repeat(5,minmax(0,1fr))
    }

    .md\:flex-row {
        flex-direction: row
    }

    .md\:pt-7 {
        padding-top: 1.75rem
    }

    .md\:text-sm {
        font-size: .875rem;
        line-height: 1.25rem
    }
}

@media (min-width: 1024px) {
    .lg\:col-span-2 {
        grid-column:span 2 / span 2
    }

    .lg\:h-96 {
        height: 24rem
    }

    .lg\:grid-cols-2 {
        grid-template-columns: repeat(2,minmax(0,1fr))
    }

    .lg\:grid-cols-3 {
        grid-template-columns: repeat(3,minmax(0,1fr))
    }

    .lg\:grid-cols-4 {
        grid-template-columns: repeat(4,minmax(0,1fr))
    }

    .lg\:grid-cols-5 {
        grid-template-columns: repeat(5,minmax(0,1fr))
    }

    .lg\:flex-row {
        flex-direction: row
    }

    .lg\:items-center {
        align-items: center
    }

    .lg\:p-8 {
        padding: 2rem
    }

    .lg\:px-8 {
        padding-left: 2rem;
        padding-right: 2rem
    }
}

@media (min-width: 1280px) {
    .xl\:grid {
        display:grid
    }

    .xl\:w-auto {
        width: auto
    }

    .xl\:grid-cols-4 {
        grid-template-columns: repeat(4,minmax(0,1fr))
    }

    .xl\:grid-cols-8 {
        grid-template-columns: repeat(8,minmax(0,1fr))
    }

    .xl\:overflow-x-visible {
        overflow-x: visible
    }
}

    </style>
</head>
<body>
    <!-- Loading Screen -->
    <div class="loading-screen" id="loadingScreen">
        <div class="loading-spinner"></div>
    </div>

    <?php include('./inc/header.php'); ?>

    <main>
        <?php include('./components/carrossel.php'); ?>

        <?php include('./components/ganhos.php'); ?>

       
        <?php include('./components/modals.php'); ?>
        
        <?php include('./components/testimonials.php'); ?>
    </main>
<section class="cartelas-section">
    <div class="cartelas-container">
        
       

        <!-- Cartelas Grid -->
        <?php if (empty($cartelas)): ?>
            <div class="empty-state">
                <i class="bi bi-grid-3x3-gap empty-icon"></i>
                <h3 style="color: white; margin-bottom: 1rem;">Nenhuma raspadinha disponível</h3>
                <p>Novas raspadinhas em breve! Fique atento às atualizações.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($cartelas as $c): ?>
                    <div class="text-card-foreground flex flex-col rounded-xl border p-4 shadow-sm max-w-[26.083rem] max-h-[13.604rem] gap-4 group border-b-2 transition-all duration-400 select-none border-gray-700/50" style="background: linear-gradient(to top, rgba(255, 205, 0, 0.063) 0%, transparent 45%);">
                        <div class="w-full aspect-[5/1] overflow-hidden rounded-lg">
                            <img src="<?= htmlspecialchars($c['banner']); ?>" alt="<?= htmlspecialchars($c['nome']); ?>" class="w-full h-full object-cover" onerror="this.src='/assets/img/placeholder-raspadinha.jpg'">
                        </div>
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-2.5">
                            <h1 class="font-semibold text-white"><?= htmlspecialchars($c['nome']); ?></h1>
                            <h2 class="text-xs text-amber-400 font-medium opacity-90 uppercase">PRÊMIOS DE ATÉ R$ <?= number_format($c['maior_premio'], 0, ',', '.'); ?></h2>
                        </div>
                        <div class="flex items-end sm:items-center justify-between">
                            <a href="/raspadinhas/show.php?id=<?= $c['id']; ?>" class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium transition-all disabled:pointer-events-none disabled:opacity-50 outline-none shadow-xs h-10 rounded-md px-4 cursor-pointer" style="background-color: rgb(255, 205, 0); color: rgb(0, 0, 0);">
                                <div class="flex gap-2 justify-between items-center">
                                    <div class="flex gap-1 items-center font-semibold">
                                        <svg fill="currentColor" viewBox="0 0 256 256" width="1em" height="1em" xmlns="http://www.w3.org/2000/svg" class="size-5">
                                            <path d="M198.51 56.09C186.44 35.4 169.92 24 152 24h-48c-17.92 0-34.44 11.4-46.51 32.09C46.21 75.42 40 101 40 128s6.21 52.58 17.49 71.91C69.56 220.6 86.08 232 104 232h48c17.92 0 34.44-11.4 46.51-32.09C209.79 180.58 216 155 216 128s-6.21-52.58-17.49-71.91Zm1.28 63.91h-32a152.8 152.8 0 0 0-9.68-48h30.59c6.12 13.38 10.16 30 11.09 48Zm-20.6-64h-28.73a83 83 0 0 0-12-16H152c10 0 19.4 6 27.19 16ZM152 216h-13.51a83 83 0 0 0 12-16h28.73C171.4 210 162 216 152 216Zm36.7-32h-30.58a152.8 152.8 0 0 0 9.68-48h32c-.94 18-4.98 34.62-11.1 48Z"></path>
                                        </svg>
                                        <span class="font-semibold">Jogar</span>
                                    </div>
                                    <div class="bg-black rounded-md p-1.5 flex items-center gap-1 text-white text-xs">
                                        <span style="color: rgb(255, 205, 0);">R$</span> <?= number_format($c['valor'], 2, ',', '.'); ?>
                                    </div>
                                </div>
                            </a>
                            <a href="/raspadinhas/show.php?id=<?= $c['id']; ?>" class="sm:pt-3 pb-0.5 sm:pb-0 flex items-center gap-1.5 text-xs font-semibold cursor-pointer transition-all duration-200 text-gray-400 active:scale-95">
                                <svg viewBox="0 0 512 512" fill="currentColor" width="1em" height="1em" xmlns="http://www.w3.org/2000/svg" class="group-hover:animate-wiggle size-3 sm:size-4">
                                    <path d="m190.5 68.8 34.8 59.2H152c-22.1 0-40-17.9-40-40s17.9-40 40-40h2.2c14.9 0 28.8 7.9 36.3 20.8zM64 88c0 14.4 3.5 28 9.6 40H32c-17.7 0-32 14.3-32 32v64c0 17.7 14.3 32 32 32h448c17.7 0 32-14.3 32-32v-64c0-17.7-14.3-32-32-32h-41.6c6.1-12 9.6-25.6 9.6-40 0-48.6-39.4-88-88-88h-2.2c-31.9 0-61.5 16.9-77.7 44.4L256 85.5l-24.1-41C215.7 16.9 186.1 0 154.2 0H152c-48.6 0-88 39.4-88 88zm336 0c0 22.1-17.9 40-40 40h-73.3l34.8-59.2c7.6-12.9 21.4-20.8 36.3-20.8h2.2c22.1 0 40 17.9 40 40zM32 288v176c0 26.5 21.5 48 48 48h144V288zm256 224h144c26.5 0 48-21.5 48-48V288H288z"></path>
                                </svg>
                                <span>VER PRÊMIOS</span>
                                <svg width="1em" height="1em" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="size-3">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 4 8 8-8 8"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
    

    <script>
        // Loading screen
        window.addEventListener('load', function() {
            const loadingScreen = document.getElementById('loadingScreen');
            setTimeout(() => {
                loadingScreen.classList.add('hidden');
            }, 1000);
        });

        // Smooth animations on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in-up');
                }
            });
        }, observerOptions);

        // Observe elements for animation
        document.addEventListener('DOMContentLoaded', function() {
            const elementsToAnimate = document.querySelectorAll('.step-item, .game-category, .prize-item');
            elementsToAnimate.forEach(el => {
                observer.observe(el);
            });
        });

        // Parallax effect for hero section
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const heroElements = document.querySelectorAll('.parallax-element');
            
            heroElements.forEach(element => {
                const speed = element.dataset.speed || 0.5;
                element.style.transform = `translateY(${scrolled * speed}px)`;
            });
        });

        // Add floating animation to certain elements
        document.addEventListener('DOMContentLoaded', function() {
            const floatingElements = document.querySelectorAll('.hero-visuals .gaming-item');
            floatingElements.forEach((el, index) => {
                el.style.animationDelay = `${index * 0.5}s`;
                el.classList.add('floating');
            });
        });

        // Notiflix configuration
        Notiflix.Notify.init({
            width: '300px',
            position: 'right-top',
            distance: '20px',
            opacity: 1,
            borderRadius: '12px',
            rtl: false,
            timeout: 4000,
            messageMaxLength: 110,
            backOverlay: false,
            backOverlayColor: 'rgba(0,0,0,0.5)',
            plainText: true,
            showOnlyTheLastOne: false,
            clickToClose: true,
            pauseOnHover: true,
            ID: 'NotiflixNotify',
            className: 'notiflix-notify',
            zindex: 4001,
            fontFamily: 'Inter',
            fontSize: '14px',
            cssAnimation: true,
            cssAnimationDuration: 400,
            cssAnimationStyle: 'zoom',
            closeButton: false,
            useIcon: true,
            useFontAwesome: false,
            fontAwesomeIconStyle: 'basic',
            fontAwesomeIconSize: '16px',
            success: {
                background: '#22c55e',
                textColor: '#fff',
                childClassName: 'notiflix-notify-success',
                notiflixIconColor: 'rgba(0,0,0,0.2)',
                fontAwesomeClassName: 'fas fa-check-circle',
                fontAwesomeIconColor: 'rgba(0,0,0,0.2)',
                backOverlayColor: 'rgba(34,197,94,0.2)',
            }
        });

        // Dynamic copyright year
        document.addEventListener('DOMContentLoaded', function() {
            const currentYear = new Date().getFullYear();
            const copyrightElements = document.querySelectorAll('.footer-description');
            if (copyrightElements.length > 0) {
                copyrightElements[0].innerHTML = copyrightElements[0].innerHTML.replace('2025', currentYear);
            }
        });

        // Add glow effect to interactive elements
        document.addEventListener('DOMContentLoaded', function() {
            const glowElements = document.querySelectorAll('.btn-register, .hero-cta, .game-btn');
            glowElements.forEach(el => {
                el.classList.add('glow');
            });
        });

        // Mobile menu toggle (if needed)
        function toggleMobileMenu() {
            const mobileMenu = document.querySelector('.mobile-menu');
            if (mobileMenu) {
                mobileMenu.classList.toggle('active');
            }
        }

        // Console welcome message
        console.log('%c🎯 RaspaGreen - Bem-vindo!', 'color: #22c55e; font-size: 16px; font-weight: bold;');
        console.log('%cSistema carregado com sucesso!', 'color: #16a34a; font-size: 12px;');
    </script>

    <!-- Performance and Analytics -->
    <script>
        // Performance monitoring
        window.addEventListener('load', function() {
            if ('performance' in window) {
                const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
                console.log(`Página carregada em ${loadTime}ms`);
            }
        });

        // Error handling
        window.addEventListener('error', function(e) {
            console.error('Erro na página:', e.error);
        });

        // Lazy loading for images when implemented
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    </script>

    
    <style>
        /* Loading Animation */
        /* Solução definitiva para loading spinner fixo */
        .loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: #0a0a0a;
            z-index: 9999;
            transition: opacity 0.5s ease;
            
            /* Centralização perfeita */
            display: grid;
            place-items: center;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            position: relative;
            /* Remove todas as propriedades de borda do elemento principal */
        }

        .loading-spinner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 3px solid rgba(34, 197, 94, 0.3);
            border-top-color: #22c55e;
            border-radius: 50%;
            
            /* Chaves para rotação sem movimento */
            transform-origin: 50% 50%; /* Centro exato */
            animation: spinFixed 1s linear infinite;
            
            /* Força o elemento a manter posição */
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @keyframes spinFixed {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        /* Alternativa ainda mais simples usando apenas border-image */
        .loading-spinner-simple {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: conic-gradient(#22c55e, rgba(34, 197, 94, 0.3));
            animation: rotateSimple 1s linear infinite;
            position: relative;
            
            /* Máscara para criar o efeito de spinner */
            mask: radial-gradient(circle at center, transparent 18px, black 21px);
            -webkit-mask: radial-gradient(circle at center, transparent 18px, black 21px);
        }

        @keyframes rotateSimple {
            to {
                transform: rotate(360deg);
            }
        }

        /* Versão com CSS puro - mais moderna */
        .loading-spinner-modern {
            width: 50px;
            height: 50px;
            background: 
                conic-gradient(from 0deg, transparent, #22c55e, transparent),
                conic-gradient(from 180deg, transparent, rgba(34, 197, 94, 0.3), transparent);
            border-radius: 50%;
            animation: rotateModern 1s linear infinite;
            position: relative;
            
            /* Efeito de máscara para criar o anel */
            mask: radial-gradient(circle, transparent 17px, black 20px);
            -webkit-mask: radial-gradient(circle, transparent 17px, black 20px);
        }

        @keyframes rotateModern {
            100% {
                transform: rotate(360deg);
            }
        }

        .hidden {
            opacity: 0;
            pointer-events: none;
        }

        /* Reset completo para garantir que não há interferências */
        .loading-screen * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }
        
        /* Parallax effect */
        .parallax-element {
            transform: translateZ(0);
            will-change: transform;
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }
        
        /* Floating elements animation */
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        
        @keyframes floating {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        /* Glowing effect */
        .glow {
            box-shadow: 0 0 20px rgba(34, 197, 94, 0.3);
        }
        
        .glow:hover {
            box-shadow: 0 0 30px rgba(34, 197, 94, 0.5);
        }
        #treasure-box {
  position: fixed;
  bottom: 300px;
  right: 20px;
  z-index: 9999;
  width: 80px;
  height: 80px;
  cursor: pointer;
  animation: floatBox 3s ease-in-out infinite;
}

.box-img {
  width: 100%;
  height: auto;
  animation: pulseBox 2s infinite ease-in-out;
  filter: drop-shadow(0 0 10px gold);
}

@keyframes floatBox {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-8px); }
}

@keyframes pulseBox {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.05); }
}

/* Confetes */
.confetti {
  position: absolute;
  width: 10px;
  height: 10px;
  border-radius: 50%;
  opacity: 0.8;
  animation: explode 1.5s ease-in-out infinite;
}

.confetti1 {
  background: #ff3838;
  top: -10px;
  left: -5px;
  animation-delay: 0s;
}

.confetti2 {
  background: #22c55e;
  top: -15px;
  left: 20px;
  animation-delay: 0.2s;
}

.confetti3 {
  background: #facc15;
  top: -20px;
  left: 40px;
  animation-delay: 0.4s;
}

@keyframes explode {
  0% {
    transform: scale(0.5) translateY(0);
    opacity: 0.8;
  }
  50% {
    transform: scale(1.2) translateY(-15px);
    opacity: 1;
  }
  100% {
    transform: scale(0.5) translateY(0);
    opacity: 0;
  }
}

    </style>
</head>


<body>
 
    <script>
        // Loading screen
        window.addEventListener('load', function() {
            const loadingScreen = document.getElementById('loadingScreen');
            setTimeout(() => {
                loadingScreen.classList.add('hidden');
            }, 1000);
        });

        // Smooth animations on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in-up');
                }
            });
        }, observerOptions);

        // Observe elements for animation
        document.addEventListener('DOMContentLoaded', function() {
            const elementsToAnimate = document.querySelectorAll('.step-item, .game-category, .prize-item');
            elementsToAnimate.forEach(el => {
                observer.observe(el);
            });
        });

        // Parallax effect for hero section
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const heroElements = document.querySelectorAll('.parallax-element');
            
            heroElements.forEach(element => {
                const speed = element.dataset.speed || 0.5;
                element.style.transform = `translateY(${scrolled * speed}px)`;
            });
        });

        // Add floating animation to certain elements
        document.addEventListener('DOMContentLoaded', function() {
            const floatingElements = document.querySelectorAll('.hero-visuals .gaming-item');
            floatingElements.forEach((el, index) => {
                el.style.animationDelay = `${index * 0.5}s`;
                el.classList.add('floating');
            });
        });

        // Notiflix configuration
        Notiflix.Notify.init({
            width: '300px',
            position: 'right-top',
            distance: '20px',
            opacity: 1,
            borderRadius: '12px',
            rtl: false,
            timeout: 4000,
            messageMaxLength: 110,
            backOverlay: false,
            backOverlayColor: 'rgba(0,0,0,0.5)',
            plainText: true,
            showOnlyTheLastOne: false,
            clickToClose: true,
            pauseOnHover: true,
            ID: 'NotiflixNotify',
            className: 'notiflix-notify',
            zindex: 4001,
            fontFamily: 'Inter',
            fontSize: '14px',
            cssAnimation: true,
            cssAnimationDuration: 400,
            cssAnimationStyle: 'zoom',
            closeButton: false,
            useIcon: true,
            useFontAwesome: false,
            fontAwesomeIconStyle: 'basic',
            fontAwesomeIconSize: '16px',
            success: {
                background: '#22c55e',
                textColor: '#fff',
                childClassName: 'notiflix-notify-success',
                notiflixIconColor: 'rgba(0,0,0,0.2)',
                fontAwesomeClassName: 'fas fa-check-circle',
                fontAwesomeIconColor: 'rgba(0,0,0,0.2)',
                backOverlayColor: 'rgba(34,197,94,0.2)',
            }
        });

        // Dynamic copyright year
        document.addEventListener('DOMContentLoaded', function() {
            const currentYear = new Date().getFullYear();
            const copyrightElements = document.querySelectorAll('.footer-description');
            if (copyrightElements.length > 0) {
                copyrightElements[0].innerHTML = copyrightElements[0].innerHTML.replace('2025', currentYear);
            }
        });

        // Add glow effect to interactive elements
        document.addEventListener('DOMContentLoaded', function() {
            const glowElements = document.querySelectorAll('.btn-register, .hero-cta, .game-btn');
            glowElements.forEach(el => {
                el.classList.add('glow');
            });
        });

        // Mobile menu toggle (if needed)
        function toggleMobileMenu() {
            const mobileMenu = document.querySelector('.mobile-menu');
            if (mobileMenu) {
                mobileMenu.classList.toggle('active');
            }
        }

        // Console welcome message
        console.log('%c🎯 RaspaGreen - Bem-vindo!', 'color: #22c55e; font-size: 16px; font-weight: bold;');
        console.log('%cSistema carregado com sucesso!', 'color: #16a34a; font-size: 12px;');
    </script>

    <!-- Performance and Analytics -->
    <script>
        // Performance monitoring
        window.addEventListener('load', function() {
            if ('performance' in window) {
                const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
                console.log(`Página carregada em ${loadTime}ms`);
            }
        });

        // Error handling
        window.addEventListener('error', function(e) {
            console.error('Erro na página:', e.error);
        });

        // Lazy loading for images when implemented
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    </script>
</body>
</html>