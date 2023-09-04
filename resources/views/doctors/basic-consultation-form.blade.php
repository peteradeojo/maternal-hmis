<form action="" method="post">
    @csrf
    <div class="form-group">
        <label for="complaints">Complaints</label>
        <textarea name="symptoms" id="complaints" cols="30" class="form-control"></textarea>
    </div>
    <div class="form-group">
        <label for="diagnosis">Diagnosis</label>
        <textarea name="prognosis" id="diagnosis" cols="30" class="form-control"></textarea>
    </div>
    <div class="row">
        <div class="form-group col-4">
            <label for="tests">Tests</label>
            <input type="text" id="tests">
            <button type="button" id="add-test-button">Add</button>
        </div>
        <div class="col-8 row pl" id="tests-list"></div>
    </div>
    <fieldset class="px">
        <legend>Treatments</legend>
        <input type="text" id="treatments">
        <table id="treatments-list" class="mt-1 table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Dosage</th>
                    <th>Duration</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </fieldset>
    <div class="form-group">
        <label for="remarks">Remarks</label>
        <textarea name="comment" id="remarks" cols="30" class="form-control"></textarea>
    </div>
    <div class="form-group">
        <label for="next_visit">Next Visit</label>
        <input type="date" name="next_visit" id="next_visit" class="form-control" min="{{ date('Y-m-d') }}">
    </div>
    <div class="form-group">
        <button class="btn btn-red" type="submit">Submit</button>
    </div>
</form>
@pushOnce('scripts')
    <script>
        $(function() {
            function parseTests(str) {
                return str.split(',');
            }

            function addTestToList(elem) {
                const inputs = parseTests(elem.val());

                inputs.forEach(function(input) {
                    if (input === '') return;
                    $('#tests-list').append(
                        `<p class='tag-input'><span>${input.trim()}</span><button type='button' onclick="this.closest('.tag-input').remove()">&times;</button><input type='hidden' name='tests[]' readonly value='${input.trim()}'></p>`
                    );
                });
                elem.val('');
            }

            $("#add-test-button").on("click", function() {
                const e = $("#tests");
                addTestToList(e);
            });
            $('#tests').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    addTestToList($(this));
                }
            });

            function addTreatment(elem) {
                const rowCount = $("#treatments-list tbody tr").length;
                const row = document.createElement('tr');
                const td = document.createElement('td');
                const td1 = document.createElement('td');
                const td2 = document.createElement('td');
                const input = document.createElement('input');
                const button = document.createElement('button');
                const tbody = document.querySelector('#treatments-list tbody');

                input.type = 'text';
                input.name = `treatments[${rowCount}]`;
                input.setAttribute('value', elem.val());
                input.readOnly = true;
                button.type = 'button';
                button.textContent = 'Remove';
                button.addEventListener('click', function() {
                    row.remove();
                });

                td1.innerHTML = `<input type="text" name="dosage[${rowCount}]" class="form-control">`;
                td2.innerHTML = `<input type="text" name="duration[${rowCount}]" class="form-control">`;

                td.appendChild(input);
                td.appendChild(button);
                row.appendChild(td);
                row.appendChild(td1);
                row.appendChild(td2);
                tbody.appendChild(row);
                elem.val('');
            }
            $('#treatments').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    addTreatment($(this));
                }
            });
        });
    </script>
@endPushOnce
