# Demo PlaceToPay

Aplicación construida en laravel 5.5 y Vue 2.x para simular compras online mediante integración con la pasarela PlaceToPlay

# Que hace exactamente
La aplicación cuenta con dos vistas:
- En la primera se permite al usuario conocer el estado de sus pagos mediante una tabla
- En la segunda se permite iniciar un proceso de pago mediante un formulario

1. Cuando el usuario confirme la compra mediante la segunda vista se consume un servicio de PlaceToPay que registra el proceso de pago, a este se envían los datos que se tengan sobre el cliente, expiración (Fecha hasta que puede ser realizado el pago), URL de retorno y demás valores definidos posteriormente.
2. Si la solicitud es correcta, el servicio retorna un identificador para la petición y una URL de procesamiento, la cual es utilizada para re direccionar al cliente y continuar con el proceso. En caso contrario, se indica el motivo de rechazo de la petición.
3. Una vez el usuario realiza el proceso de pago en la plataforma de PlaceToPay y hace clic en “Regresar al comercio”, éste es enviado a la URL de retorno especificada en el punto 1 que lo lleva  a la primera vista.
4. Cuando el usuario está de nuevo en la aplicación se toma el identificador enviado por PlaceToPlay en la URL para obtener el estado del pago y notificarle al usuario.


# Como correr la aplicación 

Descargar el proyecto y por línea de comandos ejecutar lo siguiente
```
composer install
```
```
npm install
```

Dentro del proyecto existe un command que se ejecuta cada 12 minutos con la funcionalidad de identificar el estado de pagos que se encuentran pendientes o que no se conoce el estado.
Por lo anterio, al momento de desplegar la aplicación en un servidor no olvidar habilitar la ejecución de los commands


# Links de documentación utilizada

https://vuejs.org/v2/guide/

https://laravel.com/docs/5.5/

https://github.com/dnetix/redirection

https://dev.placetopay.com/web/redirection/
