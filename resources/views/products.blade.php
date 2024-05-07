@extends('layouts.app')
@section('title')
    Products
@endsection
@section('products')
    @include('dashboard')
    {{-- add new product modal start --}}
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="#" method="POST" id="add_product_form" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4 bg-light">
                        <div class="my-2">
                            <label>Brand</label>
                            <select name="brand_id" class="form-select" aria-label="Default select example">
                                <option disabled selected>Select brand</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->brand }}</option>
                                @endforeach
                            </select>
                            <span class="help-block brand_id-error"></span>
                        </div>
                        <div class="my-2">
                            <label for="product">Product</label>
                            <input type="text" name="product" class="form-control" placeholder="Enter product">
                            <span class="help-block product-error"></span>
                        </div>
                        <div class="my-2">
                            <label for="buy">Purchase price</label>
                            <input type="text" name="buy" class="form-control" placeholder="Enter purchase price">
                            <span class="help-block buy-error"></span>
                        </div>
                        <div class="my-2">
                            <label for="sell">Sell price</label>
                            <input type="text" name="sell" class="form-control" placeholder="Enter sell price">
                            <span class="help-block sell-error"></span>
                        </div>
                        <div class="my-2">
                            <label for="quantity">Product Quantity</label>
                            <input type="text" name="quantity" class="form-control" placeholder="Enter quantity">
                            <span class="help-block quantity-error"></span>
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
                        <button type="submit" id="add_product_btn" class="btn btn-dark">Add product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- add new product modal end --}}

    {{-- edit product modal start --}}
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        data-bs-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="#" method="POST" id="edit_product_form" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="product_id" id="product_id">
                    <input type="hidden" name="product_image" id="product_image">
                    <div class="modal-body p-4 bg-light">
                        <div class="my-2">
                            <label>Brand</label>
                            <select name="brand_id" id="brand_id" class="form-select">
                                <option disabled selected>Select brand</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}"
                                        {{ $brand->id == old('brand_id') ? 'selected' : '' }}>
                                        {{ $brand->brand }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="help-block brand_id-error"></span>
                        </div>
                        <div class="my-2">
                            <label for="product">Product</label>
                            <input type="text" name="product" id="product" class="form-control"
                                placeholder="Enter product">
                            <span class="help-block product-error"></span>
                        </div>
                        <div class="my-2">
                            <label for="buy">Purchase price</label>
                            <input type="text" name="buy" id="buy" class="form-control"
                                placeholder="Enter purchase price">
                            <span class="help-block buy-error"></span>
                        </div>
                        <div class="my-2">
                            <label for="sell">Sell price</label>
                            <input type="text" name="sell" id="sell" class="form-control"
                                placeholder="Enter sell price">
                            <span class="help-block sell-error"></span>
                        </div>
                        <div class="my-2">
                            <label for="quantity">Product Quantity</label>
                            <input type="text" name="quantity" id="quantity" class="form-control"
                                placeholder="Enter product quantity">
                            <span class="help-block quantity-error"></span>
                        </div>
                        <div class="my-2">
                            <label for="image">Select image</label>
                            <input type="file" name="image" class="form-control">
                            <span class="help-block image-error"></span>
                        </div>
                        <div class="mt-2" id="image">
                            @if (!empty($product->product_image))
                                <img id="existingImage" src="{{ asset('storage/' . $product->product_image) }}"
                                    class="img-fluid" style="max-width: 100px; margin-bottom: 10px;">
                            @endif
                        </div>
                        <div class="mt-2">
                            <div id="imagePreview" class="img-holder">
                                @if (!empty($product->product_image))
                                    <img src="{{ asset('storage/' . $product->product_image) }}" class="img-fluid"
                                        style="max-width: 100px; margin-bottom: 10px;">
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="edit_product_btn" class="btn btn-dark">Update Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- edit product modal end --}}

    <body class="bg-light">
        <div class="container">
            <div class="row my-5">
                <div class="col-lg-12">
                    <div class="card shadow">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="list">Products List</h3>
                            <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addProductModal"><i
                                    class="bi-plus-circle me-2"></i>Add New Product</button>
                        </div><br>
                        <div class="card-body" id="show">
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
            $('#addProductModal').on('hidden.bs.modal', function() {
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
            $('#editProductModal').on('hidden.bs.modal', function() {
                $('input[type="file"][name="image"]').val(''); // Reset the file input
                $('#imagePreview').empty(); // Clear the image preview
            });
            //AJAX------------------------------------------------
            $(function() {

                // add new product ajax request
                $("#add_product_form").submit(function(e) {
                    e.preventDefault();
                    const fd = new FormData(this);
                    $(".help-block").html('');
                    $.ajax({
                        url: '{{ route('products.store') }}',
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
                                    'Product Added Successfully!',
                                    'success'
                                )
                                fetchAllProducts();
                            }
                            $("#add_product_btn").text('Add product');
                            $("#add_product_form")[0].reset();
                            $("#addProductModal").modal('hide');
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

                // edit product ajax request
                $(document).on('click', '.editIcon', function(e) {
                    e.preventDefault();
                    let id = $(this).attr('id');
                    $.ajax({
                        url: "{{ route('products.edit') }}",
                        method: 'get',
                        data: {
                            id: id,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $('#brand_id').val(response.brand_id);
                            $("#product").val(response.product);
                            $("#buy").val(response.buy);
                            $("#sell").val(response.sell);
                            $("#quantity").val(response.quantity);
                            $("#image").html(
                                `<img src="storage/images/${response.image}" style="widht:70px; height:70px;">`
                            );
                            $("#product_id").val(response.id);
                            $("#product_image").val(response.image);
                        }
                    });
                });

                // update product ajax request
                $("#edit_product_form").submit(function(e) {
                    e.preventDefault();
                    const fd = new FormData(this);
                    $(".help-block").html('');
                    $.ajax({
                        url: '{{ route('products.update') }}',
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
                                    'Product Updated Successfully!',
                                    'success'
                                )
                                fetchAllProducts();
                            }
                            $("#edit_product_btn").text('Update Product');
                            $("#edit_product_form")[0].reset();
                            $("#editProductModal").modal('hide');
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

                // delete product ajax request
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
                                url: '{{ route('products.delete') }}',
                                method: 'delete',
                                data: {
                                    id: id,
                                    _token: csrf
                                },
                                success: function(response) {
                                    if (response.status === 200) {
                                        swalWithBootstrapButtons.fire(
                                            'Deleted!',
                                            'Your file has been deleted.',
                                            'success'
                                        )
                                        fetchAllProducts();
                                    }
                                },
                                error: function(xhr, textStatus, errorThrown) {
                                    console.error(xhr.responseText);
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Warning!',
                                        text: 'This product has a confirmed order and cannot be removed!',
                                    })
                                }
                            });
                        }
                    });
                });
                //Reset form when close modal ------------------
                $('.btn-close').on('click', function() {
                    $("#add_product_form")[0].reset();
                    $("#edit_product_form")[0].reset();
                    $(".help-block").html('');
                });
                $('.btn-secondary').on('click', function() {
                    $("#add_product_form")[0].reset();
                    $("#edit_product_form")[0].reset();
                    $(".help-block").html('');
                });

                // fetch all product ajax request
                fetchAllProducts();

                function fetchAllProducts() {
                    $.ajax({
                        url: '{{ route('products.fetchAll') }}',
                        method: 'get',
                        success: function(response) {
                            $("#show").html(response);
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
