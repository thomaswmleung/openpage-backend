//            $(document).ready(function () {



if ($('body').attr('lang') == "es") {
    jQuery.extend(jQuery.validator.messages, {
        required: "Este campo es requerido.",
        remote: "Por favor arregla este campo.",
        email: "Por favor, introduce una dirección de correo electrónico válida.",
        url: "Por favor introduzca un URL válido.",
        date: "Por favor introduzca una fecha valida.",
        dateISO: "Ingrese una fecha válida (ISO).",
        number: "Por favor ingrese un número valido.",
        digits: "Por favor ingrese solo dígitos.",
        creditcard: "Por favor, introduzca un número de tarjeta de crédito válida.",
        equalTo: "Por favor, introduzca el mismo valor de nuevo.",
        accept: "Introduzca un valor con una extensión válida.",
        maxlength: jQuery.validator.format("No ingrese más de {0} caracteres."),
        minlength: jQuery.validator.format("Introduzca al menos {0} caracteres."),
        rangelength: jQuery.validator.format("Introduzca un valor entre {0} y {1} caracteres."),
        range: jQuery.validator.format("Introduzca un valor entre {0} y {1}."),
        max: jQuery.validator.format("Ingrese un valor menor o igual a {0}."),
        min: jQuery.validator.format("Ingrese un valor mayor o igual a {0}.")
    });
}


//            });