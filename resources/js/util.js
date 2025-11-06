window.initTab = function (el) {
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

window.asyncForm = (form, route, callback = (e, data) => { }) => {
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

window.MODAL_TITLE=".modal-title";
window.MODAL_CONTENT=".modal-body";
window.MODAL_BODY=".modal-body";

window.useGlobalModal = function (callback) {
    $("#global-overlay").removeClass("hidden");
    $("#global-modal").removeClass("translate-x-full");

    callback($("#global-modal"));
}

window.removeGlobalModal = function () {
    $("#global-modal").addClass("translate-x-full");
    setTimeout(() => $("#global-overlay").addClass("hidden"), 300);
}

$('#closeGlobalModal, #global-overlay').on('click', function () {
    removeGlobalModal();
});

/**
 * @param {{message: string; bg: string; [key: string]: any}} data
 */
window.displayNotification = function (data) {
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
    el.classList.add(...(data.bg || ['bg-green-400', 'text-white']), 'app-notification');

    document.querySelector("#notifications").appendChild(el);

    if (data.options.close_modal) {
        removeGlobalModal();
    }

    setTimeout(() => {
        el.classList.add("fade-out");
    }, 3000);

    setTimeout(() => {
        el.remove();
    }, 3300);
}

window.notifyError = function (message) {
    return displayNotification({
        message,
        bg: ['bg-red-500', 'text-white'],
        options: {
            mode: 'in-app',
        }
    })
}

window.notifySuccess = function (message) {
    return displayNotification({
        message,
        bg: ['bg-blue-400', 'text-white'],
        options: {
            mode: 'in-app',
        }
    })
}

window.notifyAction = function (message) {
    return displayNotification({
        message,
        bg: ['bg-green-500', 'text-white'],
        options: {
            mode: 'in-app',
        }
    })
}


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
            e.preventDefault();
            if (e.target.classList.contains("modal")) {
                // console.log("got click");
                e.target.classList.add("hide");
            }
        });

        // Prevent propagation for clicks inside the modal
        modal.querySelectorAll('.content').forEach((content) => {
            content.addEventListener('click', (e) => e.stopPropagation());
        });
    });

    document.querySelectorAll(".modal-trigger").forEach((trigger) => {
        trigger.addEventListener("click", (e) => {
            e.preventDefault();
            const { target } = e.target.dataset;
            const modal = document.querySelector(target);
            modal?.classList.remove("hide");
        });
    });

    $("#nav-burger").on('click', (e) => {
        $("#mobile-nav-list").toggleClass("hidden");
    });
});
