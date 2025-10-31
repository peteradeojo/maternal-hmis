// document.addEventListener("DOMContentLoaded", () => {
//     document
//         .querySelectorAll(".foldable .foldable-header")
//         .forEach((button) => {
//             button.addEventListener("click", (e) => {
//                 const elem = e.target.closest(".foldable");
//                 const body = elem.querySelector(".foldable-body");
//                 body.classList.toggle("unfolded");
//             });
//         });

//     document.querySelectorAll(".modal").forEach((modal) => {
//         // modal.addEventListener("click", (e) => {
//         //     if (e.target.classList.contains("modal")) {
//         //         e.target.classList.add("hide");
//         //     }
//         // });

//         modal.addEventListener("mousedown", (e) => {
//             // Check if the click is directly on the modal backdrop
//             if (e.target === modal) {
//                 modal.classList.add("hide");
//             }
//         });
//     });

//     document.querySelectorAll(".modal-trigger").forEach((trigger) => {
//         trigger.addEventListener("click", (e) => {
//             const { target } = e.target.dataset;
//             const modal = document.querySelector(target);
//             modal?.classList.remove("hide");
//         });
//     });

//     // document.querySelectorAll(".tabs").forEach((tabspace) => {
//     //     const tabList = document.querySelector(tabspace.dataset.list);
//     //     // tabList.querySelectorAll('.tab').forEach(t => t.classList.add('hide'));

//     //     tabspace.querySelectorAll(".tab-item").forEach((btn) => {
//     //         btn.addEventListener("click", function (e) {
//     //             tabspace
//     //                 .querySelectorAll(".tab-item")
//     //                 .forEach((t) => t.classList.remove("active"));

//     //             tabList
//     //                 .querySelectorAll(".tab")
//     //                 .forEach((t) => t.classList.add("hide"));
//     //             tabList
//     //                 .querySelector(`.tab${btn.dataset.target}`)
//     //                 ?.classList.remove("hide");
//     //             btn.classList.add("active");
//     //         });
//     //     });
//     // });
// });

function initTab(el) {
    if (!el) return;
    const tabNav = el.querySelector("nav");

    const tabList = tabNav.querySelectorAll("a");

    const tabContentList = el.querySelectorAll(
        el.getAttribute("data-tablist") + " > .tab"
    );

    tabContentList.forEach((e, i) => {
        i > 0 && e.classList.add("hidden");
    });

    tabList?.forEach((element, i) => {
        element.addEventListener("click", (e) => {
            e.preventDefault();
            e.stopPropagation();
            tabContentList.forEach((elj) => elj.classList.add("hidden"));

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

function asyncForm(form, route, callback = (e, data) => { }) {
    $(form).on("submit", (e) => {
        e.preventDefault();
        fetch(route, {
            method: "POST",
            body: new FormData(e.currentTarget),
            headers: {
                Accept: "application/json",
            },
        })
            .then((res) => {
                callback(e.currentTarget, res);
            })
            .catch((err) => {
                console.error(err);
            });
    });
}

function useGlobalModal(callback) {
    $("#global-overlay").removeClass("hidden");
    $("#global-modal").removeClass("translate-x-full");

    callback($("#global-modal"));
}

$('#closeGlobalModal, #global-overlay').on('click', function () {
    removeGlobalModal();
});

function removeGlobalModal() {
    $("#global-modal").addClass("translate-x-full");
    setTimeout(() => $("#global-overlay").addClass("hidden"), 300);
}

/**
 * @param {{message: string; bg: string; [key: string]: any}} data
 */
function displayNotification(data) {
    if (Notification.permission === 'granted' && ['both', 'desktop'].includes(data.options.mode)) {
        const n = new Notification(data.title || 'New Notification', {
            body: data.message,
        });
    }

    if (!['both', 'in-app'].includes(data.options.mode)) {
        return;
    }

    const el = document.createElement(`div`);
    el.textContent = data.message;
    el.classList.add(...(data.bg), 'app-notification');

    document.querySelector("#notifications").appendChild(el);

    if (data.close_modal) {
        removeGlobalModal();
    }

    setTimeout(() => {
        el.classList.add("fade-out");
    }, 3000);

    setTimeout(() => {
        el.remove();
    }, 3300);
}
