<x-layout>
    <div class="row">
        <div class="col d-flex">
            <div class="form-check form-switch mx-2 align-content-center">
                <input class="form-check-input" type="checkbox" id="realtime" checked>
                <label class="form-check-label" for="realtime">Realtime</label>
            </div>
            <select id="schema" class="form-select form-select-sm">
                <option value="libre">LibreTranslate</option>
                <option value="googlev2">Google Translate v2</option>
                <option value="deepl">DeepL</option>
                <option value="deeplx">DeepLX</option>
            </select>
        </div>
    </div>
    <div class="row mt-2 flex-grow-1" style="height:100%;">
        <div class="col-12 textarea-row">
            <textarea id="input" class="form-control-lg w-100"></textarea>
            <textarea id="output" class="form-control-lg w-100" readonly></textarea>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col d-flex justify-content-around">
            <select id="source" class="form-select form-select-lg w-25 mx-1" aria-label="Source Language">
                <option value="auto">auto</option>
                <x-template :foreach="App\System\Language::alphabetically() as $language">
                    <option value="{{ $language->lower() }}">{{ $language->value }}</option>
                </x-template>
            </select>
            <button id="detect" class="btn btn-secondary btn-lg mx-1">
                Detect
            </button>
            <button id="swap" class="btn btn-secondary btn-lg mx-1">
                &#8646;
            </button>
            <button id="translate" class="btn btn-primary btn-lg w-100 mx-1">Translate</button>
            <select id="target" class="form-select form-select-lg w-25 mx-1" aria-label="Target Language">
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
            const swapButton = document.getElementById('swap');
            const detectButton = document.getElementById('detect');
            const schemaSelect = document.getElementById('schema');
            const realtimeCheckbox = document.getElementById('realtime');

            var schema = 'libre';
            var translationAbortController = new AbortController();
            var signal = translationAbortController.signal;
            var timeout = null;

            translateButton.addEventListener('click', translate);
            input.addEventListener('input', saveText);
            input.addEventListener('input', realtimeTranslate);
            output.addEventListener('input', saveText);
            sourceSelect.addEventListener('change', saveLanguages);
            targetSelect.addEventListener('change', saveLanguages);
            swapButton.addEventListener('click', swapLanguages);
            detectButton.addEventListener('click', detect);
            schemaSelect.addEventListener('change', changeSchema);

            init();

            function translate() {
                if (signal) {
                    translationAbortController.abort();
                    translationAbortController = new AbortController();
                    signal = translationAbortController.signal;
                }

                const inputText = input.value.trim();
                const sourceLang = sourceSelect.value;
                const targetLang = targetSelect.value;

                if (inputText === '') {
                    return;
                }

                translateButton.disabled = true;
                output.value = 'Translating...';

                if (schema === 'libre') {
                    translation = translateLibre(inputText, sourceLang, targetLang);
                } else if (schema === 'googlev2') {
                    translation = translateGoogle(inputText, sourceLang, targetLang);
                } else if (schema === 'deepl') {
                    translation = translateDeepl(inputText, sourceLang, targetLang);
                } else if (schema === 'deeplx') {
                    translation = translateDeeplx(inputText, sourceLang, targetLang);
                } else {
                    console.error('Unknown translation schema:', schema);
                    output.value = 'Unknown translation schema: ' + schema;
                    translateButton.disabled = false;

                    return;
                }

                translation
                    .then(translatedText => {
                        output.value = translatedText;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        output.value = 'Translation failed: ' + error.message;
                    })
                    .finally(() => {
                        translateButton.disabled = false;
                        saveText();
                    });

                return translation;
            }

            function translateLibre(inputText, sourceLang, targetLang) {
                return fetch('/libre/translate', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            q: inputText,
                            source: sourceLang,
                            target: targetLang
                        }),
                        signal: translationAbortController.signal
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            throw new Error(data.error);
                        }

                        return data.translatedText;
                    })
                    .catch(error => {
                        if (error.name === 'AbortError') {
                            return;
                        }

                        throw new Error('Translation failed: ' + error.message);
                    });
            }

            function translateGoogle(input, sourceLang, targetLang) {
                return fetch('/google/v2/language/translate/v2', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            q: input,
                            source: sourceLang,
                            target: targetLang
                        }),
                        signal: translationAbortController.signal
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            throw new Error(data.error);
                        }

                        return data.data.translations[0].translatedText;
                    })
                    .catch(error => {
                        if (error.name === 'AbortError') {
                            return;
                        }

                        throw new Error('Translation failed: ' + error.message);
                    });
            }

            function translateDeepl(inputText, sourceLang, targetLang) {
                return fetch('/deepl/v2/translate', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            text: [inputText],
                            source_lang: sourceLang,
                            target_lang: targetLang
                        }),
                        signal: translationAbortController.signal
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            throw new Error(data.error);
                        }

                        return data.translations[0].text;
                    })
                    .catch(error => {
                        if (error.name === 'AbortError') {
                            return;
                        }

                        throw new Error('Translation failed: ' + error.message);
                    });
            }

            function translateDeeplx(inputText, sourceLang, targetLang) {
                return fetch('/deeplx/v1/translate', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            text: inputText,
                            source_lang: sourceLang,
                            target_lang: targetLang
                        }),
                        signal: translationAbortController.signal
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            throw new Error(data.error);
                        }
                        return data.data;
                    })
                    .catch(error => {
                        if (error.name === 'AbortError') {
                            return;
                        }

                        throw new Error('Translation failed: ' + error.message);
                    });
            }

            function init() {
                loadText();
                loadLanguages();
                loadSchema();
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

            function swapLanguages() {
                const sourceLang = sourceSelect.value;

                if (sourceLang === 'auto') {
                    detect().then(() => {
                        doSwap();
                    });
                } else {
                    doSwap();
                }
            }

            function doSwap() {
                const sourceLang = sourceSelect.value;
                const targetLang = targetSelect.value;
                sourceSelect.value = targetLang;
                targetSelect.value = sourceLang;

                const tempText = input.value;
                input.value = output.value;
                output.value = tempText;

                saveLanguages();
            }

            function detect() {
                const inputText = input.value.trim();
                detectButton.disabled = true;

                if (inputText === '') {
                    return;
                }

                return fetch('/libre/detect', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            q: inputText
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data[0]) {
                            sourceSelect.value = data[0].language;

                            saveLanguages();
                        }
                    }).finally(() => {
                        detectButton.disabled = false;
                    });
            }

            function changeSchema() {
                schema = schemaSelect.value;
                localStorage.setItem('schema', schema);
            }

            function loadSchema() {
                const savedSchema = localStorage.getItem('schema');

                if (savedSchema) {
                    schemaSelect.value = savedSchema;
                    changeSchema();
                }
            }

            function realtimeTranslate() {
                if (!realtimeCheckbox.checked) {
                    return;
                }

                if (timeout) {
                    clearTimeout(timeout);
                }

                timeout = setTimeout(() => translate(), 1000);
            }
        </script>
    </x-slot>
</x-layout>
