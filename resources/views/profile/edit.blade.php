<x-profile.layout>

  <div class="w-full px-6 pb-8 mt-8 sm:max-w-xl sm:rounded-lg">
    <h2 class="pl-6 text-2xl font-bold sm:text-xl">Public Profile</h2>

    <div class="grid max-w-2xl mx-auto mt-8">
      <div class="flex flex-col items-center space-y-5 sm:flex-row sm:space-y-0">

        <img class="object-cover w-40 h-40 p-1 rounded-full ring-2 ring-blue-500"
            src="{{ $user->image_url() }}"
            id="previewPicture"
            alt="Bordered avatar">

        <div class="flex flex-col space-y-5 sm:ml-8">
          <input type="file" id="profilePictureInput" class="d-none" name="profile_pic" form="updateProfileForm">
          <button type="button" onclick="profilePictureInput.click()"
              class="py-3.5 px-7 text-base font-medium text-white focus:outline-none bg-[#202142] rounded-lg border border-blue-200 hover:bg-blue-950">
              Change picture
          </button>
          <button type="button" id="deletePicture"
              class="py-3.5 px-7 text-base font-medium text-blue-950 focus:outline-none !bg-white rounded-lg border !border-blue-500 hover:!bg-blue-100 hover:text-[#202142]">
              Delete picture
          </button>
        </div>
      </div>

      @include('profile.partials.update-profile-information-form')

    </div>
  </div>

</x-profile.layout>

<script src="{{ url("libs/dashboard/sweetalert2.all.min.js") }}"></script>
<script>
  profilePictureInput.addEventListener("input", function () {
    const [file] = this.files
    if (file) {
      previewPicture.src = URL.createObjectURL(file)
    }
  });
  deletePicture.addEventListener("click", function () {
    Swal.fire({
        title: "Are You Sure ?",
        customClass: {
          confirmButton: 'bg-danger',
        },
        reverseButtons: true,
        buttonsStyling: true,
        showCancelButton: true,
        confirmButtonText: "Delete Picture",
      }).then((result) => {
        if (result.isConfirmed) {
          profilePictureInput.value = '';
          previewPicture.src = '/images/users/placeholder-user.jpg';
        }
      });
  });
</script>
