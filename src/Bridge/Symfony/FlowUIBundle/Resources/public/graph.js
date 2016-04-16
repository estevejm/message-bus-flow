(function (d3) {

    d3.json(flow.url.graph, function(error, graphs) {

        if (error) throw "Error rendering graph: " + error;

        createGraphs(graphs);

        if (typeof flow.component.search !== 'undefined') {
            flow.component.search.init(graphs);
        }
    });

    function createGraphs(graphs) {
        for (id in graphs) {
            if (graphs.hasOwnProperty(id)) {
                var graph = graphs[id];
                createGraph(id, graph);
            }
        }
    }

    function createGraph(id, graph)
    {
        var svg = d3.select("body")
            .append("div")
            .attr("id",  "#graph-" + id)
            .attr("class", "graph-container well")
            .append("svg")
            .attr("viewBox", "0 0 " + width + " " + height )
            .attr("preserveAspectRatio", "xMidYMid meet");

        // arrow
        svg.append("svg:defs").selectAll("marker")
            .data(["end"])
            .enter().append("marker")
            .attr("id", String)
            .attr("viewBox", "0 -5 12 12")
            .attr("refX", 25)
            .attr("refY", -1.5)
            .attr("markerWidth", 7)
            .attr("markerHeight", 7)
            .attr("orient", "auto")
            .append("path")
            .attr("d", "M0,-5L10,0L0,5")
            .style("stroke", "#4679BD")
            .style("opacity", "0.6");

        var force = d3.layout.force()
            .charge(-1000)
            .linkDistance(100)
            .size([width, height]);

        force
            .nodes(graph.nodes)
            .links(graph.links)
            .start();

        var link = svg.selectAll(".link")
            .data(graph.links)
            .enter().append("line")
            .attr("class", "link")
            .attr("marker-end", "url(#end)");

        var node = svg.selectAll(".node")
            .data(graph.nodes)
            .enter().append("g")
            .attr("class", "node")
            .attr("id", function (d) { return d.id; })
            .call(force.drag)
            .on('dblclick', connectedNodes); // show neighboring nodes

        node.append("circle")
            .attr("r", 8)
            .style("fill", function (d) { return color(d.type); });

        node.append("text")
            .attr("dx", 10)
            .attr("dy", "0.35em")
            .text(function(d) { return d.id });

        force.on("tick", function() {
            link.attr("x1", function(d) { return d.source.x; })
                .attr("y1", function(d) { return d.source.y; })
                .attr("x2", function(d) { return d.target.x; })
                .attr("y2", function(d) { return d.target.y; });

            node.attr("cx", function(d) { return d.x; })
                .attr("cy", function(d) { return d.y; });

            d3.selectAll("circle")
                .attr("cx", function (d) { return d.x; })
                .attr("cy", function (d) { return d.y; });

            d3.selectAll("text")
                .attr("x", function (d) { return d.x; })
                .attr("y", function (d) { return d.y; });
        });

        // show neighboring nodes
        //Toggle stores whether the highlighting is on
        var toggle = 0;

        //Create an array logging what is connected to what
        var linkedByIndex = {};
        for (i = 0; i < graph.nodes.length; i++) {
            linkedByIndex[i + "," + i] = 1;
        }

        graph.links.forEach(function (d) {
            linkedByIndex[d.source.index + "," + d.target.index] = 1;
        });

        //This function looks up whether a pair are neighbours
        function neighboring(a, b) {
            return linkedByIndex[a.index + "," + b.index];
        }

        function connectedNodes() {

            if (toggle == 0) {
                //Reduce the opacity of all but the neighbouring nodes
                d = d3.select(this).node().__data__;
                node.style("opacity", function (o) {
                    return neighboring(d, o) | neighboring(o, d) ? 1 : 0.1;
                });

                link.style("opacity", function (o) {
                    return d.index==o.source.index | d.index==o.target.index ? 1 : 0.1;
                });

                //Reduce the op

                toggle = 1;
            } else {
                //Put them back to opacity=1
                node.style("opacity", 1);
                link.style("opacity", 1);
                toggle = 0;
            }

        }
    }
}(d3));