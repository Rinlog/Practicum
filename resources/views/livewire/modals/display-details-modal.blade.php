<!-- Main modal -->
<div id="DisplayMessage" class="hide relative z-10 shadow-md transition-all duration-100" aria-labelledby="modal-title" role="dialog" aria-modal="true">
  <div id="DisplayMessageFrame" class="fixed inset-0 bg-gray-500/75 transition-all opacity-0 ease-in duration-200" aria-hidden="true"></div>
  <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div id="DisplayMessageMain" hasEventListener="false" class="transition-all opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95 ease-in duration-200 relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl my-8 w-full max-w-xl">
        <!--Header-->
        <div class="bg-white px-4 pb-4 pt-2 flex justify-center items-center flex-col">
            <button wire:click="$js.closeDetails"id="closebutton" type="button" class="self-end text-gray-400 hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-10 h-10 ms-auto inline-flex justify-center items-center cursor-pointer">
                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
            </button>
            <h1 class="text-[#00719d]"><b>Log Details</b></h1>
        </div>
        <!--Content-->
        <div class="flex flex-col items-start justify-start pb-2 text-[#737373] flex-wrap text-start px-10 gap-2">
            @if ($application == "true")
            <label class="text-2xl font-semibold">
              Application: 
              <label id="ApplicationDetailsModal" class="font-normal"></label>
            </label>
            @endif
            <label class="text-2xl font-semibold">
              Date: 
              <label id="DateDetailsModal" class="font-normal"></label>
            </label>
            <label class="text-2xl font-semibold">
              Time: 
              <label id="TimeDetailsModal" class="font-normal"></label>
            </label>
            <label class="text-2xl font-semibold">
              Activity: 
              <label id="ActivityDetailsModal" class="font-normal"></label>
            </label>
            <label class="text-2xl font-semibold">
              User: 
              <label id="UserDetailsModal" class="font-normal"></label>
            </label>
            <label class="text-2xl font-semibold">
              Description: <br>
              <label id="DescriptionDetailsModal" class="font-normal text-wrap"></label>
            </label>
        </div>
        <!--Footer-->
        <div class="bg-gray-50 px-4 py-4 justify-center items-center flex">
          <x-bigbutton width="w-2/5" id="OK" text="OK" customStyle="text-lg font-semibold" wireclick="$js.closeDetails"></x-bigbutton>
        </div>
      </div>
    </div>
  </div>
</div>
@script
<script>
  if (0){}

  function CloseModal(){
    $("#DisplayMessageFrame").addClass("opacity-0 ease-in duration-200");
    $("#DisplayMessageFrame").removeClass("opacity-100 ease-out duration-300");
    $("#DisplayMessageMain").addClass("opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95 ease-in duration-200");
    $("#DisplayMessageMain").removeClass("opacity-100 translate-y-0 sm:translate-y-0 sm:scale-95");
  }
  $js("closeDetails",function(){
    CloseModal();
    setTimeout(() => {
      $("#DisplayMessage").addClass("hide");
    }, 200);
  })
</script>
@endscript