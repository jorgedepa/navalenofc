var dias = ['DO', 'LU', 'MA', 'MI', 'JU', 'VI', 'SA'];
var fechaActual = new Date();
var diaSemanaActual = dias[fechaActual.getDay()];
var p = document.getElementById('diasSemana');
p.innerHTML = p.innerHTML.replace(diaSemanaActual, '<span class="hoy">' + diaSemanaActual + '</span>');

// Función para actualizar la hora
function updateTime() {
    var fechaActual = new Date();
    var reloj = document.getElementById('reloj');
    var horas = fechaActual.getHours().toString().padStart(2, '0');
    var minutos = fechaActual.getMinutes().toString().padStart(2, '0');
    var segundos = fechaActual.getSeconds().toString().padStart(2, '0');
    reloj.innerHTML = horas + ":" + minutos + ":" + segundos;
}

// Actualizar la hora cada segundo
setInterval(updateTime, 1000);
