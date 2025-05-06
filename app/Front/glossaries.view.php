<x-base>
    <div class="row flex-grow-1">
        <div class="col-6">
            <form method="post" enctype="multipart/form-data" action="/deepl/v2/glossaries" class="w-100" id="glossaryForm">
                <label>Comma separated values</label>
                <textarea class="w-100" name="entries"></textarea>
                <select class="form-select" name="source_lang" required>
                    <x-template :foreach="App\System\Language::alphabetically() as $language">
                        <option value="{{ $language->upper() }}">{{ $language->value }}</option>
                    </x-template>
                </select>
                <select class="form-select" name="target_lang" required>
                    <x-template :foreach="App\System\Language::alphabetically() as $language">
                        <option value="{{ $language->upper() }}">{{ $language->value }}</option>
                    </x-template>
                </select>
                <input type="text" class="form-control" name="name" placeholder="Glossary Name" required>
                <input type="hidden" name="entries_format" value="csv">
                <button type="submit" class="btn btn-primary btn-lg w-100">Save</button>
            </form>
        </div>
        <div class="col-6">
            <div id="glossariesContainer"></div>
        </div>
    </div>
    <x-slot name="scripts">
        <script>
            const glossariesContainer = document.querySelector('#glossariesContainer');
            const glossaryForm = document.querySelector('#glossaryForm');
            var glossaries = [];

            fetchGlossaries();

            glossaryForm.addEventListener('submit', saveForm);

            function saveForm(event) {
                event.preventDefault();
                const formData = new FormData(glossaryForm);
                fetch('/deepl/v2/glossaries', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (response.ok) {
                            fetchGlossaries();
                        } else {
                            console.error('Error saving glossary:', response.statusText);
                        }
                    })
                    .catch(error => console.error('Error saving glossary:', error));
            }

            function fetchGlossaries() {
                fetch('/deepl/v2/glossaries')
                    .then(response => response.json())
                    .then(data => {
                        glossaries = data.glossaries;
                        renderGlossaries();
                    })
                    .catch(error => console.error('Error fetching glossaries:', error));
            }

            function renderGlossaries() {
                glossariesContainer.innerHTML = '';

                glossaries.forEach(glossary => {
                    const glossaryDiv = document.createElement('div');
                    glossaryDiv.className = 'glossary-item card';
                    glossaryDiv.innerHTML = `
                    <h5>${glossary.name}</h5>
                    <p class="source-lang">Source Language: ${glossary.source_lang}</p>
                    <p class="target-lang">Target Language: ${glossary.target_lang}</p>
                    <p>Entry Count: ${glossary.entry_count}</p>
                    <button class="btn btn-primary m-1" onclick="downloadGlossary('${glossary.glossary_id}')">Download</button>
                    <button class="btn btn-danger m-1" onclick="deleteGlossary('${glossary.glossary_id}')">Delete</button>
                `;
                    glossariesContainer.appendChild(glossaryDiv);
                });
            }

            function deleteGlossary(glossaryId) {
                fetch(`/deepl/v2/glossaries/${glossaryId}`, {
                        method: 'DELETE'
                    })
                    .then(response => {
                        if (response.ok) {
                            fetchGlossaries();
                        } else {
                            console.error('Error deleting glossary:', response.statusText);
                        }
                    })
                    .catch(error => console.error('Error deleting glossary:', error));
            }

            function downloadGlossary(glossaryId) {
                fetch(`/deepl/v2/glossaries/${glossaryId}/entries`, {
                        method: 'GET'
                    })
                    .then(response => {
                        if (response.ok) {
                            return response.text();
                        } else {
                            throw new Error('Error downloading glossary:', response.statusText);
                        }
                    })
                    .then(data => {
                        const textarea = document.querySelector('textarea[name="entries"]');
                        textarea.value = data.replace(/\t/g, ',');
                        const glossary = glossaries.find(g => g.glossary_id === glossaryId);

                        if (glossary) {
                            document.querySelector('select[name="source_lang"]').value = glossary.source_lang;
                            document.querySelector('select[name="target_lang"]').value = glossary.target_lang;
                            document.querySelector('input[name="name"]').value = glossary.name;
                        }
                    })
                    .catch(error => console.error('Error downloading glossary:', error));
            }
        </script>
    </x-slot>
</x-base>
