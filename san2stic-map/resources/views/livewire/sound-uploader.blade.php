<div>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <form wire:submit.prevent="save">
                        @if ($successMessage)
                            <div role="alert" class="alert alert-success mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                <span>{{ $successMessage }}</span>
                            </div>
                        @endif

                        <div class="form-control w-full mb-4">
                            <label class="label">
                                <span class="label-text">Sound Name</span>
                            </label>
                            <input type="text" wire:model="name" class="input input-bordered w-full" />
                            @error('name') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text">Latitude</span>
                                </label>
                                <input type="text" wire:model="latitude" class="input input-bordered w-full" />
                                @error('latitude') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-control w-full">
                                <label class="label">
                                    <span class="label-text">Longitude</span>
                                </label>
                                <input type="text" wire:model="longitude" class="input input-bordered w-full" />
                                @error('longitude') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="form-control w-full mb-4">
                            <label class="label">
                                <span class="label-text">Audio File</span>
                            </label>
                            <input type="file" wire:model="audioFile" class="file-input file-input-bordered w-full" />
                            <div wire:loading wire:target="audioFile" class="text-sm opacity-75 mt-1">Uploading...</div>
                            @error('audioFile') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="card-actions justify-end mt-6">
                            <button type="submit" class="btn btn-primary">
                                <span wire:loading.remove wire:target="save">Upload Sound</span>
                                <span wire:loading wire:target="save" class="loading loading-spinner"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
