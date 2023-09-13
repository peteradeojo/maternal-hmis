$().ready(function () {
    $("#checkIn")?.on("click", async function (e) {
        e.preventDefault();
        e.stopPropagation();
        const { id } = this.dataset;

        const token = document
            .querySelector("meta[name='csrf-token']")
            .getAttribute("content");

        try {
            const url = `/api/records/patients/${id}/check-in`;
            const res = await fetch(url, {
                method: "POST",
                headers: {
                    Accept: "application/json",
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": token,
                },
            });

            const data = await res.json();
            if (data.success) {
                triggerAlert("success", data.message);
            } else {
                triggerAlert("danger", data.message);
            }
        } catch (err) {
            triggerAlert("danger", err.message);
        }
    });

    $("#createAncVisit").on("click", async function (e) {
        e.preventDefault();
        e.stopPropagation();
        const { id } = this.dataset;

        const token = document
            .querySelector("meta[name='csrf-token']")
            .getAttribute("content");

        try {
            const url = `/api/records/patients/${id}/check-in?mode=anc`;
            const res = await fetch(url, {
                method: "POST",
                headers: {
                    Accept: "application/json",
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": token,
                },
            });

            const data = await res.json();
            if (data.success) {
                triggerAlert("success", data.message);
            } else {
                triggerAlert("danger", data.message);
            }
        } catch (err) {
            // console.error(err);
            triggerAlert("danger", err.message);
        }
    });
});
