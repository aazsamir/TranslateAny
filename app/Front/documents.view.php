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
        <div class="row flex-grow-1" style="height:100%;">
            <div class="col-12">
                <form method="post" enctype="multipart/form-data" action="/deepl/v2/document">
                    <select id="target_lang" class="form-select form-select-lg w-100" aria-label="Target Language" name="target_lang">
                        <option value="en">English</option>
                        <option value="pl">Polish</option>
                    </select>
                    <input type="file" class="form-control frame-file" name="file">
                    <button type="submit" class="btn btn-primary btn-lg w-100">Translate</button>
                </form>
            </div>
        </div>
    </div>
</body>

<script>
    /*const input = document.getElementById('input');
    const output = document.getElementById('output');
    const translateButton = document.getElementById('translate');
    const sourceSelect = document.getElementById('source');
    const targetSelect = document.getElementById('target');
    const themeToggle = document.getElementById('theme-toggle');

    init();
    translateButton.addEventListener('click', translate);
    themeToggle.addEventListener('click', toggleTheme);
    input.addEventListener('input', saveText);
    output.addEventListener('input', saveText);
    sourceSelect.addEventListener('change', saveLanguages);
    targetSelect.addEventListener('change', saveLanguages);*/

    function translate() {
        const inputText = input.value;
        const sourceLang = sourceSelect.value;
        const targetLang = targetSelect.value;

        if (inputText.trim() === '') {
            alert('Please enter text to translate.');

            return;
        }

        translateButton.disabled = true;
        output.value = 'Translating...';

        fetch('/libre/translate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    q: inputText,
                    source: sourceLang,
                    target: targetLang
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    output.value = 'Error: ' + data.error;
                } else {
                    output.value = data.translatedText;
                }
            })
            .catch(error => {
                output.value = 'Error: ' + error.message;
            })
            .finally(() => {
                translateButton.disabled = false;
            });
    }

    function toggleTheme() {
        const currentTheme = document.body.getAttribute('data-bs-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        document.body.setAttribute('data-bs-theme', newTheme);
        localStorage.setItem('theme', newTheme);
    }

    function init() {
        initialTheme();
        loadText();
        loadLanguages();
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

    function loadText() {
        const savedInputText = localStorage.getItem('inputText');
        const savedOutputText = localStorage.getItem('outputText');

        if (savedInputText) {
            input.value = savedInputText;
        }

        if (savedOutputText) {
            output.value = savedOutputText;
        }
    }

    function loadLanguages() {
        const savedSourceLang = localStorage.getItem('sourceLang');
        const savedTargetLang = localStorage.getItem('targetLang');

        if (savedSourceLang) {
            sourceSelect.value = savedSourceLang;
        }

        if (savedTargetLang) {
            targetSelect.value = savedTargetLang;
        }
    }

    function saveText() {
        const inputText = input.value;
        const outputText = output.value;

        localStorage.setItem('inputText', inputText);
        localStorage.setItem('outputText', outputText);
    }

    function saveLanguages() {
        const sourceLang = sourceSelect.value;
        const targetLang = targetSelect.value;

        localStorage.setItem('sourceLang', sourceLang);
        localStorage.setItem('targetLang', targetLang);
    }
</script>

</html>
