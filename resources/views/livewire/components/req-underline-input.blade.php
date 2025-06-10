@props(["type"=>"text", "id" => "", "value"=>"", "placeholder"=>"",])
<div class="border-b-2 border-[#32a3cf] pl-2 mt-4 w-[90%]">
    <label class="text-lg {{ $textColor }}">{{ $text }}</label>
    <input type="{{ $type }}" id="{{ $id }}" placeholder="{{ $placeholder }}" value="{{ $value }}" required class="w-full outline-none text-lg {{ $inputColor }}">
</div>
