/**
 * upload button in add post
 */
document.getElementById('uploadPhotoBtn').addEventListener('click', function() {
    document.getElementById('fileInput').click();
});

document.getElementById('fileInput').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
        console.log('Selected file:', file.name);
    }
});

/**
 * edit post
 */
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

    document.getElementById('uploadPhotoButton').addEventListener('click', function(event) {
        event.preventDefault();
        document.getElementById('fileInput').click();
    });
    
    document.getElementById('fileInput').addEventListener('change', function(event) {
        console.log('File input changed');
        const file = event.target.files[0];
        if (file) {
            console.log('File selected: ', file.name, file.size, file.type);
            const reader = new FileReader();
            reader.onload = function(e) {
                const imagePreview = document.getElementById('postImagePreview');
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            console.log('No file selected');
        }
    });

    document.getElementById('editPostForm').addEventListener('submit', function(event) {
        event.preventDefault();
        console.log('Form submission started');

        const formData = new FormData(this);
        
        const fileInput = document.getElementById('fileInput');
        if (fileInput.files.length > 0) {
            console.log('Adding file to FormData:', fileInput.files[0].name);
            formData.append('image', fileInput.files[0]);
        } else {
            console.log('No file selected for upload');
        }

        const postId = this.action.split('/').pop();

        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + (pair[1] instanceof File ? pair[1].name : pair[1]));
        }

        fetch(`/posts/${postId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Server response:', data);
            if (data.success) {
                console.log('Post updated successfully');
                bootstrap.Modal.getInstance(document.getElementById('editPostModal')).hide();
                window.location.reload();
            } else {
                console.error('Error updating post:', data.error);
                alert('Error updating post: ' + JSON.stringify(data.error));
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            alert('Error: ' + error);
        });
    });


/**
* delete post
*/
    document.body.addEventListener('click', function(e) {
        if (e.target && e.target.matches('.deletePostBtn')) {
            e.preventDefault(); 

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

/**
 * privacy
 */
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

    // Optional: Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!privacyIcon.contains(e.target) && !privacyDropdown.contains(e.target)) {
            privacyDropdown.style.display = 'none';
        }
    });
});


/**
 * following
 */
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

/**
 * comments
 */
$(document).ready(function() {
    $('.comment-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var postId = form.find('input[name="post_id"]').val();
        var content = form.find('input[name="content"]').val();
        var url = form.data('comment-url');

        $.ajax({
            url: url,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                post_id: postId,
                content: content
            },
            success: function(response) {
                console.log('Response:', response);
                if (response.success && response.comment) {
                    var comment = response.comment;
                    var commentHtml = `
                    <div class="comment d-flex mb-2 p-2 border-bottom">
                        <img src="../assets-user/img/yena.jpg" alt="User" class="rounded-circle small-img">
                        <div class="ms-2">
                            <strong>${comment.user.first_name || comment.user.firstname} ${comment.user.last_name || comment.user.lastname}</strong>
                            <p class="mb-1">${comment.content}</p>
                            <button class="btn btn-link btn-sm">Reply</button>
                        </div>
                    </div>
                `;
                    $(`#comments-${postId} .comments-container`).append(commentHtml);
                    form.find('input[name="content"]').val('');
                    
                    var commentCountEl = form.closest('.collapse').prev().find('button:last-child span');
                    var currentCount = parseInt(commentCountEl.text());
                    commentCountEl.text(currentCount + 1);
                } else {
                    console.error('Unexpected response structure:', response);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                console.log('Status:', status);
                console.log('Response:', xhr.responseText);
            }
        });
    });
});

/**
 * likes
 */
$(document).ready(function() {
    $('.like-button').on('click', function() {
        var button = $(this);
        var postId = button.data('post-id');
        var icon = button.find('i');
        var countSpan = button.find('.like-count');
        var url = button.data('url');

        $.ajax({
            url: url,
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                post_id: postId
            },
            success: function(response) {
                if (response.success) {
                    if (response.liked) {
                        icon.removeClass('far').addClass('fas').css('color', 'red');
                    } else {
                        icon.removeClass('fas').addClass('far').css('color', 'black');
                    }
                    countSpan.text(response.likeCount);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    });
});
