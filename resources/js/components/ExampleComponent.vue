<template>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Get Neo Stats</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-5">
                                <label for="" class="label-control">Start Date</label>
                                <input type="date" id="start" class="form-control" v-model="item.start">
                            </div>
                            <div class="col-sm-5">
                                <label for="" class="label-control">Start Date</label>
                                <input type="date" id="end" class="form-control" v-model="item.end">
                            </div>
                            <div class="col-sm-2">
                                <label for="" class="label-control w-100">&nbsp;</label>
                                <button type="button" class="btn btn-success" @click="get">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-3" v-if="responsed">
                    <div class="card-header">Neo Stats</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-7 text-capitalize" v-for="(item,key) in responsed" v-if="key!='chart'">
                                <label for="" class="label-control">{{ item.title }} Id : {{ item.id }}</label>
                                <label for="" class="label-control">{{ item.title }} : {{ item.value  }} KM</label>
                            </div>
                            <div class="col-sm-12">
                                <canvas id="myChart" width="400" height="400"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Axios from 'axios';

    export default {
        mounted() {

        },
        data(){
            return {
                myChart: new Chart(),
                chart : [],
                responsed : [],
                item : {
                    start : "",
                    end : "",
                }
            }
        },
        methods:{
            get(){
                try{
                    axios.post('/api/stats',this.item)
                    .then(res => {
                        this.chart = res.data.chart,
                        this.responsed = res.data,
                        this.setChart()
                    });
                }catch(e){
                    console.log(e);
                }
            },
            setChart(){
                if (this.myChart) this.myChart.destroy();
                const ctx = document.getElementById('myChart').getContext('2d');
                this.myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        //labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
                        labels : this.chart.id,
                        datasets: [{
                            label: '# of Astroids',
                            data: this.chart.value,
                            //data: [12, 19, 3, 5, 2, 3],
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        }
    }
</script>
