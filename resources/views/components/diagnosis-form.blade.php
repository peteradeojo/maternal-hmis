<div class="form-group" id="diagnosis-form">
    <datalist id="diagnosis-dl">
        @foreach ($diagnoses ?? [] as $d)
            <option>{{ $d['name'] }}</option>
        @endforeach
    </datalist>
    <label for="diagnosis">Diagnosis</label>
    <div class="row start">
        <div class="col-4">
            <input type="text" id="diagnosis" class="form-control" list="diagnosis-dl">
            <button type="button" id="add-diagnosis" class="btn btn-black">Add</button>
        </div>
        <div class="col-8 row px" id="diagnosis-list"></div>
    </div>
</div>

@push('scripts')
    <script>
        $(() => {
            const elem = document.getElementById("diagnosis-list")
            const input = document.querySelector("#diagnosis");

            function parseStr(str) {
                return str.split(',');
            }

            function addDiagnosis(str) {
                if (str.length < 1) return;

                // const diagnosis = document.querySelectorAll("#diagnosis-form row .tag-input")
                elem.innerHTML +=
                    `<p class='tag-input'>${str}<input type='hidden' name='diagnosis[]' value='${str}'><span class='close' onclick="this.closest('.tag-input').remove()">&times;</span></p>`

                input.value = "";
            }

            $("#diagnosis-form input").on("keypress", (e) => {
                if (e.which === 13) {
                    e.preventDefault();
                    addDiagnosis(e.target.value);
                }
            });

            $("#add-diagnosis").on('click', function() {
                // alert("Omooe");
                const val = document.querySelector("#diagnosis");
                addDiagnosis(val.value);
            })
        })
    </script>
@endpush
