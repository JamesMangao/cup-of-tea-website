<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-white">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-1 text-sm text-gray-400">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('Delete Account') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 rounded-full bg-red-500/10 flex items-center justify-center">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#e05555" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-white">
                        {{ __('Delete Your Account?') }}
                    </h2>
                    <p class="text-xs text-gray-400">This action cannot be undone</p>
                </div>
            </div>

            <p class="text-sm text-gray-300 mb-6">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="mb-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="text-gray-300 mb-2" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="block w-full bg-black/40 border border-[#2e2e2e] rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:border-red-500 focus:ring-1 focus:ring-red-500"
                    placeholder="{{ __('Enter your password') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')" class="px-4 py-2 bg-gray-800 border border-gray-700 text-gray-300 hover:bg-gray-700 rounded-lg">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">
                    {{ __('Delete Forever') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
