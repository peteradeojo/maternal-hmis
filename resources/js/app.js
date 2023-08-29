import "./bootstrap";

async function fetchToken() {
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

class DataTable {
    element;
    columns;
    data;

    constructor(selector, options = {}) {
        this.element = document.querySelector(selector);
        this.columns = options.columns ?? undefined;
    }

    async load({ url }) {
        try {
            const token = await fetchToken();

            const response = await fetch(url, {
                headers: {
                    Accept: "application/json",
                    Authorization: `Bearer ${token}`,
                },
                // credentials: 'include'
            });
            const data = await response.json();

            this.data = data;

            await this.draw();
            return data;
        } catch (err) {
            console.log(err);
            alert(err.message);
        }
    }

    parseHeaders() {
        if (this.columns != undefined) return this.columns;

        const headers = [];
        this.element.querySelectorAll("thead th").forEach((th) => {
            headers.push(th.innerText);
        });
        this.headers = headers;
    }

    async draw() {
        await this.render(this.data);
        await this.renderFooter(this.data);
    }

    async render(data) {
        const { data: resultSet } = data;

        resultSet.forEach((row) => {
            const tr = document.createElement("tr");
            this.columns.forEach((header) => {
                if (typeof header === "function") {
                    const td = document.createElement("td");
                    td.innerHTML = header(row);
                    tr.appendChild(td);
                    return;
                }

                if (header.includes(".")) {
                    const [parent, child] = header.split(".");
                    const td = document.createElement("td");
                    td.innerText = row[parent][child];
                    tr.appendChild(td);
                    return;
                }

                const td = document.createElement("td");
                td.innerText = row[header];
                tr.appendChild(td);
            });

            this.element.querySelector("tbody").appendChild(tr);
        });
    }

    get columnCount() {
        return this.element.querySelectorAll("thead tr > *").length;
    }

    async renderFooter(data) {
        const {
            currentPage,
            links,
            per_page,
            next_page_url,
            prev_page_url,
            from,
            to,
            last_page,
            total,
        } = data;

        const previousLink = links[0];
        const lastLink = links[links.length - 1];
        const realLinks = links.slice(1, links.length - 1);

        const tfoot = document.createElement("tfoot");
        const tr = document.createElement("tr");

        tfoot.appendChild(tr);

        let td = `<td colspan='${this.columnCount}'>
            ${
                prev_page_url
                    ? `<a href='${prev_page_url}'>Previous</a>`
                    : "Previous"
            }`;

        realLinks
            .slice(
                (currentPage - 5 < 1 ? 1 : currentPage - 5,
                currentPage + 5 > last_page ? last_page : currentPage + 5)
            )
            .forEach((link) => {
                // console.log(link);
                td += `<a href='${link.url}'>${link.label}</a>`;
            });

        tr.innerHTML =
            td +
            `${next_page_url ? `<a href='${next_page_url}'>Next</a>` : "Next"}`;

        if (this.element.querySelector("tfoot")) {
            this.element.querySelector("tfoot").remove();
        }

        this.element.appendChild(tfoot);
    }
}

export default DataTable;
