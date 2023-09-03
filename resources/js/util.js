document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".foldable .foldable-header").forEach((button) => {
        button.addEventListener("click", (e) => {
            const elem = e.target.closest(".foldable");
            elem.querySelector(".foldable-body").classList.toggle("unfolded");
        });
    });
});
