import swal from 'sweetalert'

let e = null;
let code = null;
let icono = null;
export default function(error, networkOnLineError = true) {
    console.error(error);
    window.errorHandled = error;
    if (!error.__CANCEL__) {
        e = error;
        code = null;
        icono = "error";
        if (e.response) {
            console.error(e.response);
            code = e.response.status;
            e = error.response.data;

            window.errorHandled = error.response.data;
            if (typeof (e) == "string") {
                e = e.replace(/\n/g, '');
                e = e.replace(/<br\s\/>(.+)<br\s\/>/g, '');
                e = e.replace(/\n/g, '');
                e = e.replace(/"+/g, '');
                if (/\*e\*(.+)\*e\*/g.test(e)) {
                    e = e.match(/\*e\*(.+)\*e\*/g);
                    e = e[0];
                    e = e.replace(/\*e\*+/g, '');
                    icono = "error"
                } else if (/\*w\*(.+)\*w\*/g.test(e)) {
                    e = e.match(/\*w\*(.+)\*w\*/g);
                    e = e[0];
                    e = e.replace(/\*w\*+/g, '');
                    icono = "warning"
                } else if (/\*i\*(.+)\*i\*/g.test(e)) {
                    e = e.match(/\*i\*(.+)\*i\*/g);
                    e = e[0];
                    e = e.replace(/\*i\*/g, '');
                    icono = "info"
                } else if (/\*\*(.+)\*\*/g.test(e)) {
                    e = e.match(/\*\*(.+)\*\*/g);
                    e = e[0];
                    e = e.replace(/\*\*/g, '');
                    icono = "info"
                }
            }
        }
        if (typeof e == 'string') {
            swal({
                title: "Mensaje del sistema",
                text: e + (code ? "\n\n(Code:" + code + ")" : ''),
                icon: icono,
            });
        } else {
            if (errorHandled.name == 'Error' && errorHandled.message == 'Network Error') {
                if (!(navigator.onLine && !networkOnLineError)) {
                    swal({
                        title: errorHandled.message,
                        text: 'Revise su conexión de red' + '\nEstatus Actual : ' + (navigator.onLine == true ? 'Online' : 'Offline'),
                        icon: "error",
                    });
                }
            } else {
                swal({
                    title: "Mensaje del sistema",
                    text: "Error inesperado " + (code ? "(Code:" + code + ")" : ''),
                    icon: "error",
                });
            }
        }
    }
};
