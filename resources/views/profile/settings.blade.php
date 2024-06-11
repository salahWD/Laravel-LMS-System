<x-profile.layout>

  <div class="w-full px-6 pb-8 mt-8 sm:max-w-xl sm:rounded-lg">
    <h2 class="pl-6 text-2xl font-bold sm:text-xl">Settings</h2>

    <div class="grid max-w-2xl mx-auto mt-8">
      <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>

</x-profile.layout>
