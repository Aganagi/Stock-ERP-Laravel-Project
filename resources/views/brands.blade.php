@extends('layouts.app')
@section('title')
    Brands
@endsection
@section('brands')
    @include('dashboard')
    {{-- add new brand modal start --}}
    <div class="modal fade" id="addBrandModal" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New Brand</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="#" method="POST" id="add_brand_form" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4 bg-light">
                        <div class="my-2">
                            <label for="brand">Brand</label>
                            <input type="text" name="brand" class="form-control" placeholder="Emter brand">
                            <span class="help-block brand-error"></span>
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
                        <button type="submit" id="add_brand_btn" class="btn btn-dark">Add Brand</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- add new brand modal end --}}

    {{-- edit brand modal start --}}
    <div class="modal fade" id="editBrandModal" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Brand</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="#" method="POST" id="edit_brand_form" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="brand_id" id="brand_id">
                    <input type="hidden" name="brand_image" id="brand_image">
                    <div class="modal-body p-4 bg-light">
                        <div class="my-2">
                            <label for="brand">Brand</label>
                            <input type="brand" name="brand" id="brand" class="form-control"
                                placeholder="Enter brand">
                            <span class="help-block brand-error"></span>
                        </div>
                        <div class="my-2">
                            <label for="image">Select image</label>
                            <input type="file" name="image" class="form-control">
                            <span class="help-block image-error"></span>
                        </div>
                        <div class="mt-2" id="image">
                            @if (!empty($brand->brand_image))
                                <img src="{{ asset('storage/' . $brand->brand_image) }}" class="img-fluid"
                                    style="max-width: 100px; margin-bottom: 10px;">
                            @endif
                        </div>
                        <div class="mt-2">
                            <div id="imagePreview" class="img-holder"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="edit_brand_btn" class="btn btn-dark">Update Brand</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- edit brand modal end --}}

    <body class="bg-light">
        <div class="container">
            <div class="row my-5">
                <div class="col-lg-12">
                    <div class="card shadow">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="list">Brands List</h3>
                            <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addBrandModal"><i
                                    class="bi-plus-circle me-2"></i>Add New Brand</button>
                        </div><br>
                        <div class="card-body" id="show_all_brands">
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
            $('#addBrandModal').on('hidden.bs.modal', function() {
                $('input[type="file"][name="image"]').val(''); // Reset the file input
                $('.img-holder').empty(); // Clear the image preview
            });
            // Function to display image preview
            function displayImagePreview(input) {
                if (input.files && input.files[0]) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        $('#imagePreview').html('<img src="' + e.target.result +
                            '" class="img-fluid" style="max-width: 100px; margin-bottom: 10px;">');
                    };
                    reader.readAsDataURL(input.files[0]);
                } else {
                    $('#imagePreview').empty();
                }
            }

            // Trigger the image preview function when a new image is selected
            $('input[type="file"][name="image"]').on('change', function() {
                displayImagePreview(this);
            });

            // Clear the image preview if the modal is closed without saving
            $('#editBrandModal').on('hidden.bs.modal', function() {
                $('input[type="file"][name="image"]').val(''); // Reset the file input
                $('#imagePreview').empty(); // Clear the image preview
            });

            //AJAX-------------------------------------------------
            $(function() {
                // add new brand ajax request
                $("#add_brand_form").submit(function(e) {
                    e.preventDefault();
                    const fd = new FormData(this);
                    $(".help-block").html('');
                    $.ajax({
                        url: "{{ route('brands.store') }}",
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
                                    'Brand Added Successfully!',
                                    'success'
                                )
                                fetchAllBrands();
                            }
                            $("#add_brand_btn").text('Add Brand');
                            $("#add_brand_form")[0].reset();
                            $("#addBrandModal").modal('hide');
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

                // edit brand ajax request
                $(document).on('click', '.editIcon', function(e) {
                    e.preventDefault();
                    let id = $(this).attr('id');
                    $.ajax({
                        url: "{{ route('brands.edit') }}",
                        method: 'get',
                        data: {
                            id: id,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $("#brand").val(response.brand);
                            $("#image").html(
                                `<img src="storage/images/${response.image}" style="widht:50px; height:50px;">`
                            );
                            $("#brand_id").val(response.id);
                            $("#brand_image").val(response.image);
                        }
                    });
                });

                // update brand ajax request
                $("#edit_brand_form").submit(function(e) {
                    e.preventDefault();
                    const fd = new FormData(this);
                    $(".help-block").html('');
                    $.ajax({
                        url: '{{ route('brands.update') }}',
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
                                    'Brand Updated Successfully!',
                                    'success'
                                )
                                fetchAllBrands();
                            }
                            $("#edit_brand_btn").text('Update Brand');
                            $("#edit_brand_form")[0].reset();
                            $("#editBrandModal").modal('hide');
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

                // delete brand ajax request
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
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "{{ route('brands.delete') }}",
                                method: 'delete',
                                data: {
                                    id: id,
                                    _token: csrf
                                },
                                success: function(response) {
                                    console.log(response);
                                    Swal.fire(
                                        'Deleted!',
                                        'Your file has been deleted.',
                                        'success'
                                    )
                                    fetchAllBrands();
                                }
                            });
                        }
                    })
                });

                $('.btn-close').on('click', function() {
                    $("#add_brand_form")[0].reset();
                    $("#edit_brand_form")[0].reset();
                    $(".help-block").html('');
                });
                $('.btn-secondary').on('click', function() {
                    $("#add_brand_form")[0].reset();
                    $("#edit_brand_form")[0].reset();
                    $(".help-block").html('');
                });

                // fetch all brands ajax request
                fetchAllBrands();

                function fetchAllBrands() {
                    $.ajax({
                        url: "{{ route('brands.fetchAll') }}",
                        method: 'get',
                        success: function(response) {
                            $("#show_all_brands").html(response);
                            let table = $("table").DataTable({
                                dom: 'Bfrtip',
                                pageLength: 5,
                                buttons: [{
                                        extend: 'copyHtml5',
                                        text: '<i class="fas fa-copy"></i>',
                                        titleAttr: 'Copy to Clipboard'
                                    },
                                    {
                                        extend: 'excelHtml5',
                                        text: '<i class="fas fa-file-excel"></i>',
                                        titleAttr: 'Export to Excel'
                                    },
                                    {
                                        extend: 'csvHtml5',
                                        text: '<i class="fas fa-file-csv"></i>',
                                        titleAttr: 'Export to CSV'
                                    },
                                    {
                                        extend: 'pdfHtml5',
                                        text: '<i class="fas fa-file-pdf"></i>',
                                        titleAttr: 'Export to PDF'
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
