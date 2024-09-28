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
        // modal.addEventListener("click", (e) => {
        //     if (e.target.classList.contains("modal")) {
        //         e.target.classList.add("hide");
        //     }
        // });

        modal.addEventListener("mousedown", (e) => {
            // Check if the click is directly on the modal backdrop
            if (e.target === modal) {
                modal.classList.add("hide");
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

function initTab(el) {
    const tabNav = el.querySelector("nav")

    const tabList = tabNav.querySelectorAll("a");

    const tabContentList = el.querySelectorAll(el.getAttribute("data-tablist") + " > .tab");

    tabContentList.forEach((e, i) => {
        i > 0 && e.classList.add("hidden")
    });

    tabList?.forEach((element, i) => {
        element.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            tabContentList.forEach((elj) => elj.classList
                .add(
                    "hidden"));

            tabList?.forEach((eli, j) => {
                eli.classList.add("default-tab");
                eli.classList.remove("active-tab");
                eli.setAttribute("aria-current", "page");
            });

            tabList[i]?.classList.remove("default-tab");
            tabList[i]?.classList.add("active-tab");

            tabContentList[i]?.classList.remove("hidden");
        });
    });
}

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
