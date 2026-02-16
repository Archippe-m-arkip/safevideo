<?php

// use Livewire\Component;

// new class extends Component
// {
//     //
// };
?>

<div
    class="p-6 w-full md:w-3/4 xl:w-1/2 bg-red-200/10 rounded-2xl backdrop-blur-xl"
>
    <input
        type="text"
        wire:model.live="search"
        placeholder="Rechercher une vidéo..."
        class="w-full p-2 border rounded mb-6 text-amber-100"
    />

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach ($videos as $video)
            <div
                class="border w-full rounded-sm shadow p-4 bg-linear-to-br from-zinc-700 to-zinc-900"
            >
                <div class="flex justify-between items-center">
                    <h3 class="font-bold text-white">{{ $video->title }}</h3>
                    @if ($video->is_safe)
                        <x-heroicon-s-shield-check
                            title="This video has been verified"
                            class="w-4 h-4 cursor-pointer font-extrabold p-px rounded-sm text-white"
                        />
                    @else
                        <span
                            class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded"
                        >
                            ⏳
                        </span>
                    @endif
                </div>
                <p class="text-sm text-gray-200">{{ $video->description }}</p>
                <div class="mt-2 text-blue-500 font-bold">
                    <div class="mt-4">
                        <div class="aspect-video">
                            <iframe
                                class="w-full h-full rounded"
                                src="https://www.youtube.com/embed/{{ $video->youtube_id }}"
                                title="YouTube video player"
                                frameborder="0"
                                allow="
                                    accelerometer;
                                    autoplay;
                                    clipboard-write;
                                    encrypted-media;
                                    gyroscope;
                                    picture-in-picture;
                                "
                                allowfullscreen
                            ></iframe>
                        </div>
                    </div>
                </div>
                <div class="mt-2">
                    @if ($video->is_safe)
                        <div
                            class="flex flex-col gap-1 border border-gray-600/60 rounded-sm p-1.5"
                        >
                            <div
                                class="flex gap-2 items-center text-white font-semibold text-sm"
                            >
                                <x-heroicon-s-shield-check
                                    title="This video has been verified"
                                    class="w-4 h-4 cursor-pointer font-extrabold p-px rounded-sm bg-blue-500/40 text-white"
                                />

                                <p>Safe</p>
                            </div>
                            <p class="text-gray-200 text-xs">
                                This video has been analysed and validated by
                                our trustable AI for the sake of values and
                                human moral
                            </p>
                        </div>
                    @else
                        <div
                            class="flex flex-col gap-1 border border-gray-600/60 rounded-sm p-1.5"
                        >
                            <div
                                class="flex gap-2 items-center text-white font-semibold text-sm"
                            >
                                <x-heroicon-s-information-circle
                                    title="This video has been verified"
                                    class="w-4 h-4 cursor-pointer font-extrabold p-px rounded-sm text-red-500"
                                />
                                <p>In verification</p>
                            </div>
                            <p class="text-gray-200 text-xs">
                                This video is being analysed by our trustable AI
                                for the sake of values and human moral
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
