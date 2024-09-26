document.addEventListener("DOMContentLoaded", () => {
    document
        .querySelectorAll(".foldable .foldable-header")
        .forEach((button) => {
            button.addEventListener("click", (e) => {
                const elem = e.target.closest(".foldable");
                elem.querySelector(".foldable-body").classList.toggle(
                    "unfolded"
                );
            });
        });

    document.querySelectorAll(".modal").forEach((modal) => {
        modal.addEventListener("click", (e) => {
            if (e.target.classList.contains("modal")) {
                e.target.classList.add("hide");
            }
        });
    });

    document.querySelectorAll(".modal-trigger").forEach((trigger) => {
        trigger.addEventListener("click", (e) => {
            const { target } = e.target.dataset;
            const modal = document.querySelector(target);
            modal?.classList.remove("hide");
        });
    });

    // document.querySelectorAll(".tabs").forEach((tabspace) => {
    //     const tabList = document.querySelector(tabspace.dataset.list);
    //     // tabList.querySelectorAll('.tab').forEach(t => t.classList.add('hide'));

    //     tabspace.querySelectorAll(".tab-item").forEach((btn) => {
    //         btn.addEventListener("click", function (e) {
    //             tabspace
    //                 .querySelectorAll(".tab-item")
    //                 .forEach((t) => t.classList.remove("active"));

    //             tabList
    //                 .querySelectorAll(".tab")
    //                 .forEach((t) => t.classList.add("hide"));
    //             tabList
    //                 .querySelector(`.tab${btn.dataset.target}`)
    //                 ?.classList.remove("hide");
    //             btn.classList.add("active");
    //         });
    //     });
    // });
});


function asyncForm(form, route, callback = (e, data) => {}) {
    $(form).on("submit", (e) => {
        e.preventDefault();
        fetch(route, {
            method: 'POST',
            body: new FormData(e.currentTarget),
            headers: {
                'Accept': 'application/json',
            },
        }).then((res) => {
            callback(e.currentTarget, res);
        }).catch((err) => {
            console.error(err);
        });
    });
}
