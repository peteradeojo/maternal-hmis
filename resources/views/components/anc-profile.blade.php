<div class="grid grid-cols-3 gap-y-2">
    <p><b>Date of Booking: </b>
        {{ $ancProfile->created_at?->format('Y-m-d') }}</p>
    <p><b>Card Type: </b>
        {{ $ancProfile->card_type }}
    </p>
    <p><b>EDD: </b> {{ $ancProfile->edd?->format('Y-m-d') }}</p>
    <p><b>Weeks of Gestation: </b>
        {{ $ancProfile->lmp ? $ancProfile->lmp->diffInWeeks() . ' week(s)' : 'LMP Not Supplied' }}
    </p>
    <p><b>Gravida: </b> {{ $ancProfile->gravida }}</p>
    <p><b>Parity: </b> {{ $ancProfile->parity }}</p>
    <p><b>Height: </b> {{ $ancProfile->height }} cm</p>
    <p><b>Weight: </b> {{ $ancProfile->weight }} kg</p>
    <p><b>BP: </b> {{ $ancProfile->bp }} mmHg</p>
    <p><b>HB: </b> {{ $ancProfile->hb }} g/dl</p>
    <p><b>Urine: </b> {{ $ancProfile->urine }}</p>
    <p><b>VDRL: </b> {{ $ancProfile->vdrl }}</p>
    <p><b>HIV: </b> {{ $ancProfile->hiv }}</p>
    <p><b>HEP B: </b> {{ $ancProfile->hep_b }}</p>
    <p><b>HEP C: </b> {{ $ancProfile->hep_c }}</p>
    <p><b>Other: </b> {{ $ancProfile->other }}</p>
</div>
