<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Video;

class VideoCatalog extends Component
{
    public $search = '';

    public function render()
    {
        // On récupère les vidéos qui correspondent à la recherche
        $videos = Video::where('title', 'like', '%' . $this->search . '%')->get();

        return view('components.video-catalog', [
            'videos' => $videos
        ]);
    }
}