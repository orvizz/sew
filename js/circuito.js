class Circuito {

    constructor() {
        this.#comprobarApiFile();
    }

    #comprobarApiFile() {
        const main = document.querySelector("main");
        if (window.File && window.FileReader && window.FileList && window.Blob) {
            //let p = document.createElement("p");
            //p.textContent = "Soporta el API File.";
            //main.appendChild(p);
            return true;
        }
        else {
            let p = document.createElement("p");
            p.textContent = "El API File no es soportado en este navegador.";
            main.appendChild(p);
            return false;
        }
    }

    leerArchivoHTML(files) {
        var archivo = files[0];

        if (archivo.type === "text/html") {
            var lector = new FileReader();

            lector.onload = function (e) {
                const contenido = e.target.result;

                const parser = new DOMParser();
                const doc = parser.parseFromString(contenido, "text/html");

                const bodyContent = doc.body.innerHTML;

                //document.querySelector("main").innerHTML += bodyContent;

                
                const main = document.querySelector('main');
                main.insertAdjacentHTML("beforeend", bodyContent);
            };

            lector.readAsText(archivo);
        } else {
            console.log("El archivo no es HTML.");
        }
    }
}

class CargadorSVG {

    constructor() {
        
    }

    leerArchivoSVG(files) {
        const archivo = files[0];
        if (archivo && archivo.type === 'image/svg+xml') {
            const lector = new FileReader();
            lector.onload = (e) => this.#insertarSVG(e.target.result);
            lector.readAsText(archivo);
        } else {
            alert('Selecciona un archivo SVG válido.');
        }
    }

    #insertarSVG(contenidoTexto) {
        const parser = new DOMParser();
        const documentoSVG = parser.parseFromString(contenidoTexto, 'image/svg+xml');
        const elementoSVG = documentoSVG.documentElement;
        const main = document.querySelector('main');
        main.appendChild(elementoSVG);
    }
}


class CargadorKML {

    constructor() {
        const contenedorMapa = document.querySelector("main > div");

        if (!contenedorMapa) {
            console.error("No se encontró un <div> dentro de <main> para mostrar el mapa.");
            return;
        }

        // Creamos el mapa centrado temporalmente (se actualizará al cargar el KML)
        this.map = new google.maps.Map(contenedorMapa, {
            center: { lat: 52.069167, lng: -1.022222 }, // Primera coordenada por defecto
            zoom: 14,
            mapTypeId: "satellite",
        });

        this.polyline = null; // Guardaremos la polilínea aquí
    }
    leerArchivoKML(files) {
        const archivo = files[0];
        if (!archivo) {
            alert('No se ha seleccionado ningún archivo.');
            return;
        }
        if (archivo.name.toLowerCase().endsWith(".kml")) {
            const lector = new FileReader();
            lector.onload = (e) => {
                //this.initMap();
                this.#insertarCapaKML(e.target.result);
            };
            lector.readAsText(archivo);

        } else {
            alert('Selecciona un archivo KML válido.');
        }
    }

    #insertarCapaKML(contenidoTexto) {
        if (!this.map) {
            alert("El mapa no está inicializado.");
            return;
        }

        // Parsear el contenido KML como XML
        const parser = new DOMParser();
        const kmlDoc = parser.parseFromString(contenidoTexto, "application/xml");

        const coordsText = kmlDoc.querySelector("coordinates")?.textContent.trim();
        if (!coordsText) {
            alert("No se encontraron coordenadas en el archivo KML.");
            return;
        }

        // Convertir el texto de coordenadas en un array de objetos {lat, lng}
        const coords = coordsText.split(/\s+/).map(pair => {
            const [lng, lat] = pair.split(",").map(Number);
            return { lat, lng };
        });

        // Si ya hay una polilínea, eliminarla
        if (this.polyline) {
            this.polyline.setMap(null);
        }
        if (this.originMarker) {
            this.originMarker.setMap(null);
        }

        // Crear la nueva polilínea
        this.polyline = new google.maps.Polyline({
            path: coords,
            geodesic: true,
            strokeColor: "#FF0000",
            strokeOpacity: 1.0,
            strokeWeight: 3,
            map: this.map,
        });

        this.originMarker = new google.maps.Marker({
            position: coords[0],
            map: this.map,
            title: "Inicio del circuito",
        });


        // Ajustar el zoom al circuito
        const bounds = new google.maps.LatLngBounds();
        coords.forEach(c => bounds.extend(c));
        this.map.fitBounds(bounds);
    }

}