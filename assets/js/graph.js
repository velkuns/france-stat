import {Chart} from "chart.js/auto";

export class Graph {
    create(canvas, graphId, graphName, valueName) {
        fetch('api/unemployments/graphs/' + graphId)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                new Chart(canvas, this.getConfig(data.data, graphName, valueName));
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    getConfig(data, graphName, valueName) {
        return {
            type: 'line',
            data: data,
            options: {
                responsive: true,
                plugins: {title: {display: true, text: graphName}},
                interaction: {intersect: false},
                scales: {
                    x: {display: true, title: {display: true}},
                    y: {display: true, title: {display: true, text: valueName}}
                }
            },
        };
    }
}
