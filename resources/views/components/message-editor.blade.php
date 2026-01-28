@props([
    'name' => 'message',
    'placeholder' => 'Digite sua mensagem...',
    'uploadRoute',
])

<div>
    <textarea id="{{ $name }}_editor"
              name="{{ $name }}"
              class="hidden"></textarea>

    <div id="{{ $name }}_ck"></div>

    <p class="mt-1 text-xs text-slate-500">
        💡 Você pode digitar texto, colar prints (Ctrl+V) ou enviar imagens.
    </p>
</div>

@once
    @push('scripts')
        <!-- CKEditor 5 -->
        <script src="https://cdn.ckeditor.com/ckeditor5/40.2.0/classic/ckeditor.js"></script>
    @endpush
@endonce

@push('scripts')
<script>
(function () {

    class UploadAdapter {
        constructor(loader) {
            this.loader = loader;
        }

        upload() {
            return this.loader.file.then(file => {
                return new Promise((resolve, reject) => {

                    const data = new FormData();
                    data.append('upload', file);
                    data.append('_token', '{{ csrf_token() }}');

                    fetch('{{ $uploadRoute }}', {
                        method: 'POST',
                        body: data
                    })
                    .then(r => r.json())
                    .then(result => {
                        if (result.url) {
                            resolve({ default: result.url });
                        } else {
                            reject(result.error || 'Erro no upload');
                        }
                    })
                    .catch(err => reject(err));
                });
            });
        }

        abort() {}
    }

    function UploadAdapterPlugin(editor) {
        editor.plugins.get('FileRepository').createUploadAdapter =
            loader => new UploadAdapter(loader);
    }

    ClassicEditor.create(document.querySelector('#{{ $name }}_ck'), {
        extraPlugins: [UploadAdapterPlugin],
        placeholder: '{{ $placeholder }}',
        toolbar: [
            'bold','italic','link',
            'bulletedList','numberedList',
            '|','imageUpload','blockQuote',
            'undo','redo'
        ]
    }).then(editor => {

        const textarea = document.querySelector('[name="{{ $name }}"]');

        editor.model.document.on('change:data', () => {
            textarea.value = editor.getData();
        });
    });

})();
</script>
@endpush
