<div class="mt-6 w-[90%] border-b-2 border-[#32a3cf]" id="dropDownContainer-{{ $selectId }}">
    <label class="pl-2 text-lg {{ $textColor }}">{{ $selectMessage }}</label>
    <div class="flex">
        <button class="whitespace-nowrap overflow-x-hidden w-[100%] pl-2" type="button" id="button-{{ $selectId }}">
            @if (count($options) > 0)
                {{ $options[0]->{$optionName} }}
            @endif
        </button>
        <svg class="-mr-1 size-6 text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon" class="min-h-[26px] min-w-[26px]">
            <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"></path>
        </svg>
    </div>
    <div id="selectDropdown-{{ $selectId }}" isOpen="false" class="transform opacity-0 scale-0 absolute right-0 z-3 mt-2 w-90 sm:w-130 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-hidden">
        <div class="relative">
            <div class="flex lg:w-full relative">
                <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="24" height="24" viewBox="0 0 30 30" stroke="#666666" fill="#666666" class="mt-4 absolute left-3">
                    <path d="M 13 3 C 7.4889971 3 3 7.4889971 3 13 C 3 18.511003 7.4889971 23 13 23 C 15.396508 23 17.597385 22.148986 19.322266 20.736328 L 25.292969 26.707031 A 1.0001 1.0001 0 1 0 26.707031 25.292969 L 20.736328 19.322266 C 22.148986 17.597385 23 15.396508 23 13 C 23 7.4889971 18.511003 3 13 3 z M 13 5 C 17.430123 5 21 8.5698774 21 13 C 21 17.430123 17.430123 21 13 21 C 8.5698774 21 5 17.430123 5 13 C 5 8.5698774 8.5698774 5 13 5 z"></path>
                </svg>
                <input type="text" id="search-{{ $selectId }}" class="bg-white text-black p-4 rounded-lg w-[100%] border-2 border-gray-200 pl-10" placeholder="Search by Keyword">
            </div>
        </div>
        <div class="p-4 flex flex-col items-center">
            <ul id="searchTable-{{ $selectId }}" class="overflow-hidden overflow-y-scroll w-full h-70">
                @foreach ($options as $option)
                    <li class="p-2 cursor-pointer hover:bg-[#f2f2f2] text-gray-500" wire:click="$js.setPermission('{{ $option->{$optionName} }}')"  id="{{ $option->{ $optionId } }}" >{{ $option->{$optionName} }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@script
<script>
    let id = $wire.selectId;
    let OGTable = [];
    $("#searchTable-"+id).children().each(function(index){
        OGTable.push($(this).clone(true,true)[0]);
    });
    $("#search-"+id).on('input',function(ev){
        SearchThroughTable($("#search-"+id).val());
    })
    $js("setPermission",function(permission){
        $("#button-"+id).text(permission);
        let Dropdown = $("#selectDropdown-"+id);
        Dropdown.removeClass("transition ease-out duration-100");
        Dropdown.addClass("transition ease-in duration-75");
        Dropdown.addClass("transform opacity-0 scale-0");
        Dropdown.removeClass("transform opacity-100 scale-100");
        Dropdown.attr("isOpen",false);
    })
    $("#button-"+id).on("click",function(){
        let Dropdown = $("#selectDropdown-"+id);
        if (Dropdown.attr("isOpen") == "false"){
            Dropdown.addClass("transition ease-out duration-100");
            Dropdown.removeClass("transform opacity-0 scale-0");
            Dropdown.addClass("transform opacity-100 scale-100");
            Dropdown.attr("isOpen",true); 
        }
        else{
            Dropdown.removeClass("transition ease-out duration-100");
            Dropdown.addClass("transition ease-in duration-75");
            Dropdown.addClass("transform opacity-0 scale-0");
            Dropdown.removeClass("transform opacity-100 scale-100");
            Dropdown.attr("isOpen",false);
        }
        
    })
    document.addEventListener("click",function(ev){
            let parent = ev.target.parentNode;
            let Dropdown = $("#selectDropdown-"+id);
            while (parent.id != "selectDropdown-"+id && parent.id != "dropDownContainer-"+id ){
                parent = parent.parentNode;
                if (parent === null){
                    break;
                }
            }
            if (parent === null){
                Dropdown.removeClass("transition ease-out duration-100");
                Dropdown.addClass("transition ease-in duration-75");
                Dropdown.addClass("transform opacity-0 scale-0");
                Dropdown.removeClass("transform opacity-100 scale-100");
                Dropdown.attr("isOpen",false);
            }
    })
    function SearchThroughTable(searchInput){
                try{
                    let FilteredTable = []
                    let OGTableCopy = [];
                    $(OGTable).each(function(index){
                        OGTableCopy.push($(this).clone(true,true)[0]);
                    })
                    if (searchInput != ""){
                        $(OGTableCopy).each(function(index){
                            //adding tr to filtered list
                            if ($(this).text().toLowerCase().includes(searchInput.toLowerCase())){
                                FilteredTable.push($(this));
                            }
                            //highlighting matching bits
                            if ($(this).text().toLowerCase().includes(searchInput.toLowerCase())){

                                let CleanInput = CleanseSearch(searchInput.toLowerCase());
                                let Regex = new RegExp(""+CleanInput+"","ig")
                                let NewText = $(this).text();
                                NewText = NewText.replace(Regex,'<span class="bg-yellow-500 text-white">$&</span>');
                                $(this).html(NewText);
                            }
                        })
                    }
                    $("#searchTable-"+id).empty();
                    if (FilteredTable.length == 0){
                        $(OGTable).each(function(index){
                            $("#searchTable-"+id).append($(this));
                        })
                    }
                    else{
                        $(FilteredTable).each(function(index){
                            $("#searchTable-"+id).append($(this));
                        })
                    }
                }
                catch(e){
                    console.log(e);
                }
            }
function CleanseSearch(input){
    try{
        //add more as needed...
        input = input.replace("(","\\(");
        input = input.replace(")","\\)");
        input = input.replace("{","\\{");
        input = input.replace("}","\\}");
        
        return input;
                    
    }   
    catch(e){
        console.log(e);
    }
}
</script>
@endscript