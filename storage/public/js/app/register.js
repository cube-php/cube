new Vue({
    el: '#register-app',
    data: {
        requesting: false,
        registerError: null,
        isRegistered: false,
        username: null
    },
    methods: {
        register: function () {

            var _this = this;
            var formdata;

            this.$validator.validateAll()
                .then((result) => {

                    if(result) {
                        _this.requesting = true;
                        formdata = new FormData(_this.$refs.regform);

                        _this.$http.post(ajaxify('/register'), formdata)
                            .then((response) => {

                                console.log(response.body.content);

                                if(response.body.status === 'success') {
                                    var content = response.body.content;
                                    _this.username = content.user.username;
                                    _this.requesting = false;
                                    _this.isRegistered = true;
                                }

                                if(response.body.status === 'error') {

                                    var result = response.body.content.msg;
                                    _this.requesting = false;
                                    
                                    if(is_string(result)) {
                                        _this.registerError = result;
                                        return;
                                    }

                                    var first_err_name = Object.keys(result)[0];
                                    var error = result[first_err_name][0];
                                    _this.registerError = error;
                                }
                            });
                    }
                });
        }
    }
});