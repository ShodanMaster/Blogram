@extends('app.layout')
@section('main')
<div class="container mt-5">
    <div class="card bg-dark">
        <div class="card-header text-white text-center bg-secondary fs-4">
            <span id="form-title">Login</span>
        </div>

        <!-- Login Form -->
        <form action="{{route('loggingin')}}" method="POST" id="login-form">
            @csrf
            <div class="card-body">
                <div class="form-group mb-3">
                    <input type="text" class="form-control" name="email" id="email" placeholder="Email" value="{{old('email')}}" required>
                </div>
                <div class="form-group mb-3">
                    <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                    <!-- Toggle show password -->
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="show-password" />
                        <label class="form-check-label text-white" for="show-password">Show Password</label>
                    </div>
                </div>

                <!-- Link to Signup form -->
                <div class="form-group text-center text-white mb-3">
                    <p>Don't have an account? <a href="javascript:void(0);" id="show-signup-form">Sign up</a></p>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-between">
              <a href="">Login with Google</a>  <button class="btn btn-primary">Login</button>
            </div>
        </form>

        <!-- Signup Form -->
        <form id="signup-form" style="display: none;">
            <div class="card-body">
                <div class="form-group mb-3">
                    <input type="text" class="form-control" name="name" id="name" placeholder="Full Name" required>
                </div>
                <div class="form-group mb-3">
                    <input type="email" class="form-control" name="email" id="signup-email" placeholder="Email" required>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <input type="password" class="form-control" name="password" id="signup-password" placeholder="Password" required>
                            <!-- Toggle show password -->
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="show-signup-password" />
                                <label class="form-check-label text-white" for="show-signup-password">Show Password</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <input type="password" class="form-control" name="password_confirmation" id="signup-password-confirmation" placeholder="Confirmation Password" required>
                        </div>
                    </div>
                </div>

                <!-- Link to Login form -->
                <div class="form-group text-center mb-3 text-white">
                    <p>Already have an account? <a href="javascript:void(0);" id="show-login-form">Login</a></p>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
                <button class="btn btn-primary">Signup</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#show-signup-form').click(function() {
                $('#login-form').hide();
                $('#signup-form').show();
                $('#form-title').text('Signup');
            });

            // Show login form when the "Login" link in the signup form is clicked
            $('#show-login-form').click(function() {
                $('#signup-form').hide();
                $('#login-form').show();
                $('#form-title').text('Login');
            });

            // Toggle password visibility for login form
            $('#show-password').change(function() {
                var passwordField = $('#password');
                if ($(this).prop('checked')) {
                    passwordField.attr('type', 'text');
                } else {
                    passwordField.attr('type', 'password');
                }
            });

            // Toggle password visibility for signup form (password)
            $('#show-signup-password').change(function() {
                var signupPasswordField = $('#signup-password');
                if ($(this).prop('checked')) {
                    signupPasswordField.attr('type', 'text');
                } else {
                    signupPasswordField.attr('type', 'password');
                }
            });

            $(document).on('submit', '#signup-form', function(e) {
                e.preventDefault();

                var formData = new FormData(this);

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: "POST",
                    url: "{{route('registeruser')}}",
                    data: formData,
                    dataType: "json",
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        console.log(response);

                        if (response.status == 200) {
                            // Flash success message using SweetAlert2
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                confirmButtonText: 'OK'
                            });

                            setTimeout(function() {
                                window.location.href = response.url;
                            }, 2000);
                        } else {
                            // Flash error message using SweetAlert2
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message,
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        // Flash error if request fails and show the specific error message

                        // If validation errors are returned from Laravel
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            let errorMessage = 'Validation errors occurred:';

                            // Loop through the validation errors and show them in a single message
                            $.each(xhr.responseJSON.errors, function(field, messages) {
                                errorMessage += `\n${messages.join(', ')}`;
                            });

                            // Show the validation errors in SweetAlert2
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Errors!',
                                text: errorMessage,
                                confirmButtonText: 'OK'
                            });
                        } else {
                            // If some other error occurs (e.g., server error)
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Something went wrong. Please try again.',
                                confirmButtonText: 'OK'
                            });
                        }
                    }
                });

            });

        });
    </script>
@endsection
