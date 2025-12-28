@props(['id'])

$("{{$id}}").DataTable({
    processing: true,
    {{$slot}}
})
