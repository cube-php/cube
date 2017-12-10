new Vue({
    el: '#login-app',
    data: {

    },
    methods:{

        login: function () {
            this.$validator.validateAll()
                .then((response) => {

                    if(!response) {
                        return;
                    }

                    this.$http.post(ajaxify('/log'))
                        .then(response => {

                        }, err => {

                        });
                });
        }
    }
});