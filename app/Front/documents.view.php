<x-layout>
    <div class="row flex-grow-1" style="height:100%;">
        <div class="col-12">
            <form method="post" enctype="multipart/form-data" action="/deepl/v2/document">
                <select id="target_lang" class="form-select form-select-lg w-100" aria-label="Target Language" name="target_lang">
                    <x-template :foreach="App\System\Language::alphabetically() as $language">
                        <option value="{{ $language->lower() }}">{{ $language->value }}</option>
                    </x-template>
                </select>
                <input type="file" class="form-control frame-file" name="file">
                <button type="submit" class="btn btn-primary btn-lg w-100">Translate</button>
            </form>
        </div>
    </div>
</x-layout>
