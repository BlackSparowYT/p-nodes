document.addEventListener("DOMContentLoaded", function () {
    const servers = document.querySelectorAll(".card--server");

    console.log(servers)
    servers.forEach(server => {
        const serverUrl = server.getAttribute("data-url");
        checkServerStatus(server.querySelector('.status'), server, serverUrl);
    });

    function checkServerStatus(text, card, url) {
        console.log(text)
        console.log(card)
        const proxyUrl = '/files/components/proxy.php?url=' + encodeURIComponent(url);
        const xhr = new XMLHttpRequest();
        xhr.open("GET", proxyUrl, true);

        xhr.onload = function () {
            if (xhr.status >= 200 && xhr.status < 300) {
                card.classList.remove("unkown");
                card.classList.add("online");
                text.textContent = "Status: online";
            } else {
                card.classList.remove("unkown");
                card.classList.add("offline");
                text.textContent = "Status: offline";
            }
        };

        xhr.onerror = function () {
            console.error("Error making request to", url);
            card.classList.remove("unkown");
            card.classList.add("unkown");
            text.textContent = "Status: unkown";
        };

        xhr.send();
    }
});
