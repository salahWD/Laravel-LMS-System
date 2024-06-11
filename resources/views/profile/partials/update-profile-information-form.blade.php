<div class="items-center mt-8 sm:mt-14 text-[#202142]">

  <form method="POST" action="{{ route('profile.update') }}" class="mt-6 space-y-6" id="updateProfileForm" enctype="multipart/form-data">
    @csrf
    @method('patch')

    @if ($errors->any())
      <div class="alert alert-danger">
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="flex flex-col items-center w-full space-x-0 space-y-2 sm:flex-row sm:space-x-4 sm:space-y-0">
      <div class="w-full">
        <x-input-label for="first_name" :value="__('First Name')" />
        <x-text-input placeholder="first name" id="first_name" name="first_name" type="text" class="mt-1 block w-full" :value="old('first_name', $user->first_name)"
            required autofocus autocomplete="name" />
        <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
      </div>

      <div class="w-full">
        <x-input-label for="last_name" :value="__('Last Name')" />
        <x-text-input placeholder="sir name" id="last_name" name="last_name" type="text" class="mt-1 block w-full" :value="old('last_name', $user->last_name)"
            required autocomplete="name" />
        <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
      </div>
    </div>
    <p class="text-sm text-gray-600 mt-2 mb-3 sm:mb-6">{{ __('your full name (first name + last name) will be on your certificates') }}</p>

    <div class="mb-3 sm:mb-6">
      <x-input-label for="email" :value="__('Email')" />
      <x-text-input placeholder="your email" id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)"
          required autocomplete="username" />
      <x-input-error class="mt-2" :messages="$errors->get('email')" />

      @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
        <div>
          <p class="text-sm mt-2 text-gray-800 ">
            {{ __('Your email address is unverified.') }}

            <button form="send-verification"
                class="underline text-sm text-gray-600  hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 ">
                {{ __('Click here to re-send the verification email.') }}
            </button>
          </p>

          @if (session('status') === 'verification-link-sent')
            <p class="mt-2 font-medium text-sm text-green-600 ">
              {{ __('A new verification link has been sent to your email address.') }}
            </p>
          @endif
        </div>
      @endif
    </div>

    <div class="mb-3 sm:mb-6">
      <x-input-label for="bio" :value="__('Bio')" />
      <textarea id="bio" name="bio" rows="4"
        class="mt-1 !border-gray  -300 focus:!border-blue-500 focus:!ring-blue-500 rounded-md shadow-sm block p-2.5 w-full text-sm border"
        placeholder="Write your bio here...">{{ old('bio', $user->bio) }}</textarea>
      <x-input-error class="mt-2" :messages="$errors->get('bio')" />
    </div>

    <div class="flex justify-end">
      <div class="flex items-center gap-4">
        @if (session('status') === 'profile-updated')
          <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
            class="text-sm text-gray-600 ">{{ __('Saved.') }}</p>
        @endif

        <x-primary-button class="rounded-lg text-sm w-full sm:w-auto px-5 !py-2.5 mt-3 text-center">{{ __('Save') }}</x-primary-button>
      </div>
    </div>
  </form>

  <form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
  </form>

</div>
