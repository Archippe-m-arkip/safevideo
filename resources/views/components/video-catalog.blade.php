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
    <div class="relative flex">
        <input
            type="text"
            wire:model.live="search"
            placeholder="Rechercher une vidéo..."
            class="w-full z-10 pl-10 p-2 border rounded mb-6 text-gray-200"
        />
        <x-heroicon-o-magnifying-glass
            class="absolute left-3 translate-y-1/2 text-gray-200 h-5 w-4"
        />
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach ($videos as $video)
            <div
                class="border w-full rounded-lg overflow-hidden hover:border hover:border-gray-500 shadow bg-linear-to-br from-zinc-700 to-zinc-900"
            >
                <div class="text-gray-200 font-bold">
                    <div class="">
                        <div class="aspect-video">
                            <iframe
                                class="w-full h-full rounded-t-lg"
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
                <div class="w-full h-auto flex flex-col p-2 gap-1">
                    <div class="flex justify-between items-start pt-1">
                        <h3 class="font-bold text-white">
                            {{ $video->title }}
                        </h3>
                        @if ($video->is_safe)
                            <div class="h-full mt-1">
                                <x-heroicon-s-shield-check
                                    title="This video has been verified"
                                    class="w-4 h-4 cursor-pointer font-extrabold p-px rounded-sm text-white"
                                />
                            </div>
                        @else
                            <div class="h-full mt-1">
                                <x-heroicon-s-information-circle
                                    title="This video has been verified"
                                    class="w-4 h-4 cursor-pointer font-extrabold p-px rounded-sm text-red-500"
                                />
                            </div>
                        @endif
                    </div>

                    <details class="text-sm text-gray-200">
                        <summary>Description</summary>
                        {{ $video->description }}
                    </details>
                    <div class="mt-2">
                        @if ($video->is_safe)
                            <div
                                class="flex flex-col gap-1 bg-green-500/10 border border-gray-600/60 rounded-sm p-1.5"
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
                                    This video has been analysed and validated
                                    by our trustable AI for the sake of values
                                    and human moral
                                </p>
                            </div>
                        @else
                            <div
                                class="flex flex-col gap-1 bg-red-600/10 border border-gray-600/60 rounded-sm p-1.5"
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
                                    This video is being analysed by our
                                    trustable AI for the sake of values and
                                    human moral
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
