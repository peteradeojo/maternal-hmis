<div>
</div>

@script
    <script>
        let dt = $("#table").DataTable();
        $wire.on('update-data', (event) => {
            console.log(event.data);
            // dt.destroy();

            // dt = $('#table').DataTable({
            //     // Your DataTable options here
            // });
        });
    </script>
@endscript
