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

@pushOnce('scripts')
    <script>
        $(function() {
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