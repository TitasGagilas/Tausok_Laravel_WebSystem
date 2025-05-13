@props(['id','title'])
<div class="bg-white rounded-xl shadow-md p-5">
    <h3 class="font-semibold text-base mb-4 text-gray-700">{{ $title }}</h3>
    <div class="aspect-video relative">
        <canvas id="{{ $id }}"></canvas>
    </div>
</div>
