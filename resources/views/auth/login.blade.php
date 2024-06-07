@extends('layout.layout-nonav')

@section('meta')
  @php $page_title = config("app.name") . " | " . __('Dashboard') @endphp
@endsection

@section('styles')
  @vite('resources/css/login.css')
@endsection

@section('content')
  <div class="container" id="container">
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    <div class="form-container sign-up-container">
      <form method="POST" action="{{ route('register') }}">
        @csrf
        <h1>{{ __('Create Account') }}</h1>
        {{-- <div class="social-container">
          <a href="#" class="social"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="social"><i class="fab fa-google-plus-g"></i></a>
          <a href="#" class="social"><i class="fab fa-linkedin-in"></i></a>
        </div> --}}
        <span>{{ __('or use your email for registration') }}</span>
        <input type="text" placeholder="{{ __('username') }}" name="username" required autocomplete="username" tabindex="3"/>
        @error('username')
          @foreach ($errors->get('username') as $err)
            <p class="text-danger">{{ $err }}</p>
          @endforeach
        @enderror
        <input type="email" placeholder="{{ __('Email') }}" name="email" value="{{ old('email') }}" required autocomplete="email" tabindex="4"/>
        @error('email')
          @foreach ($errors->get('email') as $err)
            <p class="text-danger">{{ $err }}</p>
          @endforeach
        @enderror
        <input type="password" placeholder="{{ __('Password') }}" name="password" required tabindex="5"/>
        @error('password')
          @foreach ($errors->get('password') as $err)
            <p class="text-danger">{{ $err }}</p>
          @endforeach
        @enderror
        <button>{{ __('Sign Up') }}</button>
      </form>
    </div>
    <div class="form-container sign-in-container">
      <form method="POST" action="{{ route('login') }}">
        @csrf
        <h1>{{ __('Sign in') }}</h1>
        {{-- <div class="social-container">
          <a href="#" class="social"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="social"><i class="fab fa-google-plus-g"></i></a>
          <a href="#" class="social"><i class="fab fa-linkedin-in"></i></a>
        </div> --}}
        <span>{{ __('or use your account') }}</span>
        <input type="email" placeholder="{{ __('Email') }}" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus tabindex="1"/>
        @error('email')
          @foreach ($errors->get('email') as $err)
            <p class="text-danger">{{ $err }}</p>
          @endforeach
        @enderror
        <input type="password" placeholder="{{ __('Password') }}" name="password" required tabindex="2"/>
        @error('password')
          @foreach ($errors->get('password') as $err)
            <p class="text-danger">{{ $err }}</p>
          @endforeach
        @enderror
        <a href="#" tabindex="-1">{{ __('Forgot your password?') }}</a>
        <button>{{ __('Sign In') }}</button>
      </form>
    </div>
    <div class="overlay-container">
      <div class="overlay">
        <div class="overlay-panel overlay-left">
          <h1>{{ __('Welcome Back!') }}</h1>
          <p>{{ __('To keep connected with us please login with your personal info') }}</p>
          <button class="ghost" id="signIn">{{ __('Sign In') }}</button>
        </div>
        <div class="overlay-panel overlay-right">
          <h1>{{ __('Hello, Friend!') }}</h1>
          <p>{{ __('Enter your personal details and start journey with us') }}</p>
          <button class="ghost" id="signUp">{{ __('Sign Up') }}</button>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')

  <!-- Fontawesome -->
  <script src="https://kit.fontawesome.com/d49577c07f.js" crossorigin="anonymous"></script>

  <script>
    const signUpButton = document.getElementById('signUp');
    const signInButton = document.getElementById('signIn');
    const container = document.getElementById('container');

    signUpButton.addEventListener('click', () => {
      container.classList.add("right-panel-active");
    });

    signInButton.addEventListener('click', () => {
      container.classList.remove("right-panel-active");
    });
  </script>
@endsection
