document.getElementById('uploadPhotoBtn').addEventListener('click', function() {
    document.getElementById('fileInput').click();
});

document.getElementById('fileInput').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
        console.log('Selected file:', file.name);
    }
});

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.edit-post-btn').forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.getAttribute('data-post-id');
            const caption = this.getAttribute('data-caption');
            const imageUrl = this.getAttribute('data-image-url');
            const privacy = this.getAttribute('data-privacy');
            const firstName = this.getAttribute('data-first-name');
            const lastName = this.getAttribute('data-last-name');

            const form = document.getElementById('editPostForm');
            form.action = `/posts/${postId}`;

            document.getElementById('caption').value = caption;

            // Update name display
            document.getElementById('userName').textContent = `${firstName} ${lastName}`;

            const privacyRadios = document.querySelectorAll('input[name="privacy"]');
            privacyRadios.forEach(radio => {
                if (radio.value === privacy) {
                    radio.checked = true;
                }
            });

            const privacyDisplay = document.getElementById('privacyDisplay');
            privacyDisplay.textContent = privacy;

            const imagePreview = document.getElementById('postImagePreview');
            if (imageUrl) {
                imagePreview.src = imageUrl;
                imagePreview.style.display = 'block';
            } else {
                imagePreview.src = '';
                imagePreview.style.display = 'none';
            }

            new bootstrap.Modal(document.getElementById('editPostModal')).show();
        });
    });

    // document.getElementById('uploadPhotoButton').addEventListener('click', function() {
    //     document.getElementById('fileInput').click();
    // });
    
    // document.getElementById('fileInput').addEventListener('change', function(event) {
    //     const file = event.target.files[0];
    //     if (file) {
    //         const reader = new FileReader();
    //         reader.onload = function(e) {
    //             const imagePreview = document.getElementById('postImagePreview');
    //             imagePreview.src = e.target.result;
    //             imagePreview.style.display = 'block'; // Show the image preview
    //         };
    //         reader.readAsDataURL(file);
    //     }
    // });
    
    
    document.body.addEventListener('click', function(e) {
        if (e.target && e.target.matches('.deletePostBtn')) {
            e.preventDefault(); // Prevent the default action

            const postId = e.target.getAttribute('data-id');
            const url = `/posts/delete/${postId}`; 

            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    }).then(response => {
                        if (response.ok) {
                            Swal.fire(
                                'Deleted!',
                                'Your post has been deleted.',
                                'success'
                            ).then(() => {
                                location.reload(); 
                            });
                        } else {
                            Swal.fire(
                                'Error!',
                                'There was an issue deleting the post.',
                                'error'
                            );
                        }
                    }).catch(error => {
                        console.error('Error:', error); 
                        Swal.fire(
                            'Error!',
                            'There was an issue deleting the post.',
                            'error'
                        );
                    });
                }
            });
        }
    });

    // Handle privacy dropdown
    var privacyIcon = document.getElementById('privacyIcon');
    var privacyDropdown = document.getElementById('privacyDropdown');

    privacyIcon.addEventListener('click', function() {
        if (privacyDropdown.style.display === 'none' || privacyDropdown.style.display === '') {
            privacyDropdown.style.display = 'block';
        } else {
            privacyDropdown.style.display = 'none';
        }
    });

    document.addEventListener('click', function(e) {
        if (!privacyIcon.contains(e.target) && !privacyDropdown.contains(e.target)) {
            privacyDropdown.style.display = 'none';
        }
    });
});



jQuery(document).ready(function($) {
    $(document).on('click', '.follow-button', function() {
        var button = $(this);
        var userId = button.data('user-id');
        var isFollowing = button.text().trim() === 'Following';
        var action = isFollowing ? 'unfollow' : 'follow';
        var url = '/' + action + '/' + userId;

        $.ajax({
            url: url,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
            },
            success: function(response) {
                if (response.status === 'success') {
                    button.text(isFollowing ? 'Follow' : 'Following')
                          .toggleClass('following', !isFollowing)
                          .prop('disabled', false);
                } else if (response.status === 'unauthenticated') {
                    window.location.href = '/login';
                } else if (response.status === 'not_following') {
                    alert('You are not following this user.');
                }
            },
            error: function(xhr) {
                console.error('AJAX Error:', xhr.responseText);
                alert('An error occurred. Please try again.');
            }
        });
    });
});

