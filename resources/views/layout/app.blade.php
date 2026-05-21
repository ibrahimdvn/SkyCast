<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkyCast</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
                    },
                    colors: {
                        glass: {
                            50: 'rgba(255, 255, 255, 0.03)',
                            100: 'rgba(255, 255, 255, 0.08)',
                            200: 'rgba(255, 255, 255, 0.15)',
                            300: 'rgba(255, 255, 255, 0.25)',
                        }
                    },
                    boxShadow: {
                        'glass': '0 8px 32px 0 rgba(0, 0, 0, 0.15)',
                        'glass-inner': 'inset 0 1px 1px rgba(255, 255, 255, 0.2)',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            color: #ffffff;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            background-color: #0f172a;
        }
        
        .glass-panel {
            background: linear-gradient(135deg, rgba(255,255,255,0.12) 0%, rgba(255,255,255,0.02) 100%);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255,255,255,0.15);
            box-shadow: 0 8px 32px 0 rgba(0,0,0,0.15), inset 0 1px 1px rgba(255,255,255,0.2);
            border-radius: 1.5rem;
        }

        .glass-pill {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 9999px;
            transition: all 0.3s ease;
        }
        
        .glass-pill:hover {
            background: rgba(255,255,255,0.15);
        }

        .bg-gradient-clear { background: linear-gradient(160deg, #4facfe 0%, #00f2fe 100%); }
        .bg-gradient-clouds { background: linear-gradient(160deg, #8ba3ba 0%, #4b5d67 100%); }
        .bg-gradient-rain { background: linear-gradient(160deg, #2b5876 0%, #4e4376 100%); }
        .bg-gradient-snow { background: linear-gradient(160deg, #e0c3fc 0%, #8ec5fc 100%); }
        .bg-gradient-night { background: linear-gradient(160deg, #0f2027 0%, #203a43 50%, #2c5364 100%); }
        .bg-gradient-default { background: linear-gradient(160deg, #1e3c72 0%, #2a5298 100%); }

        /* Animated background mesh (Linear / Apple style) */
        .mesh-bg {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            z-index: -1;
            overflow: hidden;
        }
        .mesh-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.6;
            animation: float 20s infinite ease-in-out alternate;
        }
        .blob-1 { width: 600px; height: 600px; top: -100px; left: -100px; background: rgba(255,255,255,0.15); }
        .blob-2 { width: 500px; height: 500px; bottom: -50px; right: -50px; background: rgba(255,255,255,0.1); animation-delay: -5s; }

        @keyframes float {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(50px, 30px) scale(1.1); }
            100% { transform: translate(-20px, -40px) scale(0.9); }
        }

        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        .search-input::placeholder { color: rgba(255,255,255,0.5); }
        
        .animated-icon {
            filter: drop-shadow(0 10px 15px rgba(0,0,0,0.2));
            animation: hover 4s infinite ease-in-out;
        }
        
        @keyframes hover {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }
    </style>
</head>
<body class="antialiased min-h-screen">
    @yield('content')
</body>
</html>
