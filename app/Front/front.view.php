<x-base>
    <div class="row flex-grow-1" style="height:100%;">
        <div class="col-12 textarea-row">
            <textarea id="input" class="form-control-lg w-100"></textarea>
            <textarea id="output" class="form-control-lg w-100" readonly></textarea>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col d-flex justify-content-around">
            <select id="source" class="form-select form-select-lg w-25" aria-label="Source Language">
                <option value="auto">auto</option>
                <x-template :foreach="App\System\Language::alphabetically() as $language">
                    <option value="{{ $language->lower() }}">{{ $language->value }}</option>
                </x-template>
            </select>
            <button id="translate" class="btn btn-primary btn-lg w-100">Translate</button>
            <select id="target" class="form-select form-select-lg w-25" aria-label="Target Language">
                <x-template :foreach="App\System\Language::alphabetically() as $language">
                    <option value="{{ $language->lower() }}">{{ $language->value }}</option>
                </x-template>
            </select>
        </div>
    </div>

    <x-slot name="scripts">
        <script>
            const input = document.getElementById('input');
            const output = document.getElementById('output');
            const translateButton = document.getElementById('translate');
            const sourceSelect = document.getElementById('source');
            const targetSelect = document.getElementById('target');

            init();
            translateButton.addEventListener('click', translate);
            input.addEventListener('input', saveText);
            output.addEventListener('input', saveText);
            sourceSelect.addEventListener('change', saveLanguages);
            targetSelect.addEventListener('change', saveLanguages);

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
                        saveText();
                    });
            }

            function init() {
                loadText();
                loadLanguages();
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

            function saveLanguages() {
                const sourceLang = sourceSelect.value;
                const targetLang = targetSelect.value;

                localStorage.setItem('sourceLang', sourceLang);
                localStorage.setItem('targetLang', targetLang);
            }
        </script>
    </x-slot>
</x-base>
