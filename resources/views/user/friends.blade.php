<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Friends</title>

    @include('partials.links')
    <link href="../assets-user/css/friends.css" rel="stylesheet">
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
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="people-nearby">
                            @forelse ($mutualFollowers as $user)
                                <div class="card nearby-user mb-3">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col-md-2 image-profile">
                                            <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('assets-user/img/none-profile.jpg') }}"
                                            alt="Profile Picture" class="profile-photo-lg">
                                        </div>
                                        <div class="col-md-7">
                                            <div class="card-body">
                                                <h6 class="card-title mb-1">
                                                    <a href="#" class="profile-link">{{ $user->first_name }} {{ $user->last_name }}</a>
                                                </h6>
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-right">
                                            @if ($userDetails->isFollowing($user->user_id))
                                                <button class="btn btn-primary btn-sm" data-user-id="{{ $user->user_id }}">Following</button>
                                            @else
                                                <button class="btn btn-secondary btn-sm follow-button" data-user-id="{{ $user->user_id }}">Follow</button>
                                            @endif
                                            @if ($user->isFollowedBy($userId))
                                                <button class="btn btn-secondary btn-sm unfollow-button" data-user-id="{{ $user->user_id }}">Unfollow</button>
                                            @endif
                                            @if (isset($mutualFollows[$user->user_id]) && $mutualFollows[$user->user_id])
                                                <span class="badge badge-success">Mutual</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h5 class="card-title m-2">No friends</h5>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        
    </main><!-- End #main -->


    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>
            <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    @include('partials.footer')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../assets-user/js/friends.js"></script>
    


</body>

</html>
