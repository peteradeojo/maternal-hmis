document.addEventListener("DOMContentLoaded",()=>{document.querySelectorAll(".foldable .foldable-header").forEach(t=>{t.addEventListener("click",e=>{e.target.closest(".foldable").querySelector(".foldable-body").classList.toggle("unfolded")})}),document.querySelectorAll(".modal").forEach(t=>{t.addEventListener("click",e=>{e.target.classList.contains("modal")&&e.target.classList.add("hide")})}),document.querySelectorAll(".modal-trigger").forEach(t=>{t.addEventListener("click",e=>{const{target:d}=e.target.dataset,l=document.querySelector(d);l==null||l.classList.remove("hide")})})});