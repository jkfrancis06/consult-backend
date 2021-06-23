window.onload = function () {

    var app = new Vue({
        el: '#app',
        delimiters: ['${', '}'],
        data: {
            isLoading: true
        },

        watch: {
        },

        mounted (){
            self = this
            axios.get('/xhr-admin-get-chart')
                .then(function (response){
                    console.log(response)
                    self.isLoading = false
                    self.createPieChart(response.data.categorie_chart,"cat")
                    self.createPieChart(response.data.source_chart,"source")
                    self.createLineChart(response.data.date_chart,response.data.labels_array)
                })
                .catch(function (error){
                    self.isLoading = false
                    console.log(error)
                })
        },

        beforeDestroy() {
            // remove pickadate according to its API
        },

        methods : {
            createPieChart(data,type){
                let labels = []
                let datas = []
                let colors = []

                if (type === "cat"){
                    for (let i = 0; i < data.length; i++){
                        labels.push(data[i].nom);
                        datas.push(data[i].recueils_count)
                        colors.push(data[i].color)
                    }
                }

                if (type === "source"){
                    for (let i = 0; i < data.length; i++){
                        labels.push(data[i].libelle);
                        datas.push(data[i].recueils_count)
                        colors.push(data[i].color)
                    }
                }
                console.log(labels)
                console.log(datas)

                if (type === "cat"){
                    var chartDiv = $("#catChart");
                }
                if (type === "source"){
                    var chartDiv = $("#sourceChart");
                }
                var myChart = new Chart(chartDiv, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                data: datas,
                                backgroundColor: colors
                            }]
                    },
                    options: {
                        title: {
                            display: true,
                            text: 'Pie Chart'
                        },
                        responsive: true,
                        maintainAspectRatio: false,
                    }
                });
            },

            createLineChart(data,labels_array){

                let labels = labels_array
                let datas = []
                let datasets = []

                for (let i = 0; i < data.length; i++){
                    let data_item = {
                        label : '',
                        fill : false,
                        borderColor : '',
                        borderWidth : 1,
                        data : [],
                        tension: 0.5
                    }
                    data_item.label = data[i].nom_prenom
                    data_item.borderColor = data[i].color
                    data_item.data = data[i].datasets
                    datasets.push(data_item)
                }

                console.log(labels_array)

                var ctx = document.getElementById("dayChart").getContext('2d');

                var myChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: datasets
                    },
                    options: {
                        responsive: true, // Instruct chart js to respond nicely.
                        maintainAspectRatio: false, // Add to prevent default behaviour of full-width/height
                    }
                });
            }
        }
    })
}