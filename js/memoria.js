class Memoria {
    #tablero_bloqueado;
    #primera_carta;
    #segunda_carta;
    #cronometro;
    constructor() {
        this.#tablero_bloqueado = true;
        this.#primera_carta = null;
        this.#segunda_carta = null;
        this.#barajarCartas();
        this.#tablero_bloqueado = false;
        this.#addEventListeners();
        this.#cronometro = new Cronometro();
        this.#cronometro.arrancar();
    }

    voltearCarta(card) {
        if(this.#tablero_bloqueado) return;
        if(card.getAttribute('data-estado') == "revelada") return;
        if(card.getAttribute('data-estado') == "volteada") return;
        card.setAttribute('data-estado', 'volteada');
        if(this.#primera_carta == null) this.#primera_carta = card;
        else {
            this.#segunda_carta = card;
            this.#comprobarPareja();
        }
    }

    #addEventListeners() {
        let cards = document.querySelectorAll('article');
        cards.forEach(card => {
            card.onclick = this.voltearCarta.bind(this, card);
        })
    }

    #barajarCartas() {
        const contenedor = document.querySelector('main');
        let cartas = Array.from(contenedor.querySelectorAll('article'));
        for (let i = cartas.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [cartas[i], cartas[j]] = [cartas[j], cartas[i]];
        }
        cartas.forEach(carta => contenedor.appendChild(carta));
    }

    #reiniciarAtributos() {
        this.#tablero_bloqueado = true;
        this.#primera_carta = null;
        this.#segunda_carta = null;
    }

    #deshabilitarCartas() {
        this.#primera_carta.setAttribute('data-estado', 'revelada');
        this.#segunda_carta.setAttribute('data-estado', 'revelada');
        this.#reiniciarAtributos();
        if(this.#comprobarJuego()) {
            console.log("Juego completado");
        }
        else { 
            this.#tablero_bloqueado = false;
        }
    }

    #comprobarJuego() {
        let cartas = document.querySelectorAll("main > article");
        let allRevealed = true;
        cartas.forEach((carta) => {
            if (carta.getAttribute('data-estado') != "revelada") allRevealed = false;
        })
        if(allRevealed) {
            this.#cronometro.parar();
        }
        return allRevealed
    }

    #cubrirCartas() {
        this.#tablero_bloqueado = true;
        setTimeout(() => {
            this.#primera_carta.setAttribute('data-estado', '')
            this.#segunda_carta.setAttribute('data-estado', '')
            this.#reiniciarAtributos();
            this.#tablero_bloqueado = false;
        }, 1500)
        
    }

    #comprobarPareja() {
        const altPrimera = this.#primera_carta.querySelector('img').getAttribute("alt");
        const altSegunda = this.#segunda_carta.querySelector('img').getAttribute("alt");

        altPrimera == altSegunda ? this.#deshabilitarCartas() : this.#cubrirCartas();
    }
}