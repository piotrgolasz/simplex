<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Points testing</title>
		<script src="js/jquery.js"></script>
		<script src="js/CanvasXpress.min.js"></script>
		<script src="js/excanvas.js"></script>
		<script src="js/jquery.flot.js"></script>
		<script src="js/development-bundle/ui/jquery-ui-1.8.16.custom.js"></script>
		<link href="js/css/smoothness/jquery-ui-1.8.16.custom.css" rel="stylesheet"/>
		<style>
			#sliders{
				margin-top: 50px;
				margin-bottom: 50px;
			}
		</style>
		<script>
			var Slider = function(slider, div) {
				if (this.isArray(slider) && slider.length === 2) {
					this.slider = slider;
					this.div = div;
					this.sliderlength = this.slider.length;
					this.sliders = [];
					this.appender();
				} else if (this.isArray(slider) && slider.length > 2) {

				} else {
					console.log('Data not an array or incorrect data');
				}
			};
			Slider.prototype.getSlider = function() {
				return this.slider;
			};
			Slider.prototype.isArray = function(slider) {
				return Array.isArray(slider);
			};
			Slider.prototype.appender = function() {
				for (var i = 0; i < this.sliderlength; i++) {
					this.div.append('<label for="slider_' + i + '">x' + (i + 1) + '</label><div name="slider_' + i + '" id="slider_' + i + '"></div>');
					this.sliders[i] = $('#slider_' + i);
					this.sliders[i].slider({
						range: "max",
						min: 0,
						max: 10 * this.slider[i],
						value: this.slider[i],
						step: 1,
						slide: function(event, ui) {
							$("#amount").val(ui.value);
						}
					});
				}
			};
		</script>
		<script>
			$(document).ready(function() {
				var data = [2, [2, 6], "max 2x<sub>1<\/sub>+6x<sub>2<\/sub><br\/>2x<sub>1<\/sub>+5x<sub>2<\/sub>+1x<sub>3<\/sub>+0x<sub>4<\/sub>+0x<sub>5<\/sub><=30<br\/>2x<sub>1<\/sub>+3x<sub>2<\/sub>+0x<sub>3<\/sub>+1x<sub>4<\/sub>+0x<sub>5<\/sub><=26<br\/>0x<sub>1<\/sub>+3x<sub>2<\/sub>+0x<sub>3<\/sub>+0x<sub>4<\/sub>+1x<sub>5<\/sub><=15<br\/>x<sub>1<\/sub>&ge;0<br\/>x<sub>2<\/sub>&ge;0<br\/>x<sub>3<\/sub>&ge;0<br\/>x<sub>4<\/sub>&ge;0<br\/>x<sub>5<\/sub>&ge;0<br\/><br\/><table class=\"result\"><tbody><tr><th class=\"ui-state-default\">(0)<\/th><th class=\"ui-state-default\"><\/th><th class=\"ui-state-default\">-2<\/th><th class=\"ui-state-default\">-6<\/th><th class=\"ui-state-default\">0<\/th><th class=\"ui-state-default\">0<\/th><th class=\"ui-state-default\">0<\/th><th class=\"ui-state-default\" rowspan=\"2\">P<sub>o<\/sub><\/th><th class=\"ui-state-default\" rowspan=\"2\">P<sub>o<\/sub>\/a<sub>ij<\/sub><\/th><\/tr><tr><th class=\"ui-state-default\">Baza<\/th><th class=\"ui-state-default\">c<\/th><th class=\"ui-state-default\">x<sub>1<\/sub><\/th><th class=\"ui-state-default\">x<sub>2<\/sub><\/th><th class=\"ui-state-default\">x<sub>3<\/sub><\/th><th class=\"ui-state-default\">x<sub>4<\/sub><\/th><th class=\"ui-state-default\">x<sub>5<\/sub><\/th><\/tr><tr><th class=\"ui-state-default\">x<sub>3<\/sub><\/th><td class=\"center\">0<\/td><td>2<\/td><td>5<\/td><td>1<\/td><td>0<\/td><td>0<\/td><td>30<\/td><td data-dane=\"dc,30,5,6\">6<\/td><\/tr><tr><th class=\"ui-state-default\">x<sub>4<\/sub><\/th><td class=\"center\">0<\/td><td>2<\/td><td>3<\/td><td>0<\/td><td>1<\/td><td>0<\/td><td>26<\/td><td data-dane=\"dc,26,3,26\/3\">26\/3<\/td><\/tr><tr><th class=\"ui-state-default\">x<sub>5<\/sub><\/th><td class=\"center\">0<\/td><td>0<\/td><td class=\"mainelement\">3<\/td><td>0<\/td><td>0<\/td><td>1<\/td><td>15<\/td><td data-dane=\"dc,15,3,5\">5<\/td><\/tr><tr><th class=\"ui-state-default\">z<sub>j<\/sub>-c<sub>j<\/sub><\/th><th class=\"ui-state-default\"><\/th><td>-2<\/td><td>-6<\/td><td>0<\/td><td>0<\/td><td>0<\/td><td>0<\/td><td class=\"ui-state-default\"><\/td><\/tr><\/tbody><\/table>A<sub>1<\/sub>=[0,0,30,26,15]<br\/><table class=\"result\"><tbody><tr><th class=\"ui-state-default\">(1)<\/th><th class=\"ui-state-default\"><\/th><th class=\"ui-state-default\">-2<\/th><th class=\"ui-state-default\">-6<\/th><th class=\"ui-state-default\">0<\/th><th class=\"ui-state-default\">0<\/th><th class=\"ui-state-default\">0<\/th><th class=\"ui-state-default\" rowspan=\"2\">P<sub>o<\/sub><\/th><th class=\"ui-state-default\" rowspan=\"2\">P<sub>o<\/sub>\/a<sub>ij<\/sub><\/th><\/tr><tr><th class=\"ui-state-default\">Baza<\/th><th class=\"ui-state-default\">c<\/th><th class=\"ui-state-default\">x<sub>1<\/sub><\/th><th class=\"ui-state-default\">x<sub>5<\/sub><\/th><th class=\"ui-state-default\">x<sub>3<\/sub><\/th><th class=\"ui-state-default\">x<sub>4<\/sub><\/th><th class=\"ui-state-default\">x<sub>5<\/sub><\/th><\/tr><tr><th class=\"ui-state-default\">x<sub>3<\/sub><\/th><td class=\"center\">0<\/td><td class=\"mainelement\" data-dane=\"g,2,5,0,3\">2<\/td><td data-dane=\"c,5,3\">0<\/td><td data-dane=\"g,1,5,0,3\">1<\/td><td data-dane=\"g,0,5,0,3\">0<\/td><td data-dane=\"g,0,5,1,3\">-5\/3<\/td><td data-dane=\"g,30,5,15,3\">5<\/td><td data-dane=\"dc,5,2,5\/2\">5\/2<\/td><\/tr><tr><th class=\"ui-state-default\">x<sub>4<\/sub><\/th><td class=\"center\">0<\/td><td data-dane=\"g,2,3,0,3\">2<\/td><td data-dane=\"c,3,3\">0<\/td><td data-dane=\"g,0,3,0,3\">0<\/td><td data-dane=\"g,1,3,0,3\">1<\/td><td data-dane=\"g,0,3,1,3\">-1<\/td><td data-dane=\"g,26,3,15,3\">11<\/td><td data-dane=\"dc,11,2,11\/2\">11\/2<\/td><\/tr><tr><th class=\"ui-state-default\">x<sub>2<\/sub><\/th><td class=\"center\">6<\/td><td data-dane=\"r,0,3\">0<\/td><td data-dane=\"m,1,3\">1<\/td><td data-dane=\"r,0,3\">0<\/td><td data-dane=\"r,0,3\">0<\/td><td data-dane=\"r,1,3\">1\/3<\/td><td data-dane=\"r,15,3\">5<\/td><td data-dane=\"dc,-,-,-\">-<\/td><\/tr><tr><th class=\"ui-state-default\">z<sub>j<\/sub>-c<sub>j<\/sub><\/th><th class=\"ui-state-default\"><\/th><td data-dane=\"g,-2,-6,0,3\">-2<\/td><td data-dane=\"c,-6,3\">0<\/td><td data-dane=\"g,0,-6,0,3\">0<\/td><td data-dane=\"g,0,-6,0,3\">0<\/td><td data-dane=\"g,0,-6,1,3\">2<\/td><td data-dane=\"g,0,-6,15,3\">30<\/td><td class=\"ui-state-default\"><\/td><\/tr><\/tbody><\/table>A<sub>2<\/sub>=[0,5,5,11,0]<br\/><table class=\"result\"><tbody><tr><th class=\"ui-state-default\">(2)<\/th><th class=\"ui-state-default\"><\/th><th class=\"ui-state-default\">-2<\/th><th class=\"ui-state-default\">-6<\/th><th class=\"ui-state-default\">0<\/th><th class=\"ui-state-default\">0<\/th><th class=\"ui-state-default\">0<\/th><th class=\"ui-state-default\" rowspan=\"2\">P<sub>o<\/sub><\/th><th class=\"ui-state-default\" rowspan=\"2\">P<sub>o<\/sub>\/a<sub>ij<\/sub><\/th><\/tr><tr><th class=\"ui-state-default\">Baza<\/th><th class=\"ui-state-default\">c<\/th><th class=\"ui-state-default\">x<sub>3<\/sub><\/th><th class=\"ui-state-default\">x<sub>5<\/sub><\/th><th class=\"ui-state-default\">x<sub>3<\/sub><\/th><th class=\"ui-state-default\">x<sub>4<\/sub><\/th><th class=\"ui-state-default\">x<sub>5<\/sub><\/th><\/tr><tr><th class=\"ui-state-default\">x<sub>1<\/sub><\/th><td class=\"center\">2<\/td><td data-dane=\"m,1,2\">1<\/td><td data-dane=\"r,0,2\">0<\/td><td data-dane=\"r,1,2\">1\/2<\/td><td data-dane=\"r,0,2\">0<\/td><td data-dane=\"r,-5\/3,2\">-5\/6<\/td><td data-dane=\"r,5,2\">5\/2<\/td><td data-dane=\"dc,-,-,-\">-<\/td><\/tr><tr><th class=\"ui-state-default\">x<sub>4<\/sub><\/th><td class=\"center\">0<\/td><td data-dane=\"c,2,2\">0<\/td><td data-dane=\"g,0,2,0,2\">0<\/td><td data-dane=\"g,0,2,1,2\">-1<\/td><td data-dane=\"g,1,2,0,2\">1<\/td><td data-dane=\"g,-1,2,-5\/3,2\">2\/3<\/td><td data-dane=\"g,11,2,5,2\">6<\/td><td data-dane=\"dc,-,-,-\">-<\/td><\/tr><tr><th class=\"ui-state-default\">x<sub>2<\/sub><\/th><td class=\"center\">6<\/td><td data-dane=\"c,0,2\">0<\/td><td data-dane=\"g,1,0,0,2\">1<\/td><td data-dane=\"g,0,0,1,2\">0<\/td><td data-dane=\"g,0,0,0,2\">0<\/td><td data-dane=\"g,1\/3,0,-5\/3,2\">1\/3<\/td><td data-dane=\"g,5,0,5,2\">5<\/td><td data-dane=\"dc,-,-,-\">-<\/td><\/tr><tr><th class=\"ui-state-default\">z<sub>j<\/sub>-c<sub>j<\/sub><\/th><th class=\"ui-state-default\"><\/th><td data-dane=\"c,-2,2\">0<\/td><td data-dane=\"g,0,-2,0,2\">0<\/td><td data-dane=\"g,0,-2,1,2\">1<\/td><td data-dane=\"g,0,-2,0,2\">0<\/td><td data-dane=\"g,2,-2,-5\/3,2\">1\/3<\/td><td data-dane=\"g,30,-2,5,2\">35<\/td><td class=\"ui-state-default\"><\/td><\/tr><\/tbody><\/table>A<sub>3<\/sub>=[2.5,5,0,6,0]<br\/>x<sub>1<\/sub>=5\/2 (2.5)<br\/>x<sub>2<\/sub>=5<br\/>x<sub>3<\/sub>=0<br\/>x<sub>4<\/sub>=6<br\/>x<sub>5<\/sub>=0<br\/>W=35", [{"label": "S1", "data": [[0, 6], [15, 0]]}, {"label": "S2", "data": [[0, 8.6666666666667], [13, 0]]}, {"label": "S3", "data": [[0, 5], [15, 5]]}, {"label": "gradient", "data": [[0, 0], [3.75, 11.25]]}, {"label": "A1", "data": [[0, 0]], "points": {"show": true}}, {"label": "A2", "data": [[0, 5]], "points": {"show": true}}, {"label": "A3", "data": [[2.5, 5]], "points": {"show": true}}], [[0, 0, 0], [0, 0.43, 2.6], [0, 0.87, 5.2], [0, 1.3, 7.8], [0, 1.73, 10.4], [0, 2.17, 13], [0, 2.6, 15.6], [0, 3.03, 18.2], [0, 3.47, 20.8], [0, 3.9, 23.4], [0, 4.33, 26], [0, 4.77, 28.6], [0.75, 0, 1.5], [0.75, 0.43, 4.1], [0.75, 0.87, 6.7], [0.75, 1.3, 9.3], [0.75, 1.73, 11.9], [0.75, 2.17, 14.5], [0.75, 2.6, 17.1], [0.75, 3.03, 19.7], [0.75, 3.47, 22.3], [0.75, 3.9, 24.9], [0.75, 4.33, 27.5], [0.75, 4.77, 30.1], [1.5, 0, 3], [1.5, 0.43, 5.6], [1.5, 0.87, 8.2], [1.5, 1.3, 10.8], [1.5, 1.73, 13.4], [1.5, 2.17, 16], [1.5, 2.6, 18.6], [1.5, 3.03, 21.2], [1.5, 3.47, 23.8], [1.5, 3.9, 26.4], [1.5, 4.33, 29], [1.5, 4.77, 31.6], [2.25, 0, 4.5], [2.25, 0.43, 7.1], [2.25, 0.87, 9.7], [2.25, 1.3, 12.3], [2.25, 1.73, 14.9], [2.25, 2.17, 17.5], [2.25, 2.6, 20.1], [2.25, 3.03, 22.7], [2.25, 3.47, 25.3], [2.25, 3.9, 27.9], [2.25, 4.33, 30.5], [2.25, 4.77, 33.1], [3, 0, 6], [3, 0.43, 8.6], [3, 0.87, 11.2], [3, 1.3, 13.8], [3, 1.73, 16.4], [3, 2.17, 19], [3, 2.6, 21.6], [3, 3.03, 24.2], [3, 3.47, 26.8], [3, 3.9, 29.4], [3, 4.33, 32], [3, 4.77, 34.6], [3.75, 0, 7.5], [3.75, 0.43, 10.1], [3.75, 0.87, 12.7], [3.75, 1.3, 15.3], [3.75, 1.73, 17.9], [3.75, 2.17, 20.5], [3.75, 2.6, 23.1], [3.75, 3.03, 25.7], [3.75, 3.47, 28.3], [3.75, 3.9, 30.9], [3.75, 4.33, 33.5], [4.5, 0, 9], [4.5, 0.43, 11.6], [4.5, 0.87, 14.2], [4.5, 1.3, 16.8], [4.5, 1.73, 19.4], [4.5, 2.17, 22], [4.5, 2.6, 24.6], [4.5, 3.03, 27.2], [4.5, 3.47, 29.8], [4.5, 3.9, 32.4], [5.25, 0, 10.5], [5.25, 0.43, 13.1], [5.25, 0.87, 15.7], [5.25, 1.3, 18.3], [5.25, 1.73, 20.9], [5.25, 2.17, 23.5], [5.25, 2.6, 26.1], [5.25, 3.03, 28.7], [5.25, 3.47, 31.3], [5.25, 3.9, 33.9], [6, 0, 12], [6, 0.43, 14.6], [6, 0.87, 17.2], [6, 1.3, 19.8], [6, 1.73, 22.4], [6, 2.17, 25], [6, 2.6, 27.6], [6, 3.03, 30.2], [6, 3.47, 32.8], [6.75, 0, 13.5], [6.75, 0.43, 16.1], [6.75, 0.87, 18.7], [6.75, 1.3, 21.3], [6.75, 1.73, 23.9], [6.75, 2.17, 26.5], [6.75, 2.6, 29.1], [6.75, 3.03, 31.7], [7.5, 0, 15], [7.5, 0.43, 17.6], [7.5, 0.87, 20.2], [7.5, 1.3, 22.8], [7.5, 1.73, 25.4], [7.5, 2.17, 28], [7.5, 2.6, 30.6], [8.25, 0, 16.5], [8.25, 0.43, 19.1], [8.25, 0.87, 21.7], [8.25, 1.3, 24.3], [8.25, 1.73, 26.9], [8.25, 2.17, 29.5], [8.25, 2.6, 32.1], [9, 0, 18], [9, 0.43, 20.6], [9, 0.87, 23.2], [9, 1.3, 25.8], [9, 1.73, 28.4], [9, 2.17, 31], [9.75, 0, 19.5], [9.75, 0.43, 22.1], [9.75, 0.87, 24.7], [9.75, 1.3, 27.3], [9.75, 1.73, 29.9], [10.5, 0, 21], [10.5, 0.43, 23.6], [10.5, 0.87, 26.2], [10.5, 1.3, 28.8], [11.25, 0, 22.5], [11.25, 0.43, 25.1], [11.25, 0.87, 27.7], [12, 0, 24], [12, 0.43, 26.6], [12.75, 0, 25.5], [0, 0, 0], [0, 5, 30], [2.5, 5, 35]]];
				$('#leftdiv').append(data[2]);

				var slider1 = new Slider(data[1], $('#sliders'));
				$.plot($("#placeholder1"), data[3]);


				var vars = [];
				for (var i = 0; i < data[4].length; i++) {
					vars.push("Punkt" + (i + 1));
				}
				var x = {
					"y": {
						"vars": vars,
						"smps": [
							"X",
							"Y",
							"Z"
						],
						"desc": [
							"Simplex method"
						],
						"data": data[4]
					}
				};
				var cx = new CanvasXpress("canvas1", x, {
					graphType: "Scatter3D",
					useFlashIE: true,
					xAxis: [
						"X"
					],
					yAxis: [
						"Y"
					],
					zAxis: [
						"Z"
					],
					scatterType: false,
					setMinX: 0,
					setMinY: 0,
					setMinZ: 0
				});
			});
		</script>
    </head>
    <body>
		<div id="leftdiv" style="float: left;width: 50%;"></div>
		<div id="rightdiv" style="float:right;width: 50%;">
			<input type="text" id="amount" style="border:0; color:#f6931f; font-weight:bold;">
			<div id="placeholder1" style="width: 480px; height: 360px;"></div>
			<div id="sliders" style="margin-right: 30px;"></div>
			<canvas id="canvas1" width="613" height="500"></canvas>
		</div>
	</body>
</html>
