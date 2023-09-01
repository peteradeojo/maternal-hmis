import DataTable, { fetchToken } from "../app";

const table = new DataTable("#patients", {
    columns: [
        "name",
        "category.name",
        "card_number",
        "gender_value",
        ({ id }) => `<a href='/records/patients/${id}' class='mr-2'>View</a>`,
    ],
});
if (table.element) {
    table.load({
        url: "/api/records/patients",
    });
}

document.querySelector("#checkIn")?.addEventListener("click", async (e) => {
    e.preventDefault();

    try {
        const token = await fetchToken();
        const { id } = e.target.dataset;
        const res = await fetch(`/api/records/patients/check-in/${id}`, {
            method: "POST",
            headers: {
                Accept: "application/json",
                Authorization: `Bearer ${token}`,
            },
        });

        const data = await res.json();
        if (data.success) {
            alert(data.message);
            window.location.reload();
        }
    } catch (err) {
        console.error(err);
        alert(err.message);
    }
});
