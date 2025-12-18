<?php
class Cronometro
{
    private $inicio;
    private $tiempo;
    private $corriendo;

    public function __construct()
    {
        $this->inicio = 0;
        $this->corriendo = false;
        $this->tiempo = 0;
    }

    public function arrancar()
    {
        $this->inicio = microtime(true);
        $this->corriendo = true;
    }

    public function parar()
    {
        if ($this->corriendo == true) {
            $fin = microtime(true);
            $this->tiempo = $fin - $this->inicio;
            $this->corriendo = false;
        }
    }

    public function getTiempo(){
        return $this->tiempo;
    }
    public function mostrar()
    {
        if ($this->corriendo == true) {
            $this->tiempo = microtime(true) - $this->inicio;
        }
        $totalSegundos = $this->tiempo;

        $minutos = floor($totalSegundos / 60);
        $segundos = floor($totalSegundos % 60);
        $decimas = floor(($totalSegundos - floor($totalSegundos)) * 10);
        return sprintf("%02d:%02d.%01d", $minutos, $segundos, $decimas);
    }
}
?>