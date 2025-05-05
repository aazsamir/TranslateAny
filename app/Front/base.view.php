<x-component name="x-base">
    <html lang="en">

    <head>
        <title>TranslateAny</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="/bootstrap.min.css">
        <script src="/bootstrap.min.js"></script>
        <script src="bootstrap.bundle.min.js"></script>

        <style>
            html,
            body {
                height: 100%;
                margin: 0;
                padding: 0;
            }

            body {
                min-height: 100vh;
                display: flex;
                flex-direction: column;
            }

            .full-height-container {
                flex: 1 1 auto;
                display: flex;
                flex-direction: column;
                justify-content: stretch;
            }

            .textarea-row {
                flex: 1 1 auto;
                display: flex;
                gap: 1rem;
            }

            .textarea-row textarea {
                flex: 1 1 0;
                height: 100%;
                resize: none;
            }

            .textarea-row textarea:focus {
                outline: none !important;
                box-shadow: none !important;
            }
        </style>
    </head>

    <body data-bs-theme="dark">
        <div class="container-fluid full-height-container">
            <div class="row">
                <div class="col-12 text-center my-3">
                    <h1>TranslateAny</h1>
                    <button class="btn btn-secondary" id="theme-toggle" style="position: absolute; right: 20px; top: 20px;">Theme</button>
                </div>
            </div>
            <x-slot />
        </div>
    </body>

    <script>
        const themeToggle = document.getElementById('theme-toggle');
        themeToggle.addEventListener('click', toggleTheme);
        initialTheme();

        function toggleTheme() {
            const currentTheme = document.body.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.body.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        }

        function initialTheme() {
            const savedTheme = localStorage.getItem('theme');

            if (savedTheme) {
                document.body.setAttribute('data-bs-theme', savedTheme);
            } else {
                const prefersDarkScheme = window.matchMedia("(prefers-color-scheme: dark)");
                document.body.setAttribute('data-bs-theme', prefersDarkScheme.matches ? 'dark' : 'light');
            }
        }
    </script>
    <x-slot name="scripts" />

    </html>
</x-component>
