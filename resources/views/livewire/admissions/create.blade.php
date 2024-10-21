<div>
    {{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}
    <p><b>Treatment Plan</b></p>

    <div class="py-2 px-2 bg-gray-100 grid gap-y-1">
        <ul class="list-disc px-3 text-sm">
            @foreach ($visit->prescriptions as $pres)
                <li>{{ $pres }}</li>
            @endforeach
        </ul>
    </div>
</div>
