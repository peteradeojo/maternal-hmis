<h3>Imaging</h3>
<div class="form-group">
    <input type="text" id="imagings">
    <button type="button" id="add-img-button" class="btn btn-black">Add</button>
    <button type="button" id="clear-button" class="btn btn-red">Clear</button>
</div>
<div class="row pl" id="imgs-list">
    @foreach ($error['imgs'] ?? [] as $i)
        <p class='tag-input'><span>{{ $i }}</span><button type='button'
                onclick="this.closest('.tag-input').remove()">&times;</button><input type='hidden' name='imgs[]'
                readonly value='{{ $i }}'>
        </p>
    @endforeach
</div>

@pushOnce('scripts')
    <script>
        $(() => {
            function parseInput(str) {
                return str.split(',');
            }

            function addImaging(e) {
                const inputs = parseInput(e.val());

                inputs.forEach(function(input) {
                    if (input === '') return;
                    $('#imgs-list').append(
                        `<p class='tag-input'><span>${input.trim()}</span><button type='button' onclick="this.closest('.tag-input').remove()">&times;</button><input type='hidden' name='imgs[]' readonly value='${input.trim()}'></p>`
                    );
                });
                e.val('');
            }

            $("#imagings").on("keypress", function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    addImaging($(this));
                }
            });

            $("#add-img-button").on("click", function() {
                const e = $("#imagings");
                addImaging(e);
            });
        })
    </script>
@endPushOnce
