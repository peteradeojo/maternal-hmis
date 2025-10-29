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
