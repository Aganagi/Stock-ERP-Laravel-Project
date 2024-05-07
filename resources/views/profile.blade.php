@extends('layouts.app')
@section('title')
    Profile
@endsection
@section('profile')
    @include('dashboard')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <h5 class="card-header" style="color:#ffffff;">Profile Details</h5>&nbsp;
                        <!-- Account -->
                        <div class="card-body">
                            <form method="POST" id="edit_form" action="#" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <input type="hidden" value="{{ $user->id }}" name="id">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start align-items-sm-center gap-4">
                                            @if (Auth::user()->photo)
                                                <img src="{{ asset('storage/images/' . Auth::user()->photo) }}"
                                                    alt="user-avatar" class="d-block rounded" height="100" width="100"
                                                    id="user-avatar-form" />
                                            @else
                                                <img src="{{ asset('storage/images/no-photo.jpg') }}"
                                                    alt="default user-avatar" class="d-block rounded" height="100"
                                                    width="100" />
                                            @endif
                                            <div class="button-wrapper">
                                                <label for="photo" class="btn btn-primary me-2 mb-4" tabindex="0">
                                                    <span class="d-none d-sm-block">Upload new photo</span>
                                                    <i class="bx bx-upload d-block d-sm-none"></i>
                                                    <input type="file" id="photo" name="photo"
                                                        class="account-file-input" hidden
                                                        accept="image/png, image/jpeg, image/jpg, image/svg, image.gif" />
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label for="name" class="form-label">user name</label>
                                        <input class="form-control" type="text" id="name" name="name"
                                            value="{{ Auth::user()->name }}" autofocus autocomplete="off" />
                                        <span class="help-block name-error"></span>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label for="email" class="form-label">E-mail</label>
                                        <input class="form-control" type="text" id="email" name="email"
                                            value="{{ Auth::user()->email }}" placeholder="email@example.com"
                                            autocomplete="off" />
                                        <span class="help-block email-error"></span>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label for="organization" class="form-label">Organization</label>
                                        <input type="text" class="form-control" id="organization" name="organization"
                                            value="{{ Auth::user()->organization }}" placeholder="Enter your organization"
                                            autocomplete="off" />
                                        <span class="help-block organization-error"></span>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label" for="phone">Phone Number</label>
                                        <div class="input-group input-group-merge">
                                            <input type="text" id="phone" name="phone" class="form-control"
                                                value="{{ Auth::user()->phone }}" placeholder="000-00-00"
                                                autocomplete="off" />
                                        </div>
                                        <span class="help-block phone-error"></span>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label" for="password">Current password</label>
                                        <div class="input-group input-group-merge">
                                            <input type="text" id="password" name="password" class="form-control"
                                                placeholder="Current password *" autocomplete="off" />
                                        </div>
                                        <span class="help-block password-error"></span>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label" for="newpassword">New Password</label>
                                        <div class="input-group input-group-merge">
                                            <input type="text" id="newpassword" name="newpassword"
                                                class="form-control" placeholder="New password" autocomplete="off" />
                                        </div>
                                        <span class="help-block newpassword-error"></span>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <button type="submit" class="btn btn-primary me-2">Save changes</button>
                                    <button type="reset" id="btn-outline-secondary"
                                        class="btn btn-outline-secondary">Cancel</button>
                                </div>
                            </form>
                        </div>
                        <!-- /Account -->
                    </div>
                </div>
            </div>
        </div>
        <script>
            // Profile update AJAX call----------------
            $('#edit_form').submit(function(event) {
                event.preventDefault();
                const fd = new FormData(this);
                $(".help-block").html('');
                $.ajax({
                    url: "{{ route('profile.update') }}",
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
                                'Profile Updated Successfully!',
                                'success'
                            )
                            if (response.photo) {
                                let randomParam = Math.random(); 
                                let photoUrl = "{{ asset('storage/images/') }}" + "/" + response
                                    .photo + "?" + randomParam;
                                $('#user-avatar-main').attr('src',
                                    photoUrl); 
                                $('#user-avatar-main2').attr('src',
                                    photoUrl); 
                                $('#user-avatar-form').attr('src',
                                    photoUrl);
                            }

                        }
                        $('#password').val('');
                        $('#newpassword').val('');
                    },
                    error: function(xhr) {
                        if (xhr.status == 422) {
                            let errors = xhr.responseJSON;
                            $.each(xhr.responseJSON, function(key, value) {
                                $('.' + key + '-error').text(value[0]);
                            });
                        }
                        $("#btn-outline-secondary").on('click', function() {
                            $("#edit_form")[0].reset();
                            $(".help-block").html('');
                        });
                    }
                });
            });
        </script>
        <!-- / Content -->
    @endsection
