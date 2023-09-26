<h3>Complaints</h3>
<div class="row start">
    <div class="form-group col-6 pr-1">
        <label for="complaints-input">Complaints</label>
        <div class="row pb-1">
            <input type="text" id="complaints-input" class="form-control">
            <button type="button" id="add-complaint-button" class="btn btn-blue">Add</button>
        </div>
        <div id="complaints" class="row">
        </div>
    </div>
    <div class="form-group col-6 pl-1">
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
                    $('#complaints').append(
                        `<p class='tag-input'>
                            <span>${input.trim()}</span>
                            <button type='button' onclick="this.closest('.tag-input').remove()">&times;</button>
                            <input type='hidden' name='complaints[]' readonly value='${input.trim()}'>
                        </p>`
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
