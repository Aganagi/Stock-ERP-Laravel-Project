@extends('layouts.app')
@section('title')
    Tasks
@endsection
@section('tasks')
    @include('dashboard')
    {{-- add new task modal start --}}
    <div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="#" method="POST" id="addTaskForm">
                    @csrf
                    <div class="modal-body p-4 bg-light">
                        <div class="row">
                            <div class="my-2">
                                <label for="task">Task</label>
                                <input type="text" name="task" class="form-control" placeholder="Enter task"
                                    autocomplete="off">
                                <span class="help-block task-error"></span>
                            </div>
                        </div>
                        <div class="my-2">
                            <label for="date">Date</label>
                            <input type="date" name="date" class="form-control" autocomplete="off">
                            <span class="help-block date-error"></span>
                        </div>
                        <div class="my-2">
                            <label for="time">Time</label>
                            <input type="time" name="time" class="form-control" autocomplete="off">
                            <span class="help-block time-error"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="add_task_btn" class="btn btn-dark">Add TAsk</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- add new task modal end --}}

    {{-- edit task modal start --}}
    <div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="exampleModalLabel" data-bs-backdrop="static"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit TAsk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="#" method="POST" id="editTaskForm">
                    @csrf
                    <div class="modal-body p-4 bg-light">
                        <div class="row">
                            <input type="hidden" name="task_id" id="task_id">
                            <div class="my-2">
                                <label for="task">Task</label>
                                <input type="text" name="task" class="form-control" id="task"
                                    placeholder="Enter task" autocomplete="off">
                                <span class="help-block title-error"></span>
                            </div>
                        </div>
                        <div class="my-2">
                            <label for="date">Date</label>
                            <input type="date" name="date" class="form-control" id="date" autocomplete="off">
                            <span class="help-block date-error"></span>
                        </div>
                        <div class="my-2">
                            <label for="time">Time</label>
                            <input type="time" name="time" class="form-control" id="time" autocomplete="off">
                            <span class="help-block time-error"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="edit_task_btn" class="btn btn-dark">Edit TAsk</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- edit task modal end --}}

    <body class="bg-light">
        <div class="container">
            <div class="row my-5">
                <div class="col-lg-12">
                    <div class="card shadow">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="list">Tasks List</h3>
                            <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addTaskModal"><i
                                    class="bi-plus-circle me-2"></i>Add New Task</button>
                        </div><br>
                        <div class="card-body" id="show_all_tasks">
                            <h1 class="text-center text-secondary my-5">Loading...</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            $(function() {
                // add new task ajax request
                $("#addTaskForm").submit(function(e) {
                    e.preventDefault();
                    const fd = new FormData(this);
                    $(".help-block").html('');
                    $.ajax({
                        url: "{{ route('task.store') }}",
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
                                    'Task Added Successfully!',
                                    'success'
                                )
                                fetchAllTasks();
                            }
                            $("#add_task_btn").text('Add Task');
                            $("#addTaskForm")[0].reset();
                            $("#addTaskModal").modal('hide');
                        },
                        error: function(res) {
                            if (res.status == 422) {
                                let errors = res.responseJSON;
                                $.each(res.responseJSON, function(key, value) {
                                    $('.' + key + '-error').html(value[0]);
                                });
                            }
                            $('.btn-close', '.btn-secondary').on('click', function() {
                                $("#addTaskForm")[0].reset();
                                $(".help-block").html('');
                            });
                        }
                    });
                });

                // edit task ajax request
                $(document).on('click', '.editIcon', function(e) {
                    e.preventDefault();
                    let id = $(this).attr('id');
                    $.ajax({
                        url: "{{ route('task.edit') }}",
                        method: 'get',
                        data: {
                            id: id,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $("#task").val(response.task);
                            $("#task_id").val(response.id);
                            $("#date").val(response.date);
                            $("#time").val(response.time);
                        }
                    });
                });

                // update task ajax request
                $("#editTaskForm").submit(function(e) {
                    e.preventDefault();
                    $(".help-block").html('');
                    const fd = new FormData(this);
                    $.ajax({
                        url: "{{ route('task.update') }}",
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
                                    'Task Updated Successfully!',
                                    'success'
                                )
                                fetchAllTasks();
                            }
                            $("#edit_task_btn").text('Update Task');
                            $("#editTaskForm")[0].reset();
                            $("#editTaskModal").modal('hide');
                        },
                        error: function(response) {
                            if (response.status == 422) {
                                let errors = response.responseJSON;
                                $.each(response.responseJSON, function(key, value) {
                                    $('.' + key + '-error').html(value[0]);
                                });
                            }
                            $('.btn-close', '.btn-secondary').on('click', function() {
                                $("#editTaskForm")[0].reset();
                                $(".help-block").html('');
                            });
                        }
                    });
                });

                // delete task ajax request
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
                                url: "{{ route('task.delete') }}",
                                method: 'delete',
                                data: {
                                    id: id,
                                    _token: csrf
                                },
                                success: function(response) {
                                    console.log(response);
                                    toastr.error('Task Dleted Successfully!', 'Success!', {
                                        timeOut: 12000
                                    }, {
                                        "class": "custom-toast-error"
                                    });
                                    fetchAllTasks();
                                }
                            });
                        }
                    });
                });

                // fetch all task ajax request
                fetchAllTasks();

                function fetchAllTasks() {
                    $.ajax({
                        url: "{{ route('task.fetchAll') }}",
                        method: 'get',
                        success: function(response) {
                            $("#show_all_tasks").html(response);
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

            function updateCountdown() {
                let countdownElements = document.querySelectorAll('.countdown');
                countdownElements.forEach(function(element) {
                    let endTime = new Date(element.getAttribute('data-end')).getTime();
                    let now = new Date().getTime();
                    let timeDifference = endTime - now;

                    if (timeDifference <= 0) {
                        element.textContent = 'Time expired';
                    } else {
                        let days = Math.floor(timeDifference / (1000 * 60 * 60 * 24));
                        let hours = Math.floor((timeDifference % (1000 * 60 * 60 * 24)) / (1000 *
                            60 * 60));
                        let minutes = Math.floor((timeDifference % (1000 * 60 * 60)) / (1000 * 60));
                        let seconds = Math.floor((timeDifference % (1000 * 60)) / 1000);

                        let countdownText = '';

                        if (days > 0) {
                            countdownText += days + ' day' + (days > 1 ? 's' : '') + ' ';
                        }

                        if (hours > 0 || days > 0) {
                            countdownText += hours + ':';
                        }

                        if (minutes > 0 || days > 0 || hours > 0) {
                            countdownText += (minutes < 10 ? '0' : '') + minutes + ':';
                        }

                        countdownText += (seconds < 10 ? '0' : '') + seconds;

                        element.textContent = countdownText;
                    }
                });
            }

            setInterval(updateCountdown, 1000);

            updateCountdown();
        </script>

    </body>
@endsection
