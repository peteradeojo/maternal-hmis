import './app';

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

    const listener = (i) => {
        return (e) => {
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
        };
    };

    tabList?.forEach((element, i) => {
        element.addEventListener("click", listener(i));
    });
}

window.asyncForm = (form, route, callback = (e, data) => { }) => {
    $(form).on("submit", (e) => {
        e.preventDefault();
        axios.post(route, new FormData(e.currentTarget), {
            headers: {
                Accept: "application/json",
            }
        }).then((res) => {
            callback(e.currentTarget, res);
        }).catch((err) => {
            console.error(err.message);
            displayNotification({
                message: err.message,
                bg: ['bg-red-500', 'text-white'],
                options: {
                    mode: ['in-app']
                },
            });
        });
    });
}

window.MODAL_TITLE = ".modal-title";
window.MODAL_CONTENT = ".modal-body";
window.MODAL_BODY = ".modal-body";

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
    const { options, message, bg, meta } = data;

    if (Notification.permission === 'granted' && ['both', 'desktop'].includes(options.mode)) {
        const n = new Notification(data.title || 'New Notification', {
            body: message,
            icon: '/favicon.ico',
            requireInteraction: (options?.priority || 0) >= 3,
            data: meta,
        });

        n.addEventListener('click', function (e) {
            const { url } = this.data || {};
            if (url) {
                const link = document.createElement('a');
                link.href = url;
                link.target = "_blank";
                link.rel = "noreferrer noopener";
                link.click();
                n.close();
            }
        });

        setTimeout(() => n.close(), 30000);
    }

    if (!['both', 'in-app'].includes(options.mode)) {
        return;
    }

    const el = document.createElement(`div`);
    el.textContent = message;
    el.classList.add(...(bg || ['bg-green-400', 'text-white']), 'app-notification');

    document.querySelector("#notifications").appendChild(el);

    if (options.close_modal) {
        removeGlobalModal();
    }

    setTimeout(() => {
        el.classList.add("fade-out");
    }, Math.max(options.timeout, 5000));

    setTimeout(() => {
        el.remove();
    }, Math.max(options.timeout, 5300));
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

    $("#nav-burger").on('click', (e) => {
        $("#mobile-nav-list").toggleClass("hidden");
    });
});
