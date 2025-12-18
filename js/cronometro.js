class Cronometro {
    #tiempo;
    #inicio;
    #corriendo;
    constructor() {
        this.#tiempo = 0;
        this.#inicio = null;
        this.#corriendo = null;
    }

    arrancar() {
        if(this.#corriendo == null && this.#tiempo == 0) {
            try {
                this.#inicio = Temporal.Now.instant();
                console.log("Usando Temporal:", this.#inicio)
            } catch(error) {
                this.#inicio = new Date();
                console.warn("Temporal no disponible. Usando Date:", this.#inicio);
            }
            this.#corriendo = setInterval(this.#actualizar.bind(this), 100);
        }
    }

    #actualizar() {
        let ahora;
        let diferencia;

        try {
            ahora = Temporal.Now.instant();
            diferencia = ahora.epochMilliseconds - this.#inicio.epochMilliseconds;
        } catch (error) {
            ahora = new Date();
            diferencia = ahora.getTime() - this.#inicio.getTime();
        }
        this.#tiempo = (diferencia / 1000).toFixed(1);
        this.mostrar();
        //console.log(`Tiempo transcurrido: ${this.#tiempo} s`);
        

    }

    parar() {
        if (this.#corriendo) {
            clearInterval(this.#corriendo);
            this.#corriendo = null;
            //console.log("Cronómetro detenido.");
        }
    }

    reiniciar() {
        if (this.#corriendo) {
            clearInterval(this.#corriendo);
            this.#corriendo = null;
        }
        this.#tiempo = 0;
        this.mostrar();
        //console.log("Cronómetro reiniciado.");
    }

    mostrar() {
        // Descomponer tiempo en minutos, segundos y décimas
        const minutos = parseInt(this.#tiempo / 60);
        const segundos = parseInt((this.#tiempo % 60));
        const decimas = parseInt((this.#tiempo * 10) % 10);

        // Formato mm:ss.s
        const formato = `${String(minutos).padStart(2, '0')}:${String(segundos).padStart(2, '0')}.${decimas}`;

        // Buscar el primer <p> dentro de <main>
        const parrafo = document.querySelector('main p');
        if (parrafo) {
            parrafo.textContent = formato;
        } else {
            console.warn("No se encontró un párrafo dentro de <main>.");
        }

        //console.log("Tiempo mostrado:", formato);
    }
}