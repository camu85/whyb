<?php 
  class users_controller {

    static function form() {
      global $firephp;
      $usuario = "";
      $admin = 0;
      $titulo = "WHYB formulario";
      $header = header::construye($usuario, $admin);
      $body = formulario::construye();
      $footer = footer::construye();
      $paginaDetalle = new plantillaPagina($titulo, $header, $body, $footer);
      $pagina = $paginaDetalle->mostrar();
      //$firephp->log($paco, 'Mensaje');
      return $pagina;
    }
  }
?>