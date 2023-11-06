<h3>Complaints</h3>
<datalist id="complaint-list">
    @foreach (($complaints ?? []) as $c)
        <option value="{{ $c['name'] }}">{{ $c['name'] }}</option>
    @endforeach
</datalist>
<div class="row start">
    <div class="form-group col-6 pr-1">
        <label for="complaints-input">Complaints</label>
        <div class="row pb-1">
            <input type="text" id="complaints-input" class="form-control" list="complaint-list">
            <button type="button" id="add-complaint-button" class="btn btn-black">Add</button>
        </div>
        <div id="complaints">
        </div>
    </div>
    {{-- <div class="form-group col-6 pr-1">
        <label for="durations">Complaints Duration</label>
        <textarea name="complaints_durations" id="durations" class="form-control"></textarea>
    </div> --}}
    <div class="form-group col-6">
        <label for="history">History of Complaints</label>
        <textarea name="history" id="history" class="form-control"></textarea>
    </div>
</div>

@push('scripts')
    <script defer>
        $(function() {
            function parseTests(str) {
                return str.split(',');
            }

            function addComplaint(elem) {
                const inputs = parseTests(elem.val());

                inputs.forEach(function(input) {
                    if (input === '') return;
                    const complaints = document.querySelectorAll('#complaints .tag-input');
                    $('#complaints').append(
                        `<div class='tag-input-2 mb-1'><p class='tag-input'>
                            <span>${input.trim()}</span>
                            <button type='button' onclick="this.closest('.tag-input-2').remove()">&times;</button>
                            <input type='hidden' name="complaints[${complaints.length}][name]" readonly value='${input.trim()}'>
                            </p>
                            <input type='text' name="complaints[${complaints.length}][duration]" placeholder='Duration' />
                        </div>`
                    );
                });
                elem.val('');
            }

            $("#complaints-input").on('keypress', function(e) {
                if (e.which == 13) {
                    e.preventDefault();
                    // alert($(this).val())
                    addComplaint($(this));
                }
            })

            $("#add-complaint-button").on('click', function() {
                const e = $("#complaints-input");
                addComplaint(e);
            });
        });
    </script>
@endpush
