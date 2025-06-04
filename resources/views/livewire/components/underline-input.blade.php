@props(["type"=>"text", "id" => "", "value"=>"", "placeholder"=>"","customStyles"=>"", "borderColor" => "border-[#32a3cf]"])
<div class="border-b-2 {{ $borderColor }} pl-2 mt-4 w-[90%]">
    <label class="text-lg">{{ $text }}</label>
    <input type="{{ $type }}" id="{{ $id }}" placeholder="{{ $placeholder }}" value="{{ $value }}" class="w-full outline-none text-lg text-white {{ $customStyles }}">
</div>
