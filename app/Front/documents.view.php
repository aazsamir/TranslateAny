<x-base>
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
</x-base>
