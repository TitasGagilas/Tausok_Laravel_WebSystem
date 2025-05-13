<x-app-layout>
    <div class="min-h-screen px-4 sm:px-6 lg:px-8 py-10">

        <div class="max-w-4xl mx-auto space-y-6">

            <h1 class="text-xl font-semibold text-center text-gray-800">Profilio Nustatymai</h1>

            {{-- Profile Information Card --}}
            <div class="p-6 bg-white shadow rounded-xl">
                @include('profile.partials.update-profile-information-form')
            </div>

            {{-- Update Password Card --}}
            <div class="p-6 bg-white shadow rounded-xl">
                @include('profile.partials.update-password-form')
            </div>

            {{-- Delete Account Card --}}
            <div class="p-6 bg-white shadow rounded-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
