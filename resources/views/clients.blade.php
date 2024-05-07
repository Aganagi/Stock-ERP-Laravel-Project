@extends('layouts.app')
@section('title')
    Clients
@endsection
@section('clients')
    @include('dashboard')
    {{-- add new client modal start --}}
    <div class="modal fade" id="addClientModal" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New Client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="#" method="POST" id="add_client_form" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4 bg-light">
                        <div class="row">
                            <div class="col">
                                <label for="name">First name</label>
                                <input type="text" name="name" class="form-control" placeholder="First name"
                                    aria-label="First name">
                                <span class="help-block name-error"></span>
                            </div>
                            <div class="col">
                                <label for="surname">Last name</label>
                                <input type="text" name="surname" class="form-control" placeholder="Last name"
                                    aria-label="Last name">
                                <span class="help-block surname-error"></span>
                            </div>
                        </div>
                        <div class="my-2">
                            <label for="email">E-mail</label>
                            <input type="mail" name="email" class="form-control" placeholder="Enter mail adress">
                            <span class="help-block email-error"></span>
                        </div>
                        <div class="my-2">
                            <label for="company">Company</label>
                            <input type="text" name="company" class="form-control" placeholder="Enter company name">
                            <span class="help-block company-error"></span>
                        </div>
                        <div class="my-2">
                            <label for="phone">Phone number</label>
                            <input type="text" name="phone" class="form-control" placeholder="Enter phone number">
                            <span class="help-block phone-error"></span>
                        </div>
                        <div class="my-2">
                            <label for="image">Select image</label>
                            <input type="file" name="image" class="form-control">
                            <span class="help-block image-error"></span>
                        </div>
                        <div class="img-holder"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="add_client_btn" class="btn btn-dark">Add client</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- add new client modal end --}}

    {{-- edit client modal start --}}
    <div class="modal fade" id="editClientModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        data-bs-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="#" method="POST" id="edit_client_form" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="client_id" id="client_id">
                    <input type="hidden" name="client_image" id="client_image">
                    <div class="modal-body p-4 bg-light">
                        <div class="row">
                            <div class="col">
                                <label for="name">First name</label>
                                <input type="text" name="name" id="name" class="form-control"
                                    placeholder="First name">
                                <span class="help-block name-error"></span>
                            </div>
                            <div class="col">
                                <label for="surname">Lasrt name</label>
                                <input type="text" name="surname" id="surname" class="form-control"
                                    placeholder="Last name">
                                <span class="help-block surname-error"></span>
                            </div>
                        </div>
                        <div class="my-2">
                            <label for="email">E-mail</label>
                            <input type="mail" name="email" id="email" class="form-control"
                                placeholder="Enter mail adress">
                            <span class="help-block email-error"></span>
                        </div>
                        <div class="my-2">
                            <label for="company">Company</label>
                            <input type="text" name="company" id="company" class="form-control"
                                placeholder="Enter company name">
                            <span class="help-block company-error"></span>
                        </div>
                        <div class="my-2">
                            <label for="phone">Phone number</label>
                            <input type="text" name="phone" id="phone" class="form-control"
                                placeholder="Enter phone number">
                            <span class="help-block phone-error"></span>
                        </div>
                        <div class="my-2">
                            <label for="image">Select image</label>
                            <input type="file" name="image" class="form-control">
                            <span class="help-block image-error"></span>
                        </div>
                        <div class="mt-2" id="image">
                            @if (!empty($client->client_image))
                                <img id="existingImage" src="{{ asset('storage/' . $client->client_image) }}"
                                    class="img-fluid" style="max-width: 100px; margin-bottom: 10px;">
                            @endif
                        </div>
                        <div class="mt-2">
                            <div id="imagePreview" class="img-holder">
                                @if (!empty($client->client_image))
                                    <img src="{{ asset('storage/' . $client->client_image) }}" class="img-fluid"
                                        style="max-width: 100px; margin-bottom: 10px;">
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="edit_client_btn" class="btn btn-dark">Update Client</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- edit client modal end --}}

    <body class="bg-light">
        <div class="container">
            <div class="row my-5">
                <div class="col-lg-12">
                    <div class="card shadow">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="list">Clients List</h3>
                            <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addClientModal"><i
                                    class="bi-plus-circle me-2"></i>Add New Client</button>
                        </div><br>
                        <div class="card-body" id="show_all_clients">
                            <h1 class="text-center text-secondary my-5">Loading...</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            //Reset input file
            $('input[type="file"][name="image"]').val('');
            //Image preview
            $('input[type="file"][name="image"]').on('change', function() {
                let img_path = $(this)[0].value;
                let img_holder = $('.img-holder');
                let extension = img_path.substring(img_path.lastIndexOf('.') + 1).toLowerCase();

                if (extension == 'jpeg' || extension == 'jpg' || extension == 'png') {
                    if (typeof(FileReader) != 'undefined') {
                        img_holder.empty();
                        let reader = new FileReader();
                        reader.onload = function(e) {
                            $('<img/>', {
                                'src': e.target.result,
                                'class': 'img-fluid',
                                'style': 'max-width:100px;margin-bottom:10px;'
                            }).appendTo(img_holder);
                        }
                        img_holder.show();
                        reader.readAsDataURL($(this)[0].files[0]);
                    } else {
                        $(img_holder).html('This browser does not support FileReader');
                    }
                } else {
                    $(img_holder).empty();
                }
            });
            $('#addClientModal').on('hidden.bs.modal', function() {
                $('input[type="file"][name="image"]').val(''); // Reset the file input
                $('.img-holder').empty(); // Clear the image preview
            });
            // Function to display image preview
            function displayImagePreview(input) {
                if (input.files && input.files[0]) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        $('#imagePreview').empty(); // Clear the image preview
                        $('#imagePreview').html('<img src="' + e.target.result +
                            '" class="img-fluid" style="max-width: 100px; margin-bottom: 10px;">');
                    };
                    reader.readAsDataURL(input.files[0]);
                } else {
                    $('#imagePreview').empty(); // Clear the image preview
                }
            }

            // Clear the image preview if the modal is closed without saving
            $('#editClientModal').on('hidden.bs.modal', function() {
                $('input[type="file"][name="image"]').val(''); // Reset the file input
                $('#imagePreview').empty(); // Clear the image preview
            });
            //AJAX------------------------------------------
            $(function() {

                // add new client ajax request
                $("#add_client_form").submit(function(e) {
                    e.preventDefault();
                    const fd = new FormData(this);
                    $(".help-block").html('');
                    $.ajax({
                        url: "{{ route('clients.store') }}",
                        method: 'post',
                        data: fd,
                        cache: false,
                        contentType: false,
                        processData: false,
                        dataType: 'json',
                        success: function(response) {
                            if (response.status == 200) {
                                Swal.fire(
                                    'Added!',
                                    'Client Added Successfully!',
                                    'success'
                                )
                                fetchAllClients();
                            }
                            $("#add_client_btn").text('Add client');
                            $("#add_client_form")[0].reset();
                            $("#addClientModal").modal('hide');
                        },
                        error: function(res) {
                            if (res.status == 422) {
                                let errors = res.responseJSON;
                                $.each(res.responseJSON, function(key, value) {
                                    $('.' + key + '-error').html(value[0]);
                                });
                            }
                        }
                    });
                });

                // edit client ajax request
                $(document).on('click', '.editIcon', function(e) {
                    e.preventDefault();
                    let id = $(this).attr('id');
                    $.ajax({
                        url: '{{ route('clients.edit') }}',
                        method: 'get',
                        data: {
                            id: id,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $('#name').val(response.name);
                            $("#surname").val(response.surname);
                            $("#email").val(response.email);
                            $("#company").val(response.company);
                            $("#phone").val(response.phone);
                            $("#image").html(
                                `<img src="storage/images/${response.image}" style="widht:80px; height:80px;">`
                            );
                            $("#client_id").val(response.id);
                            $("#client_image").val(response.image);
                        }
                    });
                });

                // update product ajax request
                $("#edit_client_form").submit(function(e) {
                    e.preventDefault();
                    const fd = new FormData(this);
                    $(".help-block").html('');
                    $.ajax({
                        url: "{{ route('clients.update') }}",
                        method: 'post',
                        data: fd,
                        cache: false,
                        contentType: false,
                        processData: false,
                        dataType: 'json',
                        success: function(response) {
                            if (response.status == 200) {
                                Swal.fire(
                                    'Updated!',
                                    'Client Updated Successfully!',
                                    'success'
                                )
                                fetchAllClients();
                            }
                            $("#edit_client_btn").text('Update Client');
                            $("#edit_client_form")[0].reset();
                            $("#editClientModal").modal('hide');
                        },
                        error: function(res) {
                            if (res.status == 422) {
                                let errors = res.responseJSON;
                                $.each(res.responseJSON, function(key, value) {
                                    $('.' + key + '-error').html(value[0]);
                                });
                            }
                        }
                    });
                });

                // delete client ajax request
                $(document).on('click', '.deleteIcon', function(e) {
                    e.preventDefault();
                    let id = $(this).attr('id');
                    let csrf = '{{ csrf_token() }}';
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        $.ajax({
                            url: "{{ route('clients.delete') }}",
                            method: 'delete',
                            data: {
                                id: id,
                                _token: csrf
                            },
                            success: function(response) {
                                if (response.status === 200) {
                                    Swal.fire(
                                        'Deleted!',
                                        'Your file has been deleted.',
                                        'success'
                                    )
                                    fetchAllClients();
                                }
                            },
                            error: function(xhr, textStatus, errorThrown) {
                                console.error(xhr.responseText);
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Warning!',
                                    text: 'This client has a confirmed order and cannot be removed!',
                                })
                            }
                        });
                    });
                });
                //Reset form when close modal-------
                $('.btn-close').on('click', function() {
                    $("#add_client_form")[0].reset();
                    $("#edit_client_form")[0].reset();
                    $(".help-block").html('');
                });
                $('.btn-secondary').on('click', function() {
                    $("#add_client_form")[0].reset();
                    $("#edit_client_form")[0].reset();
                    $(".help-block").html('');
                });

                // fetch all client ajax request
                fetchAllClients();

                function fetchAllClients() {
                    $.ajax({
                        url: '{{ route('clients.fetchAll') }}',
                        method: 'get',
                        success: function(response) {
                            $("#show_all_clients").html(response);
                            let table = $("table").DataTable({
                                dom: 'Bfrtip',
                                pageLength: 5,
                                buttons: [{
                                        extend: 'copyHtml5',
                                        text: '<i class="fas fa-copy"></i>',
                                        titleAttr: 'Copy to Clipboard' // Optional tooltip
                                    },
                                    {
                                        extend: 'excelHtml5',
                                        text: '<i class="fas fa-file-excel"></i>',
                                        titleAttr: 'Export to Excel' // Optional tooltip
                                    },
                                    {
                                        extend: 'csvHtml5',
                                        text: '<i class="fas fa-file-csv"></i>',
                                        titleAttr: 'Export to CSV' // Optional tooltip
                                    },
                                    {
                                        extend: 'pdfHtml5',
                                        text: '<i class="fas fa-file-pdf"></i>',
                                        titleAttr: 'Export to PDF' // Optional tooltip
                                    }
                                ],
                                columnDefs: [{
                                    searchable: false,
                                    orderable: false,
                                    targets: 0
                                }],
                                order: [
                                    [1, 'asc']
                                ]
                            });
                            table
                                .on('order.dt search.dt', function() {
                                    let i = 1;

                                    table
                                        .cells(null, 0, {
                                            search: 'applied',
                                            order: 'applied'
                                        })
                                        .every(function(cell) {
                                            this.data(i++);
                                        });
                                })
                                .draw();
                        }
                    });
                }
            });
        </script>
    </body>
@endsection
