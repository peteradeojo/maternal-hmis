<div>
    <div class="grid grid-cols-3 gap-4">
        <p><b>Consultant:</b> {{ $opnote->consultant }}</p>
        <p><b>Unit:</b> {{ $opnote->unit }}</p>
        <p><b>Date:</b> {{ $opnote->operation_date }}</p>
        <p><b>Surgeon(s):</b> {{ $opnote->surgeons }}</p>
        <p><b>Assistant(s):</b> {{ $opnote->assistants }}</p>
        <p><b>Anaethisist(s):</b> {{ $opnote->anaesthesists }}</p>
        <p><b>Anaethia Type:</b> {{ $opnote->anaesthesia_type }}</p>
        <p class="col-span-2"><b>Indication:</b> {{ $opnote->indication }}</p>
        <p class="col-span-2"><b>Incision:</b> {{ $opnote->incision }}</p>
        <p class="col-span-2"><b>Findings:</b> {{ $opnote->findings }}</p>
        <p class="col-span-2"><b>Procedure:</b> {{ $opnote->procedure }}</p>
    </div>

    <div class="py-5">
        <p><b>Submitted: </b> {{ $opnote->created_at->format('Y-m-d h:i A') }}</p>
        <p><b>By: </b> {{ $opnote->user->name }}</p>
    </div>
</div>
