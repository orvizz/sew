class Carrusel {
    #busqueda;
    #actual;
    #maximo;
    #url;
    #fotosUrls;
    #fotosTitle;
    #interval;
    #carrousel;
    constructor(busqueda) {
        this.#busqueda = busqueda;
        this.#actual = 0;
        this.#maximo = 5;
        this.#url = "https://api.flickr.com/services/feeds/photos_public.gne";
        this.#fotosUrls = [];
        this.#fotosTitle = [];
        this.#interval = 3000; // 3 segundos
    }

    async getFotografias() {
        $.ajax({
            dataType: "jsonp",
            jsonp: "jsoncallback",
            jsonpCallback: "jsonFlickrFeed",
            url: this.#url,
            data: {
                tags: this.#busqueda,
                format: "json",
                tagmode: "all"
            },
            method: "GET",
            success: (data) => {
                this.#mostrarFotografias(data);
            },
            error: this.#manejarError.bind(this),
        });
    }
    #mostrarFotografias(respuesta) {
        console.log("Respuesta recibida de la API:", respuesta);
//        if (respuesta. === "ok") {
            const fotos = respuesta.items;
            fotos.forEach((foto, index) => {
                const fotoUrl = foto.media.m.replace("_m", "_z");
                const fotoTitle = foto.title;
                console.log(`Foto ${index + 1}: ${fotoTitle} - ${fotoUrl}`);
                this.#fotosUrls.push(fotoUrl);
                this.#fotosTitle.push(fotoTitle);

            });
            this.#cambiarFotografia();
            this.#carrousel = setInterval(this.#cambiarFotografia.bind(this), this.#interval);
//        } else {
            //console.error("Error en la respuesta de la API:", respuesta.message);
//        }
    }

    #cambiarFotografia() {
        const section = document.querySelector("article");
        section.innerHTML = "";
        const h2 = document.createElement("h2");
        h2.textContent = `Imágenes del circuito de Silverstone`;
        section.appendChild(h2);
        const img = document.createElement("img");
        img.src = this.#fotosUrls[this.#actual];
        img.alt = this.#fotosTitle[this.#actual];
        console.log("Mostrando imagen:", img.src);
        section.appendChild(img);
        this.#actual = (this.#actual + 1) % this.#maximo;
    }

    #manejarError(jqXHR, textStatus, errorThrown) {
        console.error("Error en la solicitud AJAX:", textStatus, errorThrown);
    }
}