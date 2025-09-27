import './bootstrap';

import L from 'leaflet';
import axios from 'axios';
import WaveSurfer from 'wavesurfer.js';
import RegionsPlugin from 'wavesurfer.js/dist/plugins/regions.js';

document.addEventListener('DOMContentLoaded', () => {
    // Map Logic
    const mapElement = document.getElementById('map');
    if (mapElement) {
        const map = L.map('map').setView([48.8566, 2.3522], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        const audioPlayer = new Audio();
        axios.get('/api/sounds')
            .then(response => {
                const sounds = response.data;
                sounds.forEach(sound => {
                    const marker = L.marker([sound.latitude, sound.longitude])
                        .addTo(map)
                        .bindPopup(`<b>${sound.name}</b><br><a href="/sound/${sound.id}/edit" class="text-blue-500 hover:underline">Edit Sound</a><br><small>Click marker to play</small>`);
                    marker.on('click', () => {
                        audioPlayer.src = sound.url;
                        audioPlayer.play();
                    });
                });
            })
            .catch(error => console.error('Error fetching sounds:', error));
    }

    // Wavesurfer Logic
    const waveformEl = document.getElementById('waveform');
    if (waveformEl) {
        const playBtn = document.getElementById('playBtn');
        const trimBtn = document.getElementById('trimBtn');
        const statusEl = document.getElementById('status');
        const soundUrl = waveformEl.dataset.url;

        const wavesurfer = WaveSurfer.create({
            container: waveformEl,
            waveColor: 'rgb(200, 200, 200)',
            progressColor: 'rgb(100, 100, 100)',
            url: soundUrl,
        });

        const wsRegions = wavesurfer.registerPlugin(RegionsPlugin.create());

        wavesurfer.on('ready', () => {
            wsRegions.addRegion({
                start: 1,
                end: 3,
                color: 'rgba(255, 0, 0, 0.1)',
            });
        });

        let activeRegion = null;
        wsRegions.on('region-updated', (region) => {
            activeRegion = region;
            trimBtn.disabled = false;
        });
        wsRegions.on('region-out', (region) => {
            if (activeRegion === region) {
                wavesurfer.pause();
            }
        });

        playBtn.addEventListener('click', () => {
            wavesurfer.playPause();
        });

        trimBtn.addEventListener('click', () => {
            if (activeRegion) {
                statusEl.textContent = 'Trimming...';
                trimBtn.disabled = true;
                const soundId = window.location.pathname.split('/')[2];
                axios.post(`/sound/${soundId}/trim`, {
                    start: activeRegion.start,
                    end: activeRegion.end,
                })
                .then(response => {
                    statusEl.textContent = 'Trim successful! Reloading...';
                    setTimeout(() => window.location.reload(), 1500);
                })
                .catch(error => {
                    statusEl.textContent = 'An error occurred during trimming.';
                    console.error('Trim error:', error);
                    trimBtn.disabled = false;
                });
            }
        });
    }
});
