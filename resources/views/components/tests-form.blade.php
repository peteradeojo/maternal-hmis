<div class="row">
    <div class="form-group col-4">
        <label for="tests">Tests</label>
        <input type="text" id="tests">
        <button type="button" id="add-test-button">Add</button>
    </div>
    <div class="col-8 row pl" id="tests-list"></div>
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
