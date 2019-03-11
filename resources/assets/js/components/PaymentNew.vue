<template>
    <div class="modal fade" tabindex="-1" role="dialog" id="create_model"><!--  ojo0000000000  -->
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">Información del pago</h4> <!--  ojo0000000000  -->
                </div>
                <div class="modal-body">


                    <div class="form-group">


                        <div class="panel panel-default">

                            <div class="panel-body">
                                <form v-on:submit="savePayment()">

                                    <div class="row">
                                        <div class="col-xs-12 form-group">
                                            <label class="control-label">Nombre completo del comprador.</label>
                                            <input type="text" v-model="payment.name" class="form-control" required autofocus>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12 form-group">
                                            <label class="control-label">Email del comprador.</label>
                                            <input type="email" v-model="payment.email" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12 form-group">
                                            <label class="control-label">Teléfono del comprador.</label>
                                            <input type="number" v-model="payment.phone" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12 form-group">
                                            <label class="control-label">Dirección del comprador.</label>
                                            <input type="text" v-model="payment.address" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12 form-group">
                                            <label class="control-label">valor pagar.</label>
                                            <input type="number" v-model="paymentSubscription.payment.amount.total" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12 form-group">
                                            <label class="control-label">Moneda de pago.</label>
                                            <select v-model="paymentSubscription.payment.amount.currency" required>
                                                <option disabled value="">Selecciona una opción</option>
                                                <option value="COP">Peso colombiano</option>
                                                <option value="USD">Dólar estadounidense</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12 form-group">
                                            <button class="btn btn-success">Iniciar Pago</button>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-default" @click="hideCreate($event)">Cancelar</button><!--  ojo0000000000  -->

                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</template>

<script>
    export default {
        props: {
            modal: {
                type: Boolean,
                required: false,
                default: false
            }
        },
        data: function() {
            return {
                showView: false,
                payment: {
                    name : "",
                    email : "",
                    phone : "",
                    address : "",
                },

                paymentSubscription : {
                    payment : {
                        reference : "",
                        description : "Testing payment",
                        amount : {
                            currency : "",
                            total : ""
                        },
                    },
                    expiration: "",
                    returnUrl: "",
                    ipAddress : "",
                    userAgent : ""
                },

            };
        },
        methods:{

            hideCreate(evnt){
                evnt.preventDefault()
                this.$emit('hideCreate')
            },
            savePayment() {
                event.preventDefault();
                var app = this;
                axios
                    .post("/api/v1/pay/subscription", {data : app.paymentSubscription})
                    .then(function(resp) {

                        app.payment = {
                                name : "",
                                email : "",
                                phone : "",
                                address : "",
                        };

                        /*paymentSubscription = {
                            payment: {
                                reference: "",
                                description: "Testing payment",
                                amount : {
                                    currency : "",
                                    total : ""
                                },
                            },
                            expiration: "",
                            returnUrl: "",
                            ipAddress: "",
                            userAgent: ""
                        };
                        */
                        console.log(resp.data);
                        alert("Registro inicial exitoso");
                        if('msg' in resp.data.data[0]){
                            alert(resp.data.data[0]['msg']);
                        }
                        if('url' in resp.data.data[0]){
                            location.href=resp.data.data[0]['url'];
                        }


                    })
                    .catch(function(resp) {
                        console.log(resp);
                        alert('Algo salió mal, intenta de nuevo');
                    });

            },
        },
        watch: {
            showView: function(val) {
                var app = this;
                if (app.showView == true) {
                    app. paymentSubscription.userAgent = navigator.userAgent;
                    $.getJSON('https://ipinfo.io', function(data){
                        app. paymentSubscription.ipAddress = data.ip;
                    });
                    $("#create_model").modal("show");
                } else {
                    $("#create_model").modal("hide");
                }
            },

            modal: function(val){

                this.showView = val;

            }

        }
    }
</script>

<style scoped>

</style>