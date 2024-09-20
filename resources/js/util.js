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
