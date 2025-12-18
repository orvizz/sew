class Noticias {
    constructor() {
        this.apiKey = "swjjur5k1RZhVvdlYAq9Jl0L4VwiNEPGVcSBMMjf";
        this.busqueda = "MotoGP | Silverstone";
        this.url = `https://api.thenewsapi.com/v1/news/all?api_token=${this.apiKey}`;    }

    buscar() {
        fetch(this.url + "&search=" + this.busqueda + "&language=es&limit=5")
            .then(response => response.json())
            .then(data => this.procesarInformacion(data))
            .catch(error => console.error("Error al obtener las noticias:", error));
    }

    procesarInformacion(data) {
        console.log("Datos recibidos de la API de noticias:", data);
        const section = document.querySelector("section");
        const h2 = document.createElement("h2");
        h2.textContent = "Noticias sobre MotoGP en Silverstone";
        section.appendChild(h2);   
        data.data.forEach(noticia => {
            const article = document.createElement("article");
            const titulo = document.createElement("h3");
            titulo.textContent = noticia.title;
            const descripcion = document.createElement("p");
            descripcion.textContent = noticia.snippet;
            const enlace = document.createElement("a");
            enlace.href = noticia.url;
            enlace.textContent = "Leer más";
            enlace.target = "_blank";
            const fuente = document.createElement("p");
            fuente.textContent = `Fuente: ${noticia.source}`;
            article.appendChild(titulo);
            article.appendChild(descripcion);
            article.appendChild(enlace);
            article.appendChild(fuente);
            section.appendChild(article);
        });
    }

    mockNoticias() {
        const mockData = {
            data: [
                {
                    title: "Gran Premio de Silverstone: Resumen de la carrera",
                    snippet: "El Gran Premio de Silverstone ofreció una emocionante carrera llena de adelantamientos y estrategias...",
                    url: "https://example.com/noticia1",
                    source: "MotoGP News"
                },
                {
                    title: "Entrevista con el ganador del GP de Silverstone",
                    snippet: "Hablamos con el piloto que se llevó la victoria en Silverstone sobre sus sensaciones y la carrera...",
                    url: "https://example.com/noticia2",
                    source: "Racing Today"
                }
            ]};
        this.procesarInformacion(mockData);
    }
}