<div class="card py px my">
    <div class="header">
        <div class="row between">
            <h2 class="card-header">Actions</h2>
        </div>
    </div>
    <div class="body py">
        <div class="grid grid-cols-4 gap-x-2">
            <a href="{{route('nurses.vitals')}}" class="flex justify-between border-4 hover:bg-gray-50 hover:font-bold transition-all duration-[.2s] border-green-500 p-3 rounded min-h-32 items-center">
                Take Vitals

                <span class="text-red-600 text-2xl">{{$visits}}</span>
            </a>
        </div>
    </div>
</div>
