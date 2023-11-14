<div class="card py px">
    <div class="card-header">Encounter Report</div>
    <button onclick="window.print()" class="no-print">Print</button>
    <div class="body">
        <div class="row start">
            <div class="col-6">
                <p><b><u>Patient:</u></b> {{ $visit->patient->name }}</p>
                <p><b><u>Date:</u></b> {{ $visit->created_at->format('Y-m-d h:i A') }}</p>
                <p><b><u>Type:</u></b> {{ $visit->visit->type }}</p>
            </div>
            <div class="col-6">
                <p><u>Vitals:</u></p>
                @include('doctors.components.vitals', ['visit' => $visit])
            </div>
        </div>
        <div class="pb-2"></div>
        @forelse ($visit->documentations as $doc)
            @include('doctors.components.documentation', ['doc' => $doc])
        @empty
            <p>No documentation was found for this encounter</p>
        @endforelse
    </div>
</div>
