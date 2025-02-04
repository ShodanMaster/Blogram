@extends('app.master')
@section('content')

{{-- Edit Profile Modal --}}
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-secondary">
          <h1 class="modal-title fs-5" id="editProfileModalLabel">Modal title</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="updateProfile" enctype="multipart/form-data">
            <div class="modal-body bg-dark">
                <div class="form-group">
                    <label class="form-label">Gender: </label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" id="male" value="Male"
                            {{ (old('gender', Auth::user()->profile->gender) == 'Male') ? 'checked' : '' }} required>
                        <label class="form-check-label" for="male">Male</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" id="female" value="Female"
                            {{ (old('gender', Auth::user()->profile->gender) == 'Female') ? 'checked' : '' }} required>
                        <label class="form-check-label" for="female">Female</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" id="other" value="Other"
                            {{ (old('gender', Auth::user()->profile->gender) == 'Other') ? 'checked' : '' }} required>
                        <label class="form-check-label" for="other">Other</label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="bio" class="form-label">Bio: </label>
                    <textarea name="bio" id="bio" cols="30" rows="10" class="form-control">{{Auth::user()->profile->bio}}</textarea>
                </div>

                <div class="form-group">
                    <label for="profile_image" class="form-label">Profile Image: </label>
                    <input type="file" class="form-control" name="profile_image" id="profile_image">
                    <!-- Image preview container -->
                    <div id="image-preview-container" class="mt-3">
                        <img id="image-preview" src="#" alt="Profile Preview" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover; display: none;">
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-dark">
              <button type="submit" class="btn btn-secondary">Save changes</button>
            </div>
        </form>
      </div>
    </div>
</div>

<div class="card bg-dark text-white">
    <div class="card-body">
        <div class="row">
            <!-- Profile Picture -->
            <div class="col-md-3 text-center">
                @if(Auth::user()->profile && Auth::user()->profile->profile_image)
                    <img src="{{ asset('storage/' . Auth::user()->profile->profile_image) }}"
                         alt="{{ Auth::user()->name }}"
                         title="{{ Auth::user()->name }}"
                         class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                @else
                    <img src="{{ asset('defaults/default_profile.jpeg') }}"
                         alt="No profile photo"
                         title="No profile photo"
                         class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                @endif
            </div>

            <!-- Profile Info -->
            <div class="col-md-9">
                <div class="row">
                    <!-- Username -->
                    <div class="col-12">
                        <h4 class="mb-0">{{ Auth::user()->name }}</h4>
                    </div>

                    <!-- Gender Section (Optional) -->
                    <div class="col-12 mt-2">
                        <p class="text-white">{{ Auth::user()->profile->gender ?? 'Add a bio...' }}</p>
                    </div>

                    <!-- Bio Section (Optional) -->
                    <div class="col-12 mt-2">
                        <p class="text-white">{{ Auth::user()->profile->bio ?? 'Add a bio...' }}</p>
                    </div>

                    <!-- Follow/Followers Section (Optional) -->
                    <div class="col-12 mt-2">
                        <span class="text-white">1000 followers | 500 following</span>
                    </div>

                    <!-- Edit Profile Button -->
                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            Edit Profile
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
    <script>
        $(document).ready(function(){

            $(document).on('submit', '#updateProfile', function (e) {
                e.preventDefault();

                // console.log('inside');

                var formData = new FormData(this);

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: "POST",
                    url: "{{route('profile.updateprofile')}}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
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
                                $('#editProfileModal').modal('hide');
                                location.reload();
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

            let initialGender = "{{ old('gender', Auth::user()->profile->gender) }}";
            let initialBio = "{{ Auth::user()->profile->bio }}";
            let initialProfileImage = $("#profile_image")[0].value;

            $('#editProfileModal').on('hidden.bs.modal', function () {
                // If the form fields haven't been changed, reset the form values
                if ($('#updateProfile')[0].checkValidity()) {
                    // Reset gender radio buttons
                    $("input[name='gender']").each(function () {
                        if ($(this).val() === initialGender) {
                            $(this).prop('checked', true);
                        } else {
                            $(this).prop('checked', false);
                        }
                    });

                    // Reset bio field
                    $('#bio').val(initialBio);

                    // Reset the profile image input field (it won't reset automatically)
                    $('#profile_image').val(initialProfileImage);
                }
            });

            let cropper;
            const imageInput = document.getElementById('profile_image');
            const imagePreview = document.getElementById('image-preview');
            const imagePreviewContainer = document.getElementById('image-preview-container');

            // Event listener for image input
            imageInput.addEventListener('change', function (e) {
                const file = e.target.files[0];

                if (file) {
                    const reader = new FileReader();

                    reader.onload = function (event) {
                        // Show the image preview
                        imagePreview.src = event.target.result;
                        imagePreview.style.display = 'block';
                        imagePreviewContainer.style.display = 'block';

                        // If there’s an existing cropper, destroy it
                        if (cropper) {
                            cropper.destroy();
                        }

                        // Create a new cropper instance for the selected image
                        cropper = new Cropper(imagePreview, {
                            aspectRatio: 1, // Keeps it circular (1:1 ratio)
                            viewMode: 2, // Ensures image doesn't overflow the crop box
                            responsive: true,
                            background: true,
                            autoCropArea: 1,
                            ready() {
                                console.log('Cropper is ready');
                            }
                        });
                    };

                    reader.readAsDataURL(file);
                }
            });

            // Get cropped image data when the form is submitted
            document.getElementById('updateProfile').addEventListener('submit', function (e) {
                e.preventDefault();

                if (cropper) {
                    // Get the cropped image
                    const croppedCanvas = cropper.getCroppedCanvas();
                    croppedCanvas.toBlob(function (blob) {
                        // Append the cropped image to the form as a hidden field or upload it directly
                        const formData = new FormData();
                        formData.append('profile_image', blob);

                        // Append other form data as needed
                        const form = new FormData(document.getElementById('updateProfile'));
                        formData.append('bio', form.get('bio'));
                        formData.append('gender', form.get('gender'));

                        // Send the form data via AJAX (you can use fetch or any AJAX method)
                        fetch('/path-to-update-profile', {
                            method: 'POST',
                            body: formData
                        }).then(response => {
                            // Handle response here
                            console.log('Profile updated');
                        }).catch(error => {
                            console.error('Error updating profile', error);
                        });
                    });
                }
            });

        });
    </script>
@endsection
