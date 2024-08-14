<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit Profile</title>

    @include('partials.links')
</head>

<body>

    <!-- ======= Header ======= -->
    @include('partials.header')
    <!-- End Header -->

    <!-- ======= Sidebar ======= -->
    @include('partials.sidebar')
    <!-- End Sidebar -->

    <main id="main" class="main">
        <div class="pagetitle">
            <!-- Optional Page Title -->
        </div><!-- End Page Title -->
        <section class="section">
            <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
            <div class="container">
                <div class="row flex-lg-nowrap">
                    <div class="col-12">
                        <div class="row">
                            <div class="col mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="e-profile">
                                            <form id="profile-form" action="{{ route('user.update-profile') }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="row">
                                                    <!-- Profile Picture Section -->
                                                    <div class="col-12 col-sm-auto mb-3">
                                                        <div class="mx-auto" style="width: 140px; margin-top:20px; margin-bottom:10px;">
                                                            <div class="d-flex justify-content-center align-items-center rounded mb-3" style="height: 140px; background-color: rgb(233, 236, 239);">
                                                                <img id="profile-picture-preview" src="{{ $userDetails->profile_picture ? asset('storage/' . $userDetails->profile_picture) : '' }}" alt="Profile Picture" style="width: 140px; height: 140px; object-fit: cover; display: {{ $userDetails->profile_picture ? 'block' : 'none' }};">
                                                                <span id="no-profile-text" style="color: rgb(166, 168, 170); font: bold 8pt Arial; display: {{ $userDetails->profile_picture ? 'none' : 'block' }};">No Profile</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Profile Information Section -->
                                                    <div class="col d-flex flex-column flex-sm-row justify-content-between mb-3">
                                                        <div class="text-center text-sm-left mb-2 mb-sm-0">
                                                            <h4 class="pt-sm-2 pb-1 mb-0 text-nowrap" style="margin-top:20px;">{{ $userDetails->first_name }} {{ $userDetails->last_name }}</h4>
                                                            <p class="mb-0">{{ $userDetails->username }}</p>
                                                            <div class="text-center mt-3">
                                                                <label for="profile-picture-input" class="btn btn-primary">
                                                                    <i class="fa fa-fw fa-camera"></i>
                                                                    <span>Change Photo</span>
                                                                </label>
                                                                <input id="profile-picture-input" type="file" name="profile_picture" accept="image/*" style="display: none;">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <ul class="nav nav-tabs">
                                                    <li class="nav-item"><a href="#" class="active nav-link">Settings</a></li>
                                                </ul>
                                                @if (session('success'))
                                                    <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
                                                        {{ session('success') }}
                                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                                    </div>
                                                @endif

                                                @if ($errors->any())
                                                    <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
                                                        <ul class="mb-0">
                                                            @foreach ($errors->all() as $error)
                                                                <li>{{ $error }}</li>
                                                            @endforeach
                                                        </ul>
                                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                                    </div>
                                                @endif
                                                <div class="tab-content pt-3">
                                                    <div class="tab-pane active">
                                                        <!-- Form Fields -->
                                                        <div class="row">
                                                            <!-- First Row -->
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="cover_photo">Cover Photo</label>
                                                                    <input id="cover_photo" class="form-control mb-3" type="file" name="cover_photo">
                                                            
                                                                    <!-- Show the existing cover photo if it exists -->
                                                                    @if ($userDetails->cover_photo)
                                                                        <div class="mt-3">
                                                                            <img src="{{ asset('storage/' . $userDetails->cover_photo) }}" alt="Cover Photo" style="max-width: 20%; height: auto;">
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            
                                                           
                                                        <div class="row">
                                                            <!-- First Row -->
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="username">Username</label>
                                                                    <input id="username" class="form-control mb-3" type="text" name="username" value="{{ $userDetails->username }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="email">Email</label>
                                                                    <input id="email" class="form-control mb-3" type="email" name="email" value="{{ $userDetails->email }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <!-- Second Row -->
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="first_name">First Name</label>
                                                                    <input id="first_name" class="form-control mb-3" type="text" name="first_name" value="{{ $userDetails->first_name }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="middle_name">Middle Name</label>
                                                                    <input id="middle_name" class="form-control mb-3" type="text" name="middle_name" value="{{ $userDetails->middle_name }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="last_name">Last Name</label>
                                                                    <input id="last_name" class="form-control mb-3" type="text" name="last_name" value="{{ $userDetails->last_name }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="birthdate">Birthdate</label>
                                                                    <input id="birthdate" class="form-control mb-3" type="date" name="birthdate" value="{{ $userDetails->birthdate->format('Y-m-d') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <!-- Third Row -->
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="current_password">Current Password</label>
                                                                    <input id="current_password" class="form-control" type="password" name="current_password">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="new_password">New Password</label>
                                                                    <input id="new_password" class="form-control" type="password" name="new_password">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="confirm_password">Confirm Password</label>
                                                                    <input id="confirm_password" class="form-control" type="password" name="confirm_password">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col d-flex justify-content-center mt-3">
                                                                <button class="btn btn-primary" type="submit">Save Changes</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main><!-- End #main -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
    @include('partials.footer')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../assets-user/js/edit-profile.js"></script>
</body>

</html>
