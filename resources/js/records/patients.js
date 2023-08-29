import DataTable from "../app";

const table = new DataTable("#patients", {
    columns: [
        "name",
        "category.name",
        "card_number",
        "gender_value",
        ({id}) => `<a href='/records/patients/${id}' class='mr-2'>View</a>`,
    ],
});
table.load({
    url: "/api/records/patients",
});
