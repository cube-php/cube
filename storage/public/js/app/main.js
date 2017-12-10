Vue.use(VeeValidate);
var Validator = VeeValidate.Validator;

function ajaxify(path) {
    return BASE_URI + 'http/json' + path;
}

function is_string(str) {
    return typeof str === 'string';
}