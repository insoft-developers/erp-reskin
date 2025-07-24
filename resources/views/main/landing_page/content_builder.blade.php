<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/grapesjs@0.21.11/dist/grapes.min.js"></script>
    <script src="https://unpkg.com/grapesjs-lory-slider"></script>
    <script src="https://unpkg.com/grapesjs-preset-webpage@1.0.3"></script>
    <script src="https://unpkg.com/grapesjs-blocks-basic@1.0.2"></script>
    <script src="https://unpkg.com/grapesjs-plugin-forms@2.0.6"></script>
    <script src="https://unpkg.com/grapesjs-component-countdown@1.0.2"></script>
    <script src="https://unpkg.com/grapesjs-tabs@1.0.6"></script>
    <script src="https://unpkg.com/grapesjs-custom-code@1.0.2"></script>
    <script src="https://unpkg.com/grapesjs-touch@0.1.1"></script>
    <script src="https://unpkg.com/grapesjs-parser-postcss@1.0.3"></script>
    <script src="https://unpkg.com/grapesjs-tooltip@0.1.8"></script>
    <script src="https://unpkg.com/grapesjs-tui-image-editor@0.1.3"></script>
    <script src="https://unpkg.com/grapesjs-typed@2.0.1"></script>
    <script src="https://unpkg.com/@silexlabs/grapesjs-fonts"></script>
    <script src="https://unpkg.com/grapesjs-style-bg@2.0.2"></script>
    <script src="https://unpkg.com/grapesjs-templates@1.0.6"></script>
    <link href="https://unpkg.com/grapesjs/dist/css/grapes.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

    <style>
        body,
        html {
            margin: 0;
            height: 100%;
        }

        #gjs {
            width: 100%;
            min-height: 100vh;
        }
    </style>
</head>

<body>
    <div id="gjs">
    </div>
    <script>
        const projectID = '{{ $id }}'
        const projectEndpoint = '{{ route('landing-page.content-builder.store', ['id' => $id]) }}'
        const assetEndpoint = '{{ route('landing-page.content-builder.upload_file') }}';
        const deleteAssetEndpoint =
            '{{ route('landing-page.content-builder.remove_file') }}';
        const editPageRoute = '{{ route('landing-page.edit', ['landing_page' => $id]) }}'; // Route untuk edit page
        const initialHtml = `{!! $data->html_code !!}`;
        const initialCss = `{!! $data->css_code !!}`;

        document.addEventListener("DOMContentLoaded", function() {
            var editor = grapesjs.init({
                container: '#gjs',
                // Tambahkan konfigurasi editor di sini
                plugins: [
                    'gjs-blocks-basic', // Tambahkan plugin ini
                    'grapesjs-plugin-forms',

                    // extra
                    'grapesjs-component-countdown',
                    'grapesjs-custom-code',
                    'grapesjs-tooltip',
                    'grapesjs-tabs',
                    'grapesjs-typed',

                    'grapesjs-undo',
                    'grapesjs-redo',
                    'gjs-lory-slider',
                    'grapesjs-preset-webpage',
                    'grapesjs-touch',
                    'grapesjs-parser-postcss',
                    'grapesjs-tui-image-editor',
                    'grapesjs-style-bg',
                    'grapesjs-templates',
                ],
                pluginsOpts: {
                    'gjs-blocks-basic': {},
                    'grapesjs-plugin-forms': {},

                    'grapesjs-component-countdown': {},
                    'grapesjs-custom-code': {},
                    'grapesjs-tooltip': {},
                    'grapesjs-tabs': {},
                    'grapesjs-typed': {},
                    'grapesjs-undo': {},
                    'grapesjs-redo': {},
                    'gjs-lory-slider': {},
                    'grapesjs-preset-webpage': {},
                    'grapesjs-touch': {},
                    'grapesjs-parser-postcss': {},
                    'grapesjs-tui-image-editor': {},
                    'grapesjs-style-bg': {},
                    'grapesjs-templates': {},
                },
                storageManager: {
                    type: 'remote',
                    autosave: true,
                    autoload: false,
                    stepsBeforeSave: 3,
                    options: {
                        remote: {
                            // urlLoad: projectEndpoint,
                            urlStore: projectEndpoint,
                            fetchOptions: opts => (opts.method === 'POST' ? {
                                method: 'PATCH'
                            } : {}),
                            onStore: data => ({
                                id: projectID,
                                html: editor.getHtml(),
                                css: editor.getCss(),
                                _token: '{{ csrf_token() }}'
                            }),
                            onLoad: result => result.data,
                        }
                    }
                },
                assetManager: {
                    assets: {!! json_encode($assets) !!},
                    upload: assetEndpoint,
                    uploadName: 'files',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                }
            });

            editor.Panels.addPanel({
                id: 'panel-info',
                visible: true,
                content: '<div class="relative w-[300px]">' +
                    '<div class="absolute ml-[105px] w-[300px] top-[7px] text-[12px] text-left">' +
                    '<b>Update Terakhir</b>: <span class="updatedat">-</span></div>' +
                    '</div>'
            });

            editor.Panels.addButton('options', {
                id: 'save',
                className: 'fa fa-save',
                command: 'custom-save',
                attributes: {
                    title: 'Save'
                }
            });

            editor.Commands.add('custom-save', {
                run: function(editor, sender) {
                    // Logika penyimpanan menggunakan AJAX
                    const html = editor.getHtml();
                    const css = editor.getCss();

                    $.ajax({
                        url: projectEndpoint,
                        method: 'PATCH',
                        data: {
                            id: projectID,
                            html: html,
                            css: css,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $('.updatedat').html(moment(response.updated_at).format(
                                'DD-MM-YYYY HH:mm:ss'));
                        },
                        error: function(xhr, status, error) {
                            console.error('Gagal menyimpan: ', error);
                        }
                    });
                }
            });

            editor.Panels.addButton('options', {
                id: 'close',
                className: 'fa fa-times',
                command: 'custom-close',
                attributes: {
                    title: 'Close'
                }
            });

            editor.Commands.add('custom-close', {
                run: function(editor, sender) {
                    // Redirect ke halaman edit
                    window.location.href = editPageRoute;
                }
            });

            // Set initial HTML and CSS
            editor.setComponents(initialHtml);
            editor.setStyle(initialCss);

            editor.on('storage:after', (type) => {
                $('.updatedat').html(moment().format('DD-MM-YYYY HH:mm:ss'));
            });

            editor.on('load', (type) => {
                $('.updatedat').html(moment('{{ $data->last_update_content_at }}').fromNow());
            });

            editor.on('asset:remove', (asset) => {
                const path = asset.id
                $.ajax({
                    url: deleteAssetEndpoint,
                    method: 'POST',
                    data: {
                        path,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        console.log('File berhasil dihapus');
                    },
                    error: function(xhr, status, error) {
                        console.error('Gagal menghapus file: ', error);
                    }
                });
            });
        });
    </script>
</body>

</html>
