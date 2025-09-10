@props(["placeholder" => "", "id", "src"=>"", "type" => "text", 'customStyle' => ""])
<div class="w-full">
    <input wire:model="{{ $src }}" id="{{ $id }}" type="{{$type}}" placeholder={{ $placeholder }} class="bg-[#f2f2f2] p-3 rounded-lg  w-full {{ $customStyle }}">
</div>