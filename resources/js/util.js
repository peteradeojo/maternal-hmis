document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".foldable .foldable-header").forEach((button) => {
        button.addEventListener("click", (e) => {
            const elem = e.target.closest(".foldable");
            elem.querySelector(".foldable-body").classList.toggle("unfolded");
        });
    });

    document.querySelectorAll('.modal').forEach((modal) => {
        modal.addEventListener('click', (e) => {
            if(e.target.classList.contains('modal')) {
                e.target.classList.add('hide');
            }
        });
    });

    document.querySelectorAll('.modal-trigger').forEach((trigger) => {
        trigger.addEventListener('click', (e) => {
            const {target} = e.target.dataset;
            const modal = document.querySelector(target);
            modal?.classList.remove('hide');
        });
    });
});
