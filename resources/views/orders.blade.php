@extends('layouts.app')
@section('title')
    Orders
@endsection
@section('orders')
    @include('dashboard')
    {{-- add new order modal start --}}
    <div class="modal fade" id="addOrderModal" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="add_order_form">
                    @csrf
                    <div class="modal-body p-4 bg-light">
                        <div class="my-2">
                            <label for="client_id">Client</label>
                            <select name="client_id" class="form-select" aria-label="Default select example">
                                <option disabled selected>Select Client</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->name }} {{ $client->surname }}</option>
                                @endforeach
                            </select>
                            <span class="help-block client_id-error"></span>
                        </div>
                        <div class="my-2">
                            <label for="product_id">Product</label>
                            <select name="product_id" class="form-select" aria-label="Default select example">
                                <option disabled selected>Select Product</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->brand->brand }}
                                        {{ $product->product }}({{ $product->quantity }})</option>
                                @endforeach
                            </select>
                            <span class="help-block product_id-error"></span>
                        </div>
                        <div class="my-2">
                            <label for="amount">Order Quantity</label>
                            <input type="text" name="amount" class="form-control" placeholder="Enter quantity">
                            <span class="help-block amount-error"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="add_order_btn" class="btn btn-dark">Add order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- add new order modal end --}}

    {{-- edit order modal start --}}
    <div class="modal fade" id="editOrderModal" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="edit_order_form">
                    @csrf
                    <input type="hidden" name="order_id" id="order_id">
                    <div class="modal-body p-4 bg-light">
                        <div class="my-2">
                            <label for="client_id">Client</label>
                            <select name="client_id" id="client_id" class="form-select" aria-label="Default select example">
                                <option disabled selected>Chose brand</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}"
                                        {{ $client->id == old('client_id') ? 'selected' : '' }}>
                                        {{ $client->name }} {{ $client->surname }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="help-block client_id-error"></span>
                        </div>
                        <div class="my-2">
                            <label for="product_id">Product</label>
                            <select name="product_id" id="product_id" class="form-select"
                                aria-label="Default select example">
                                <option disabled selected>Chose Product</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}"
                                        {{ $product->id == old('product_id') ? 'selected' : '' }}>
                                        {{ $product->brand->brand }} - {{ $product->product }}
                                        ({{ $product->quantity }})
                                    </option>
                                @endforeach
                            </select>
                            <span class="help-block product_id-error"></span>
                        </div>
                        <div class="my-2">
                            <label for="amount">Order Quantity</label>
                            <input type="text" name="amount" id="amount" class="form-control"
                                placeholder="Enter order quantity">
                            <span class="help-block amount-error"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="edit_order_btn" class="btn btn-dark">Update Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- edit order modal end --}}

    <body class="bg-light">
        <div class="container">
            <div class="row my-5">
                <div class="col-lg-12">
                    <div class="card shadow">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="list">Orders List</h3>
                            <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addOrderModal"><i
                                    class="bi-plus-circle me-2"></i>Add New Order</button>
                        </div><br>
                        <div class="card-body" id="show_all_orders">
                            <h1 class="text-center text-secondary my-5">Loading...</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $(function() {
                // add new order ajax request
                $("#add_order_form").submit(function(e) {
                    e.preventDefault();
                    const fd = new FormData(this);
                    $(".help-block").html('');
                    $.ajax({
                        url: "{{ route('orders.store') }}",
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
                                    'Order Added Successfully!',
                                    'success'
                                )
                                fetchAllOrders();
                            }
                            $("#add_order_btn").text('Add Order');
                            $("#add_order_form")[0].reset();
                            $("#addOrderModal").modal('hide');
                        },
                        error: function(res) {
                            if (res.status == 422) {
                                let errors = res.responseJSON;
                                $.each(errors.errors, function(key, value) {
                                    $('.' + key + '-error').html(value[0]);
                                });
                                $('.btn-close').on('click', function() {
                                    $("#add_order_form")[0].reset();
                                    $(".help-block").html('');
                                });
                                $('.btn-secondary').on('click', function() {
                                    $("#add_order_form")[0].reset();
                                    $(".help-block").html('');
                                });
                            }
                        }
                    });
                });

                // edit order ajax request
                $(document).on('click', '.editIcon', function(e) {
                    e.preventDefault();
                    let id = $(this).attr('id');
                    $.ajax({
                        url: "{{ route('orders.edit') }}",
                        method: 'get',
                        data: {
                            id: id,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $('#client_id').val(response.client_id);
                            $("#product_id").val(response.product_id);
                            $("#amount").val(response.amount);
                            $("#order_id").val(response.id);
                        }
                    });
                });

                // update order ajax request
                $("#edit_order_form").submit(function(e) {
                    e.preventDefault();
                    const fd = new FormData(this);
                    $(".help-block").html('');
                    $.ajax({
                        url: "{{ route('orders.update') }}",
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
                                    'Order Updated Successfully!',
                                    'success'
                                )
                                fetchAllOrders();
                            }
                            $("#edit_order_btn").text('Update Order');
                            $("#edit_order_form")[0].reset();
                            $("#editOrderModal").modal('hide');
                        },
                        error: function(res) {
                            if (res.status == 422) {
                                let errors = res.responseJSON;
                                $.each(errors.errors, function(key, value) {
                                    $('.' + key + '-error').html(value[0]);
                                });
                            }
                            $('.btn-close').on('click', function() {
                                $("#edit_order_form")[0].reset();
                                $(".help-block").html('');
                            });
                            $('.btn-secondary').on('click', function() {
                                $("#edit_order_form")[0].reset();
                                $(".help-block").html('');
                            });
                        }
                    });
                });

                // delete order ajax request
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
                                url: "{{ route('orders.delete') }}",
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
                                    fetchAllOrders();
                                }
                            });
                        }
                    });
                });
                //confirm order ajax request
                $(document).on('click', '.confirm', function() {
                    let id = $(this).attr('id');
                    let csrf = '{{ csrf_token() }}';
                    $.ajax({
                        url: "{{ route('orders.confirmOrder') }}",
                        method: 'get',
                        data: {
                            id: id
                        },
                        success: function(data) {
                            if (data.status == 200) {
                                Swal.fire(
                                    'Done!',
                                    'You order has been confirmed!',
                                    'success'
                                )
                                fetchAllOrders();
                            }
                        },
                        error: function(response) {
                            if (response.status == 422) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Warning!',
                                    text: 'Insufficient quantity of products to confirm order!',
                                })
                                fetchAllOrders();
                            }
                        }
                    });
                });
                //cancel order ajax request
                $(document).on('click', '.cancel', function(e) {
                    e.preventDefault();
                    let id = $(this).attr('id');
                    let csrf = '{{ csrf_token() }}';
                    $.ajax({
                        url: "{{ route('orders.cancelOrder') }}",
                        method: 'GET',
                        data: {
                            id: id,
                            _token: csrf
                        },
                        success: function(response) {
                            console.log(response);
                            Swal.fire(
                                'Done..',
                                'The order was successfully canceled!',
                                'success'
                            )
                            fetchAllOrders();
                        },
                        error: function(response) {
                            console.log(response);
                            if (reponse.status == 422) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'The order was not canceed!',
                                })
                                fetchAllOrders();
                            }
                        }
                    });
                });
                // fetch all order ajax request
                fetchAllOrders();

                function fetchAllOrders() {
                    $.ajax({
                        url: "{{ route('orders.fetchAll') }}",
                        method: 'get',
                        success: function(response) {
                            $("#show_all_orders").html(response);
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
