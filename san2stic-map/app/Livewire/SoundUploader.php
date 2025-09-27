<?php

namespace App\Livewire;

use App\Models\Sound;
use Livewire\Component;
use Livewire\WithFileUploads;

class SoundUploader extends Component
{
    use WithFileUploads;

    public $name;
    public $latitude;
    public $longitude;
    public $audioFile;
    public $successMessage;

    protected $rules = [
        'name' => 'required|string|max:255',
        'latitude' => 'required|numeric|between:-90,90',
        'longitude' => 'required|numeric|between:-180,180',
        'audioFile' => 'required|file|mimes:mp3,wav,ogg|max:10240', // 10MB Max
    ];

    public function save()
    {
        $this->validate();

        $path = $this->audioFile->store('sounds', 's3');

        Sound::create([
            'name' => $this->name,
            'path' => $path,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ]);

        $this->reset(['name', 'latitude', 'longitude', 'audioFile']);
        $this->successMessage = 'Sound uploaded successfully!';
    }

    public function render()
    {
        return view('livewire.sound-uploader')
            ->layout('layouts.app', ['header' => 'Upload a New Sound']);
    }
}