<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postify Sign Up</title>
    <link rel="stylesheet" href="{{ asset('assets/css/sign-up.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />
    <script src="{{ asset('assets/js/login.js') }}"></script>
    <link rel="icon" href="{{ asset('assets/images/logo.png') }}" type="image/x-icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
</head>

<body>
    <div class="random-images-container" id="randomImagesContainer"></div>
    <div class="container">
        <form method="POST" action="{{ route('store-user-details.post') }}">
            @csrf <!-- Add this token to protect against CSRF attacks -->
            <h2>Create an Account</h2>
            <div class="form-row">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter your Username">
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email address">
                </div>
                <div class="form-group">
                    <label for="first-name">First Name</label>
                    <input type="text" id="first-name" name="first-name" placeholder="Enter your first name">
                </div>
                <div class="form-group">
                    <label for="middle-name">Middle Name</label>
                    <input type="text" id="middle-name" name="middle-name" placeholder="Enter your middle name">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="last-name">Last Name</label>
                    <input type="text" id="last-name" name="last-name" placeholder="Enter your last name">
                </div>
                <div class="form-group">
                    <label for="birthdate">Birth Date</label>
                    <input type="date" id="birthdate" name="birthdate">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" placeholder="Enter your password">
                        <i id="pass-toggle-btn" class="fa-solid fa-eye"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label for="confirm-password">Confirm Password</label>
                    <input type="password" id="confirm-password" name="password_confirmation" placeholder="Confirm your password">
                </div>
            </div>
            <div class="submit-btn">
                <input type="submit" value="Submit">
            </div>
            <p class="sign-in-text">Already have an account? <a href="{{ route('login') }}">Sign In</a></p>
        </form>
        
    </div>
    
    <script src="{{ asset('assets/js/sign-up.js') }}"></script>
</body>

</html>