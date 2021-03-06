<?php
	class modeloUsuario {

		//Guarda usuario
		static function alta($request){
			global $firephp;
			//Comprobar si los campos obligatorios estan completados
			$avisos = self::verificacion($request);
			if ($avisos != "")  // se ha producido algún error en la verificación
	  			throw new LogicException($avisos);
    		else {
    			$dob = $request->get('selYear')."-".$request->get('selMonth')."-".$request->get('selDay');
      			$conexion = accesoBBDD::abreConexionBD();

      			if (!$conexion)
      				die('Error de conexión (' . $conexion->connect_errno . ') ' . $conexion->connect_error);

      			$consulta = "INSERT INTO users (rolId, countryId, nickname, password, email, name, birthdate, sex)
	                 		VALUES (3, '".$request->get('selCountries')."', '".$request->get('txtUserName')."', '".$request->get('txtPass')."',
                     '".$request->get('txtMail')."', '".$request->get('txtName')."', '$dob', '".$sex = $request->get('sex')."')";
				
				$resultado = $conexion->query($consulta);
				//$firephp->log($resultado, 'nuevo usuario');

				if ($resultado) {
			      AccesoBBDD::cierraConexionBD($conexion);
		        } 
		        else {
		        	switch ($conexion->errno) {
		            	case 1062:
		            	$mensajeError = "Nombre de usuario ya existente";
		            	break;
		        	}
					AccesoBBDD::cierraConexionBD($conexion);
				 	throw new Exception("Error: $mensajeError");
		        }
		        return $resultado;
      		}
		}


		//Comprueba usuario
		static function logUser($request){
			global $firephp;
      		$conexion = accesoBBDD::abreConexionBD();

      		if (!$conexion)
      			die('Error de conexión (' . $conexion->connect_errno . ') ' . $conexion->connect_error);
      		else{
	      		$consulta = "SELECT userId, password FROM users WHERE nickname = '".$request->get('txtUserNameReg')."'";
	      		if ($resultado = $conexion->query($consulta)) {
	        		if($fila = $resultado->fetch_object()) { 
	          			$user = new Usuario($fila->userId, null, null, null, $fila->password, null, null, null, null, null);
	        		}
	        		else
	      				$user = '';

	        		$resultado->free();
	      		}
	      		else
	      			$user = '';
	      		
	      		AccesoBBDD::cierraConexionBD($conexion);
		 		return $user;
	    	}
	  	}

		//Comprueba los campos introducidos en el formulario
		static function verificacion($request){
			$avisos = "";
		    if ($request->get('txtUserName') == "")
			  $avisos .= "- El campo usuario no puede estar vacío" . "<br /> \n";
			if ($request->get('txtPass') == "")
			  $avisos .= "- El campo contraseña no puede estar vacío" . "<br /> \n";
			if ($request->get('txtPassRep') == "")
			  $avisos .= "- El campo repetir contraseña no puede estar vacío" . "<br /> \n";
			if ($request->get('txtPass') != "" && $request->get('txtPassRep') != ""){
				if ($request->get('txtPass') != $request->get('txtPassRep'))
			  		$avisos .= "- Los contraseñas no coinciden!" . "<br /> \n";
	  		}
	  		if ($request->get('txtMail') == "")
			  $avisos .= "- El campo email no puede estar vacío" . "<br /> \n";

		    return $avisos;	  
		}

		//Recuperar datos usuario
		static function userData($userId){
			global $firephp;
      		$conexion = accesoBBDD::abreConexionBD();

	      	$consulta = "SELECT userId, countryId, nickname, email, name, birthdate, sex FROM users WHERE userId = $userId";
	      	if ($resultado = $conexion->query($consulta)) {
	        	if($fila = $resultado->fetch_object()) { 
	        		$userData = new Usuario($fila->userId, null, $fila->countryId, $fila->nickname, null, $fila->email, null, $fila->name, $fila->birthdate, $fila->sex);
        		}	
        		$resultado->free();
      		}
      		
      		AccesoBBDD::cierraConexionBD($conexion);
	 		return $userData;
	    	
	  	}

	  	//Modificar datos usuario
		static function modifyuser($userId, $name, $dateOfBirth, $sex, $countryId){
			global $firephp;
      		$conexion = accesoBBDD::abreConexionBD();

	      	$consulta = "UPDATE users SET name = '$name', birthdate = '$dateOfBirth', sex = '$sex', countryId = '$countryId' WHERE userId=$userId;";
      		$firephp->log($consulta, 'Mensaje');
	      	$resultado = $conexion->query($consulta);

      		AccesoBBDD::cierraConexionBD($conexion);
	 		
	 		return $resultado;
	  	}

	  	//Borrar usuario
		static function deleteUser($userId){
			global $firephp;
			$cont = 0;
			$sitios = array();

      		$conexion = accesoBBDD::abreConexionBD();

      		//Recupera si existen el Id de los lugares que ha visitado el usuario 
      		$consulta = "SELECT placeId FROM placesvisited WHERE userId = $userId and isUnesco = 0";
      		$firephp->log($consulta, 'Query consulta sitios propios');

      		if ($resultado = $conexion->query($consulta)) {
				while ($fila = $resultado->fetch_object()) {	
			        $sitios[] = $fila->placeId;
		    	}
		    }
		    $firephp->log($sitios, 'Resultado sitios propios');

		    //Borra todos los registros de la tabla placesvisited del usuario
		    $consulta = "DELETE FROM placesvisited WHERE userId = $userId";
		    $conexion->query($consulta);

		    //Borra en caso de existir los lugares credos por el usuario
		    if (count($sitios) > 0){
		    	$consulta = "DELETE FROM places WHERE placeId IN (";
		    	foreach ($sitios as $userPlace) {
		    		$consulta .= "$userPlace,";
		    	}
		    	$consulta = substr($consulta, 0, -1);
		    	$consulta .=");";
		    	$firephp->log($consulta, 'Query borrar sitios propios');
		    	$conexion->query($consulta);
		    } 

			//Borra el usuario de la tabla users
		    $consulta = "DELETE FROM users WHERE userId = $userId";
		    $conexion->query($consulta);

      		AccesoBBDD::cierraConexionBD($conexion);
	 		
	 		return $resultado;
	  	}
	}
?>