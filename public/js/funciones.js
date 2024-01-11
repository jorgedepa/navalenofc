var days = ['DO', 'LU', 'MA', 'MI', 'JU', 'VI', 'SA'];
var date = new Date();
var today = days[date.getDay()];
var p = document.getElementById('days');
p.innerHTML = p.innerHTML.replace(today, '<span class="today">' + today + '</span>');

// Función para actualizar la hora
function updateTime() {
    var date = new Date();
    var time = document.getElementById('time');
    var hours = date.getHours().toString().padStart(2, '0');
    var minutes = date.getMinutes().toString().padStart(2, '0');
    var seconds = date.getSeconds().toString().padStart(2, '0');
    time.innerHTML = hours + ":" + minutes + ":" + seconds;
}

// Actualizar la hora cada segundo
setInterval(updateTime, 1000);
