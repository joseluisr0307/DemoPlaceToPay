<template>
    <div>


        <div class="col-md-10 floater">

            <div class="nvt-input-group third">
                <input type="text" id="viewSearch" class="form-control" placeholder="Buscar por estado" v-model="filterState">
            </div>
            <div class="nvt-button-group two-third right-align">

                <button type="button" class="btn  btn-success" v-on:click="showNewPayment" >Realizar nuevo pago</button>

            </div>
        </div>



        <div class="panel panel-default">
            Historial de pagos
            <div class="panel-body" v-show="payments.length > 0">

                <table class="table table-bordered table-striped">
                    <thead>

                    <tr>
                        <th>Fecha (UTC)</th>
                        <th>Descripción</th>
                        <th>Total</th>
                        <th>Moneda</th>
                        <th>Estado</th>
                    </tr>

                    </thead>
                    <tbody>
                    <tr v-for="payments, index in filter" >
                        <td>{{ payments.createdAt }}</td>
                        <td>{{ payments.description }}</td>
                        <td>{{ payments.currency }}</td>
                        <td>{{ payments.total }}</td>
                        <td v-if="payments.status === 'Aceptado'" bgcolor="#00ff55">{{ payments.status }}</td>
                        <td v-else-if="payments.status === 'Rechazado'" bgcolor="#ffd400">{{ payments.status }}</td>
                        <td v-else >{{ payments.status }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="panel-body" v-if="payments.length < 1">
                <h3> Cargando datos... </h3>
            </div>

            <NewPayment :modal="modalPayment"  @hideCreate="hideNewPayment"> </NewPayment>

        </div>
    </div>
</template>
<script>

    import PaymentNew from './PaymentNew.vue';

    export default {
        data: function() {
            return {
                reference : '',
                modalPayment: false,
                payment: {},
                payments: [],
                filterState: ""
            };
        },
        components: {
            'NewPayment': PaymentNew ,
        },
        mounted() {
            var app = this;

            app.reference = window.location.href.split('=').pop();

            if (app.reference !== "" && app.reference !== 'http://placetopay.local/') {
                axios
                    .post(
                        "/api/v1/pay/query/status",{reference : app.reference}
                    )
                    .then(function(resp) {
                        console.log(resp.data.data);
                        if('msg' in resp.data.data[0]){
                            alert(resp.data.data[0]['msg']);
                        }
                    })
                    .catch(function(resp) {
                        console.log(resp.data);
                        alert("Error al consultar estado del último pago");
                    });
            }

            axios
                .get(
                    "/api/v1/pay/records"
                )
                .then(function(resp) {
                    app.payments = resp.data.data;
                    console.log(resp.data.data);
                })
                .catch(function(resp) {
                    console.log(resp.response.data);
                    alert("No se pudo recuperar el historial de pagos");
                });

        },
        methods: {
            capitalize(str){
                if (typeof str !== 'string') return ''
                return str.charAt(0).toUpperCase() + str.slice(1)
            },
            showNewPayment(){
                this.modalPayment=true;
            },
            hideNewPayment(){

                this.modalPayment=false;
                this.loadRecords();
            },
            loadRecords(){
                var app = this;
                axios
                    .get(
                        "/api/v1/pay/records"
                    )
                    .then(function(resp) {
                        app.payments = resp.data.data;
                        console.log(resp.data.data);
                    })
                    .catch(function(resp) {
                        console.log(resp.response.data);
                        alert("No se pudo recuperar el historial de pagos");
                    });
            },
        },
        computed: {
            filter() {
                return this.payments.filter(payment => {
                    return payment.status.includes(this.capitalize(this.filterState));
                });
            },
        },
    }
</script>
<style scoped>
    select.selectpicker {
        float: right;
    }
    .panel.panel-default {
        margin-top: 10%;
    }
    .panel.panel-default {
        margin-top: 10%;
    }
    .nvt-input-group.third {
        width: 25%;
        float: left;
    }
    .nvt-button-group.two-third.right-align {
        float: right;
    }
    .col-md-10.floater {
        width: 100%;
        float: inherit;
    }
</style>
