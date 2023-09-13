import "./bootstrap";

export async function fetchToken() {
    const res = await fetch("/sanctum/csrf-cookie", {
        headers: {
            Accept: "application/json",
        },
        credentials: "include",
    });

    const token = document.cookie
        .split("; ")
        .find((row) => row.startsWith("XSRF-TOKEN"))
        .split("=")[1];
    return token;
}

export function triggerAlert(status = "success", message) {
    const div = document.createElement("div");
    div.classList.add("alert", `alert-${status}`);
    div.innerText = message;

    document.querySelector("body").appendChild(div);

    setTimeout(() => {
        div.remove();
    }, 5000);
}
