<?php
	header("Service-Worker-Allowed: /");
	header("Content-Type: application/javascript");
	header("X-Robots-Tag: none");

echo "importScripts('https://app.pushmagnet.com/sw.min.js');";