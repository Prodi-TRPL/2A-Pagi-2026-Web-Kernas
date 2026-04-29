<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SK-POLIBATAM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        .card-shadow {
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25), 0 4px 16px rgba(0, 0, 0, 0.15);
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #1e3a6e;
            box-shadow: 0 0 0 3px rgba(30, 58, 110, 0.1);
        }

        .btn-login {
            background: #1e3a6e;
            transition: background 0.2s ease, transform 0.1s ease;
        }
        .btn-login:hover { background: #162d56; }
        .btn-login:active { transform: scale(0.99); background: #0f2040; }
        .btn-login:disabled { background: #8fa3c4; cursor: not-allowed; }

        /* Fade-in animation for the card */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-up {
            animation: fadeUp 0.45s ease both;
        }
    </style>
</head>

<body class="relative min-h-screen flex items-center justify-center overflow-hidden px-4 py-10 bg-cover bg-center bg-no-repeat" style="">\
    
</body>
</html>