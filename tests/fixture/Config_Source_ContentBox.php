<?php


$configArray = array(
		'styles' => '
			/* defines the content box itself */
			/* font family and font size must be defined here too! */
			body {
		        color: #000;
		        font-family: dejavusans;
		        font-size: 11pt;
				line-height: 120%;
				top:200mm;
				left:120mm;
				width:70mm;
				height:40mm;
			}
		    p {
		        color: #000;
		        font-family: dejavusans;
		        font-size: 11pt;
				line-height: 120%;
				margin:0 0 0.3em 0;
		    }
		    p.a {
				margin:0 0 1em 0;
		    }',
		'html' => '
			<p class=\"a\"><b>naw.info GmbH</b></p>
			<p>René Fritz</p>
			<p>Immengarten 16-18<br />
			30177 Hannover</p>
			<p>Telefon: +49 (0)511/62 62 93-12<br />
			Email: r.fritz@bitmotion.de</p>'
	);
