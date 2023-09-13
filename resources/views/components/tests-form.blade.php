<div class="form-group">
    <label for="tests">Tests</label>
    <input type="text" id="tests">
    <button type="button" id="add-test-button" class="btn btn-blue">Add</button>
    <button type="button" id="clear-button" class="btn btn-red">Clear</button>
</div>
<div class="row pl" id="tests-list"></div>

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
