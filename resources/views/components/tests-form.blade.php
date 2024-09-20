<h3>Tests</h3>
<div class="form-group">
    {{-- <label for="tests">Tests</label> --}}
    <datalist id="tests-dl">
        @foreach ($tests ?? [] as $t)
            <option>{{ $t['name'] ?? $t }}</option>
        @endforeach
    </datalist>
    <input type="text" id="tests" list="tests-dl" class="input">
    <button type="button" id="add-test-button" class="btn btn-black">Add</button>
    <button type="button" id="clear-button" class="btn btn-red">Clear</button>
</div>
<div class="flex pl" id="tests-list">
    @foreach ($error['tests'] ?? [] as $test)
        <p class='tag-input'><span>{{ $test }}</span><button type='button'
                onclick="this.closest('.tag-input').remove()">&times;</button><input type='hidden' name='tests[]'
                readonly value='{{ $test }}'></p>
    @endforeach
</div>

@pushOnce('scripts')
    <script>
        $(() => {
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

            $("#clear-button").on("click", function() {
                const inputs = document.querySelectorAll("#tests-list p.tag-input");
                inputs.forEach(function(input) {
                    input.remove();
                });
            });
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
        });
    </script>
@endpushOnce
