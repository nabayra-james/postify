<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Post</title>
    <link href="../assets-user/css/post.css" rel="stylesheet">
    <link href="../assets-user/css/comment.css" rel="stylesheet">
    @include('partials.links')
</head>


<body>

    <!-- ======= Header ======= -->
    @include('partials.header')
    <!-- End Header -->

    <!-- ======= Sidebar ======= -->
    @include('partials.sidebar')
    <!-- End Sidebar-->

    <main id="main" class="main">

        <div class="pagetitle">


        </div><!-- End Page Title -->

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <button type="button" class="btn w-100" data-bs-toggle="modal"
                            data-bs-target="#onYourMindModal"
                            style="background-color: rgb(224, 224, 224); height: 50px;">
                            What's on your mind?
                        </button>
                        <div class="modal fade" id="onYourMindModal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header text-center">
                                        <h5 class="modal-title w-100" style="font-weight: bold;">Create a new post</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Text Field -->
                                        <form action="{{ route('posts.store') }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <!-- Privacy Settings -->
                                            <div class="mb-3">
                                                <label for="privacy" class="form-label">Who can see your post?</label>
                                                <select class="form-select" id="privacy" name="privacy">
                                                    <option value="Public" selected>Public</option>
                                                    <option value="Friends">Friends</option>
                                                    <option value="Only Me">Only Me</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <textarea class="form-control" id="postText" name="caption" rows="4" placeholder="What's on your mind?"
                                                    style="background-color: rgb(243, 243, 243);"></textarea>
                                            </div>
                                            <!-- Upload Photo Button -->
                                            <div class="mb-3">
                                                <button type="button" class="btn upload-button w-100"
                                                    id="uploadPhotoBtn">
                                                    Upload Photo
                                                </button>
                                                <input type="file" id="fileInput" name="image" accept="image/*"
                                                    style="display: none;">
                                            </div>
                                            <!-- Buttons -->
                                            <div class="d-grid gap-2">
                                                <button type="submit" class="btn btn-primary">Post</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div><!-- End Basic Modal-->
                    </div>
                </div>
            </div>

            <div class="pagetitle">
                <h1 class="mb-3">Posts</h1>

                <div class="col-lg-12">
                    @if ($posts->isEmpty())
                        {{-- <div class="alert alert-info">No posts to display.</div> --}}
                    @else
                        @foreach ($posts as $post)
                            <div class="card">
                                <div class="card-body">
                                    <!-- Post Header -->
                                    <div class="d-flex align-items-center">
                                        <!-- Profile Picture -->
                                        <img src="{{ $post->user->profile_picture ? asset('storage/' . $post->user->profile_picture) : asset('assets-user/img/none-profile.jpg') }}"
                                            alt="Profile Picture" class="rounded-circle-profile"
                                            style="width: 50px; height: 50px; object-fit: cover;">
                                        <!-- Post Information -->
                                        <div class="ms-3 flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <strong class="d-block mt-2">{{ $post->user->first_name }}
                                                            {{ $post->user->last_name }}</strong>
                                                        @if (Auth::check() && Auth::id() !== $post->user_id)
                                                            @php
                                                                $isFollowing = in_array($post->user_id, $followedUsers);
                                                            @endphp
                                                            <button class="btn follow-button mt-2"
                                                                data-user-id="{{ $post->user_id }}">
                                                                {{ $isFollowing ? 'Following' : 'Follow' }}
                                                            </button>
                                                        @endif
                                                    </div>
                                                    <div class="text-muted" style="font-size:13px;">
                                                        @if ($post->privacy === 'Public')
                                                            <i class="fas fa-globe" style="color: #c0bebe;"></i> Public
                                                        @elseif ($post->privacy === 'Friends')
                                                            <i class="fas fa-user-friends" style="color: #c0bebe"></i>
                                                            Friends
                                                        @elseif ($post->privacy === 'Only Me')
                                                            <i class="fas fa-lock" style="color: #c0bebe"></i> Only Me
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- Ellipsis Button -->
                                                @if (auth()->check() && auth()->id() === $post->user_id)
                                                    <div class="dropdown">
                                                        <button class="btn btn-link" type="button"
                                                            id="dropdownMenuButton-{{ $post->id }}"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="fa-solid fa-ellipsis"></i>
                                                        </button>
                                                        <ul class="dropdown-menu"
                                                            aria-labelledby="dropdownMenuButton-{{ $post->id }}">
                                                            <li>
                                                                <a class="dropdown-item edit-post-btn"
                                                                    data-post-id="{{ $post->user_post_id }}"
                                                                    data-caption="{{ $post->caption }}"
                                                                    data-image-url="{{ asset('storage/' . $post->image) }}"
                                                                    data-privacy="{{ $post->privacy }}"
                                                                    data-first-name="{{ $post->user->first_name ?? 'N/A' }}"
                                                                    data-last-name="{{ $post->user->last_name ?? 'N/A' }}">
                                                                    <i class="fas fa-edit"></i> Edit
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item deletePostBtn" href="#"
                                                                    data-id="{{ $post->user_post_id }}">
                                                                    <i class="fas fa-trash"></i> Delete
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Post Content -->
                                    <div class="mb-3">
                                        <!-- Caption -->
                                        <p style="margin-top:20px;">{{ $post->caption }}</p>
                                        <!-- Photo -->
                                        @if ($post->image)
                                            <div class="card" style="width: 100%;">
                                                <img src="{{ asset('storage/' . $post->image) }}" alt="Post Photo"
                                                    class="img-fluid"
                                                    style="max-height: 400px; object-fit: contain; width: 100%; height: auto;">
                                            </div>
                                        @endif

                                        <!-- Icons and Counts -->
                                        <div class="d-flex align-items-center">
                                            <!-- Likes Button and Count -->
                                            <!-- Like Button and Count -->
                                            <button class="btn btn-link like-button no-underline" type="button"
                                                data-post-id="{{ $post->user_post_id }}"
                                                data-url="{{ route('like.toggle') }}">
                                                <i class="fa-heart {{ $post->likes->contains('user_id', Auth::id()) ? 'fas' : 'far' }}"
                                                    style="{{ $post->likes->contains('user_id', Auth::id()) ? 'color: red;' : 'color: black;' }}"></i>
                                                <span class="like-count">{{ $post->likes->count() }}</span>
                                            </button>

                                            <!-- Comments Button and Count -->
                                            <button class="btn btn-link no-underline" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#comments-{{ $post->user_post_id }}">
                                                <i class="fa-regular fa-comment" style="color: #333;"></i>
                                                <span>{{ $post->comments->count() }}</span>
                                            </button>

                                        </div>

                                        <div class="collapse mt-3" id="comments-{{ $post->user_post_id }}">
                                            <div class="comments-container">
                                                <h6>Comments</h6>
                                                <hr>
                                                <!-- Loop through comments -->
                                                @foreach ($post->comments as $comment)
                                                    <div class="comment d-flex mb-2 p-2 border-bottom">
                                                        <img src="{{ $comment->user->profile_picture ? asset('storage/' . $comment->user->profile_picture) : asset('assets-user/img/none-profile.jpg') }}"
                                                            alt="Profile Picture" class="rounded-circle small-img">
                                                        <div class="ms-2">
                                                            <strong>{{ $comment->user->first_name }}
                                                                {{ $comment->user->last_name }}</strong>
                                                            <p class="mb-1">{{ $comment->content }}</p>
                                                            <button class="btn btn-link btn-sm">Reply</button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>


                                            <!-- Add Comment Form -->
                                            <div class="mt-3">
                                                <form method="POST" action="{{ route('comments.store') }}"
                                                    class="d-flex mt-3 comment-form"
                                                    data-comment-url="{{ route('comments.store') }}">
                                                    @csrf
                                                    <input type="hidden" name="post_id"
                                                        value="{{ $post->user_post_id }}">
                                                    <input type="text" class="form-control me-2" name="content"
                                                        placeholder="Add a comment..." required>
                                                    <button class="btn btn-primary" type="submit">Post</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Edit Post Modal -->
            <div class="modal fade" id="editPostModal" tabindex="-1" aria-labelledby="editPostModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form id="editPostForm" action="" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Modal Header -->
                            <div class="modal-header">
                                <h5 class="modal-title" id="editPostModalLabel">Edit Post</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>

                            <!-- Modal Body -->
                            <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
                                <!-- Profile Picture, Name, and Privacy Setting -->
                                <div class="d-flex align-items-center mb-3">
                                    <img src="{{ auth()->user()->profile_picture ? asset('storage/' . auth()->user()->profile_picture) : asset('assets-user/img/none-profile.jpg') }}"
                                        alt="Profile Picture" class="rounded-circle-profile"
                                        style="width: 50px; height: 50px; object-fit: cover;">

                                    <div class="ms-3">
                                        <div class="fw-bold mt-3" id="userName"></div>
                                        <div class="position-relative">
                                            <span
                                                id="privacyDisplay">{{ isset($post) ? $post->privacy : 'Public' }}</span>
                                            <i class="fa fa-caret-down" id="privacyIcon"
                                                style="cursor: pointer; padding: 5px; display: inline-block;"></i>
                                            <div class="dropdown-menu" id="privacyDropdown"
                                                style="display: none; position: absolute; top: 100%; left: 0; min-width: 300px;">
                                                <div class="p-2 fw-bold" style="margin-left: 13px;">Who can see
                                                    your post?</div>
                                                <div
                                                    class="form-check d-flex justify-content-between align-items-center">
                                                    <label class="form-check-label me-2" for="public"><i
                                                            class="fas fa-globe"></i> Public</label>
                                                    <input class="form-check-input" style="margin-right: 20px;"
                                                        type="radio" name="privacy" id="public" value="Public">
                                                </div>
                                                <div
                                                    class="form-check d-flex justify-content-between align-items-center">
                                                    <label class="form-check-label me-2" for="friends"><i
                                                            class="fas fa-user-friends"></i> Friends</label>
                                                    <input class="form-check-input" style="margin-right: 20px;"
                                                        type="radio" name="privacy" id="friends"
                                                        value="Friends">
                                                </div>
                                                <div
                                                    class="form-check d-flex justify-content-between align-items-center">
                                                    <label class="form-check-label me-2" for="onlyMe"><i
                                                            class="fas fa-lock"></i> Only Me</label>
                                                    <input class="form-check-input" style="margin-right: 20px;"
                                                        type="radio" name="privacy" id="onlyMe"
                                                        value="Only Me">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Caption Textarea -->
                                <div class="mb-3">
                                    <textarea class="form-control caption" id="caption" name="caption" required placeholder="What's on your mind?"
                                        style="border:none; font-size: 16px; padding: 10px; background-color: rgb(255, 255, 255);">{{ old('caption', isset($post) ? $post->caption : '') }}</textarea>
                                </div>

                                <!-- Upload Photo Button -->
                                <div class="mb-3 position-relative">
                                    <input type="file" id="fileInput" name="image" accept="image/*"
                                        style="display: none;">
                                    <button type="button" class="btn btn-light position-absolute start-0"
                                        id="uploadPhotoButton" style="margin-top: 5px; margin-left:5px;">
                                        <i class="fas fa-plus-circle"></i> Add photos
                                    </button>
                                </div>

                                <!-- Image Preview -->
                                <img id="postImagePreview"
                                    src="{{ isset($post) && $post->image ? asset('storage/' . $post->image) : '' }}"
                                    alt="Post Image" class="img-fluid mt-2"
                                    style="max-height: 300px; object-fit: cover; {{ isset($post) && $post->image ? '' : 'display: none;' }}">
                            </div>

                            <!-- Modal Footer -->
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary w-100">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


        </section>
    </main><!-- End #main -->


    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    @include('partials.footer')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../assets-user/js/user-post.js"></script>

</body>

</html>
