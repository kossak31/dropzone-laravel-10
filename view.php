<!DOCTYPE html>
<html>

<head>
    <title>Laravel 10 Drag and Drop File Upload with Dropzone JS - ItSolutionStuff.com</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css" type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Laravel 10 Drag and Drop File Upload with Dropzone JS - ItSolutionStuff.com</h1>
                <form action="{{ route('upload.image') }}" class="dropzone " id="formDropzone" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group mb-3">
                        <label class="form-label text-muted opacity-75 fw-medium" for="formName">Name</label>
                        <input class="form-control border-2 shadow-none fw-bold p-3" id="name" name="name" type="text" required>
                        <div class="invalid-feedback fw-bold">The name field is required.</div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label text-muted opacity-75 fw-medium" for="formEmail">Email</label>
                        <input class="form-control border-2 shadow-none fw-bold p-3" id="email" name="email" type="email" required>
                        <div class="invalid-feedback fw-bold">The email field is required.</div>
                    </div>

                </form>
                <button class="btn btn-primary fw-medium py-3 px-4 mt-3" id="upload-button" type="submit">
                    <span class="spinner-border spinner-border-sm d-none me-2" aria-hidden="true"></span>
                    Submit Form
                </button>

                {{-- <form action="{{ route('upload.image') }}" method="POST" enctype="multipart/form-data"
                id="image-upload" class="dropzone" multiple>

                @csrf


                <button id="upload-button" class="btn btn-primary fw-medium py-3 px-4 mt-3" id="formSubmit" type="submit">
                    <span class="spinner-border spinner-border-sm d-none me-2" aria-hidden="true"></span>
                    Submit Form
                </button>
                </form> --}}
            </div>
        </div>
    </div>



    <script type="text/javascript">
        Dropzone.autoDiscover = false;

        $(document).ready(function() {

            var dropzone = new Dropzone('#image-upload', {
                dictDefaultMessage: 'Solte os ficheiros aqui para enviar ',
                dictFallbackMessage: 'O seu navegador não suporta o envio de ficheiros por arrastar e largar.',
                dictFallbackText: 'Por favor, utilize o formulário abaixo para enviar os seus ficheiros como antigamente.',
                dictFileTooBig: 'O ficheiro é demasiado grande . Tamanho máximo permitido: 5 MiB.',
                dictInvalidFileType: 'Não pode enviar ficheiros deste tipo.',
                dictResponseError: 'O servidor respondeu com o código 500.',
                dictCancelUpload: 'Cancelar envio',
                dictCancelUploadConfirmation: 'Tem a certeza de que deseja cancelar este envio?',
                dictRemoveFile: 'Remover ficheiro',
                dictMaxFilesExceeded: 'Não pode enviar mais ficheiros.',
                paramName: "files",

                addRemoveLinks: true,
                uploadMultiple: true,
                autoProcessQueue: false,

                maxFiles: 5,
                parallelUploads: 10,
                acceptedFiles: ".jpeg,.jpg,.png,.gif",
                init: function() {
                    $.ajax({
                        url: '{{ route('
                        readFiles ') }}',
                        type: 'get',
                        dataType: 'json',
                        success: function(response) {

                            $.each(response, function(key, value) {
                                var mockFile = {
                                    name: value.name,
                                    size: value.size
                                };

                                dropzone.emit("addedfile", mockFile);
                                dropzone.emit("thumbnail", mockFile, value.path);
                                dropzone.emit("complete", mockFile);

                            });

                        }
                    });

                    this.on("addedfile", function(file) {

                        // Add an input field to capture the order
                        $(file.previewElement).append(
                            '<input type="text" name="file_order[]" value="' + dropzone
                            .files.length + '">');

                    });

                },
                removedfile: function(file) {

                    var name = file.name;
                    $.ajax({

                        type: 'POST',
                        url: "{{ route('deleteFile') }}",

                        data: {
                            "_token": "{{ csrf_token() }}",
                            filename: name
                        },
                        success: function(data) {
                            console.log("File has been successfully removed!!");
                        },
                        error: function(e) {
                            console.log(e);
                        }
                    });
                    var fileRef;
                    return (fileRef = file.previewElement) != null ?
                        fileRef.parentNode.removeChild(file.previewElement) : void 0;
                },

            });

            $("#upload-button").click(function() {
                // Before upload, update the order based on current file positions

                dropzone.processQueue(); // Manually trigger file upload
                $("#upload-button").hide()
                $('.dropzone').sortable('disable');
                $('.dropzone').hide();

            });


            $(".dropzone").sortable({
                items: '.dz-preview',
                cursor: 'grab',
                opacity: 0.5,
                containment: '.dropzone',
                distance: 20,
                tolerance: 'pointer',
                stop: function() {
                    var queue = dropzone.getAcceptedFiles();
                    newQueue = [];
                    $(' .dz-preview .dz-filename [data-dz-name]').each(function(count, el) {
                        var name = el.innerHTML;
                        queue.forEach(function(file) {
                            if (file.name === name) {
                                newQueue.push(file);
                            }
                        });
                    });
                    dropzone.files = newQueue;

                    updateFileOrder()
                }
            });

            function updateFileOrder() {
                // Update the order of files based on their positions in the dropzone
                $("#image-upload .dz-preview").each(function(index) {
                    $(this).find('input[name="file_order[]"]').val(index);
                });
            }
        });
    </script>
</body>

</html>