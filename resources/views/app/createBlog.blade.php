<!-- Add this to your master.blade.php file -->

<!-- Include TinyMCE CDN in the <head> -->
    <script src="https://cdn.tiny.cloud/1/ebrtr3zs5d1eo1obh5mklrxp71gt7hjg7gvqc2s0hb6q9rdf/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>


    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary position-fixed top-3 end-0 m-3" style="z-index: 1;" data-bs-toggle="modal" data-bs-target="#createBlogModal">
        Create Blog
    </button>

    <div class="modal fade" id="createBlogModal" tabindex="-1" aria-labelledby="createBlogModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-secondary">
                    <h1 class="modal-title fs-5" id="createBlogModalLabel">Create Blog</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createBlog" enctype="multipart/form-data">
                    <div class="modal-body bg-dark">
                        <div class="form-group">
                            <label for="blog_title" class="form-label">Blog Title: </label>
                            <input type="text" class="form-control" name="title" id="blog_title" required>
                        </div>
                        <div class="form-group">
                            <label for="blog_content" class="form-label">Blog Content: </label>
                            <!-- Textarea for TinyMCE editor -->
                            <textarea class="form-control" name="content" id="blog_content" cols="30" rows="10"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-dark">
                        <button type="submit" class="btn btn-secondary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Initialize TinyMCE for the blog content textarea -->
    <script>
        tinymce.init({
            selector: '#blog_content',
            menubar: false,  // Optional: disable the menu bar
            plugins: 'link image lists',  // Optional: you can add more plugins if needed
            toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | bullist numlist | link image', // Optional: customize the toolbar
        });
    </script>

    <!-- Ensure jQuery is loaded for the form submission handling -->
    <script src="{{asset('js/jquery-3.6.0.min.js')}}"></script>

    <script>
        $(document).ready(function () {
            $(document).on('submit', '#createBlog', function (e) {
                e.preventDefault();
                console.log('Form submitted');

                // Get content from TinyMCE editor
                var content = tinymce.get('blog_content').getContent();

                // Check if content is empty
                if (content.trim() === '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Content cannot be empty!',
                        confirmButtonText: 'OK'
                    });
                    return false; // Prevent form submission
                }

                var formData = new FormData(this);

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: "POST",
                    url: "{{route('blog.storeblog')}}",
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
                                $('#createBlogModal').modal('hide');
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
        });
    </script>

