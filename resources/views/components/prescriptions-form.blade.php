<fieldset class="">
    <datalist id="prescriptions-dl">
        @foreach ($prescriptions ?? [] as $p)
            <option value="{{ $p['name'] }}">{{ $p['name'] }}</option>
        @endforeach
    </datalist>
    <legend>Treatments</legend>
    <input type="text" id="treatments" class="input" list="prescriptions-dl">
    <button type="button" id="add-treatment" class="btn btn-black">Add</button>
    <table id="treatments-list" class="mt-1 table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Route</th>
                <th>Dosage</th>
                <th>Frequency</th>
                <th>Duration</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</fieldset>

@pushOnce('scripts')
    <script>
        $(function() {
            function addTreatment(elem) {
                const rowCount = $("#treatments-list tbody tr").length;
                const row = document.createElement('tr');
                const td = document.createElement('td');
                const td1 = document.createElement('td');
                const td2 = document.createElement('td');
                const td3 = document.createElement('td');
                const td4 = document.createElement('td');
                const input = document.createElement('input');
                const button = document.createElement('button');
                const tbody = document.querySelector('#treatments-list tbody');

                const val = elem.val();

                if (val.length < 1) return;

                input.type = 'text';
                input.name = `treatments[${rowCount}]`;
                input.setAttribute('value', val);
                input.readOnly = true;
                button.type = 'button';
                button.innerHTML = '&times;';
                button.classList.add('btn',  'w-12', 'h-12');
                button.addEventListener('click', function() {
                    row.remove();
                });

                td1.innerHTML = `<input type="text" name="dosage[${rowCount}]" class="form-control" required>`;
                td2.innerHTML = `<input type="text" name="frequency[${rowCount}]" class="form-control" required>`;
                td3.innerHTML = `<input type="text" name="duration[${rowCount}]" class="form-control" required>`;
                td4.innerHTML = `<input type='text' name="route[${rowCount}]" class='form-control' />`;

                td.appendChild(input);
                td.appendChild(button);
                row.appendChild(td);
                row.appendChild(td4);
                row.appendChild(td1);
                row.appendChild(td2);
                row.appendChild(td3);
                tbody.appendChild(row);
                elem.val('');
            }
            $('#treatments').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    addTreatment($(this));
                }
            });

            $('#add-treatment').on('click', function() {
                addTreatment($('#treatments'));
            });
        });
    </script>
@endPushOnce
