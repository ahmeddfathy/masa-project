<x-action-section>
    <x-slot name="title">
        {{ __('Delete Account') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Permanently delete your account.') }}
    </x-slot>

    <x-slot name="content">
        @if(auth()->user()->hasRole('admin'))
            <div class="max-w-xl text-sm text-gray-600">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
            </div>

            <div class="mt-5">
                <x-danger-button wire:click="confirmUserDeletion" wire:loading.attr="disabled">
                    <i class="fas fa-trash-alt mr-2"></i>{{ __('Delete Account') }}
                </x-danger-button>
            </div>

            <!-- Delete User Confirmation Modal -->
            <x-dialog-modal wire:model.live="confirmingUserDeletion">
                <x-slot name="title">
                    <div class="flex items-center text-red-600">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        {{ __('Delete Account') }}
                    </div>
                </x-slot>

                <x-slot name="content">
                    <div class="text-gray-700">
                        {{ __('Are you sure you want to delete your account? Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                    </div>

                    <div class="mt-4" x-data="{}" x-on:confirming-delete-user.window="setTimeout(() => $refs.password.focus(), 250)">
                        <x-input type="password"
                            class="mt-1 block w-3/4 rounded-lg shadow-sm"
                            autocomplete="current-password"
                            placeholder="{{ __('Enter your password to confirm') }}"
                            x-ref="password"
                            wire:model="password"
                            wire:keydown.enter="deleteUser" />

                        <x-input-error for="password" class="mt-2" />
                    </div>
                </x-slot>

                <x-slot name="footer">
                    <div class="flex items-center space-x-3">
                        <x-secondary-button wire:click="$toggle('confirmingUserDeletion')" wire:loading.attr="disabled">
                            <i class="fas fa-times mr-2"></i>{{ __('Cancel') }}
                        </x-secondary-button>

                        <x-danger-button wire:click="deleteUser" wire:loading.attr="disabled">
                            <i class="fas fa-trash-alt mr-2"></i>{{ __('Delete Account') }}
                        </x-danger-button>
                    </div>
                </x-slot>
            </x-dialog-modal>
        @else
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            {{ __('Account deletion is not available for customer accounts. Please contact support if you need assistance.') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </x-slot>
</x-action-section>
